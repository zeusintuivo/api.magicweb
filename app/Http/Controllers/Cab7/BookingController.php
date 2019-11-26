<?php

namespace App\Http\Controllers\Cab7;

use App\Exceptions\Cab7\TrialBalanceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cab7\BookingRequest;
use App\Http\Resources\Cab7\CashBookResource;
use App\Http\Resources\Cab7\DriveLogResource;
use App\Http\Resources\Cab7\LedgerBalanceResource;
use App\Http\Resources\Cab7\NetIncomeResource;
use App\Models\Cab7\LedgerAccount;
use App\Models\Cab7\LedgerJournal;
use App\Models\Cab7\Skr04Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function collect;
use function implode;
use function response;

class BookingController extends Controller
{
    public function bookDoubleEntry(BookingRequest $request)
    {
        return response()->json($request->bookDoubleEntry($request->validated()), 200);
    }

    public function deleteDoubleEntry(BookingRequest $request)
    {
        return response()->json(LedgerJournal::destroy($request['id']), 200);
    }

    public function fetchLedgerJournalEntry(string $locale, LedgerJournal $journal)
    {
        $debitAccount = $journal->ledgerAccounts()->where('debit', '>', 0)->first()->skr04;
        $creditAccount = $journal->ledgerAccounts()->where('credit', '>', 0)->first()->skr04;
        $journal->accounts = [
            'debit' => $debitAccount,
            'credit' => $creditAccount
        ];
        return response()->json($journal, 200);
    }

    public function fetchLedgerJournal(Request $request)
    {
        $entries = LedgerJournal::with(['ledgerAccounts'])
            // ->onlyTrashed()
            ->where('date', 'like', "{$request->year}%")
            ->orderBy('date')
            ->orderBy('internal_bill_number')
            ->get();
        return response()->json(NetIncomeResource::collection($entries), 200);
    }

    public function fetchLedgerAccounts(BookingRequest $request)
    {
        $accounts = collect(DB::select("
            SELECT skr04_id, round(sum(debit) - sum(credit), 2) balance,
                skr04.de_DE, skr04.en_GB, skr04.pid, skr04.balance_side, skr04.vat_code, skr04.private
            FROM cab7_ledger_journal journal, cab7_ledger_accounts account, cab7_skr04_accounts skr04
            WHERE journal.id = account.journal_id AND account.skr04_id = skr04.id
              AND journal.deleted_at IS NULL AND journal.date BETWEEN ? AND ?
            GROUP BY skr04.id
            ORDER BY skr04.id;
        ", [$request['begin'], $request['end']]));

        return response()->json(LedgerBalanceResource::collection($accounts), 200);
    }

    public function fetchNetIncome(BookingRequest $request)
    {
        $dateLike = "{$request['year']}%";
        $directAccounts = '70001,70000,27810,26400,02180,01800,01600';
        $accounts = collect(DB::select("
            SELECT journal.id, journal.date,
                @dp := POSITION(LPAD(debit.skr04, 5, '0') IN direct.accounts) dp,
                @cp := POSITION(LPAD(credit.skr04, 5, '0') IN direct.accounts) cp,
                IF(@dp > @cp, credit.skr04, debit.skr04) offset_account,
                IF(@dp < @cp, credit.skr04, debit.skr04) direct_account,
                IF(@dp > @cp, +journal.amount, -journal.amount) amount,
                
                journal.vat_code, journal.internal_bill_number,
                journal.client_details, journal.system_details, journal.original_bill_number, journal.created_at, journal.updated_at
            FROM cab7_ledger_journal journal, (
                SELECT journal_id, MAX(s.id) skr04, MAX(s.surplus) surplus FROM cab7_ledger_accounts a, cab7_skr04_accounts s
                WHERE a.skr04_id = s.id AND a.debit > 0
                GROUP BY journal_id
            ) debit, (
                SELECT journal_id, MAX(s.id) skr04, MAX(s.surplus) surplus FROM cab7_ledger_accounts a, cab7_skr04_accounts s
                WHERE a.skr04_id = s.id AND a.credit > 0
                GROUP BY journal_id
            ) credit, (SELECT @accounts := '$directAccounts' accounts) direct
            WHERE journal.deleted_at IS NULL AND journal.date LIKE '$dateLike'
                AND journal.id = debit.journal_id AND journal.id = credit.journal_id
            ORDER BY journal.date, journal.id;
        "));
        return $accounts;
        return response()->json(NetIncomeResource::collection($accounts), 200);
    }

    public function fetchCashBook(BookingRequest $request)
    {
        $dateLike = "{$request['year']}%";
        $rows = collect(DB::select("
            SELECT a.id, j.internal_bill_number, j.date, (a.debit - a.credit) amount, j.vat_code,
                o.skr04_id ref_account, j.client_details, j.system_details, j.created_at
            FROM cab7_ledger_accounts a, cab7_ledger_journal j, (
                SELECT MAX(offset.skr04_id) skr04_id, offset.journal_id FROM cab7_ledger_accounts offset
                GROUP BY journal_id
            ) o
            WHERE a.journal_id = j.id AND j.deleted_at IS NULL
                AND o.journal_id = j.id AND o.skr04_id <> a.skr04_id
                AND a.skr04_id = 1600 AND j.date LIKE '$dateLike'
            ORDER BY j.date, j.id
        "));
        return response()->json(CashBookResource::collection($rows), 200);
    }

    public function fetchBankLog(BookingRequest $request)
    {
        $dateLike = "{$request['year']}%";
        $rows = collect(DB::select("
            SELECT a.id, j.internal_bill_number, j.date, (a.debit - a.credit) amount, j.vat_code,
                o.skr04_id ref_account, j.client_details, j.system_details, j.created_at
            FROM cab7_ledger_accounts a, cab7_ledger_journal j, (
                SELECT MAX(offset.skr04_id) skr04_id, offset.journal_id FROM cab7_ledger_accounts offset
                GROUP BY journal_id
            ) o
            WHERE a.journal_id = j.id AND j.deleted_at IS NULL
                AND o.journal_id = j.id AND o.skr04_id <> a.skr04_id
                AND a.skr04_id = 1800 AND j.date LIKE '$dateLike'
            ORDER BY j.date, j.id
        "));
        return response()->json(CashBookResource::collection($rows), 200);
    }

    public function fetchDriveLog(BookingRequest $request)
    {
        $shifts = collect(DB::select("
            SELECT * FROM (
                SELECT id, began_at, ended_at, duration, driver, vehicle, km_total, @c := ROUND(@c + km_total, 2) AS mileage, trip_count, created_at, updated_at, deleted_at
                FROM (SELECT @c := 0.00) AS excel, cab7_insika_shifts AS s WHERE began_at LIKE '{$request['year']}%'
            ) mileage
            ORDER BY began_at DESC;
        "));
        return response()->json(DriveLogResource::collection($shifts), 200);
    }

    public function fetchStandardAccounts(BookingRequest $request)
    {
        return response()->json(Skr04Account::wherePrivate(0)->get(['id', $request['lang'], 'vat_code']), 200);
    }

    public function fetchBookingDetails(Request $request)
    {
        return response()->json(LedgerJournal::distinct()->select('client_details')->get(), 200);
    }

}
