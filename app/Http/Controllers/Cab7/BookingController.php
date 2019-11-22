<?php

namespace App\Http\Controllers\Cab7;

use App\Exceptions\Cab7\TrialBalanceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cab7\BookingRequest;
use App\Http\Resources\Cab7\CashBookResource;
use App\Http\Resources\Cab7\DriverLogResource;
use App\Http\Resources\Cab7\LedgerBalanceResource;
use App\Http\Resources\Cab7\NetIncomeResource;
use App\Models\Cab7\LedgerAccount;
use App\Models\Cab7\LedgerAccountView;
use App\Models\Cab7\LedgerJournal;
use App\Models\Cab7\Skr04Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $debitAccount = $journal->ledgerAccounts()->whereCredit(0)->first()->skr04;
        $creditAccount = $journal->ledgerAccounts()->whereDebit(0)->first()->skr04;
        $journal->accounts = [
            'debit' => $debitAccount,
            'credit' => $debitAccount
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
        $accounts = collect(DB::select("
            SELECT j.id, j.internal_bill_number, j.date, IF(s.balance_side = 'dead', a.debit - a.credit, a.credit - a.debit) amount,
                j.vat_code, o.skr04_id offset_account, s.id direct_account, j.client_details, j.system_details, j.original_bill_number, j.created_at, j.updated_at
                FROM cab7_ledger_journal j, cab7_ledger_accounts a, cab7_skr04_accounts s, (
                    SELECT acc.journal_id, acc.skr04_id FROM mweb.cab7_ledger_journal jrn, cab7_skr04_accounts skr, cab7_ledger_accounts acc
                    WHERE jrn.id = acc.journal_id AND acc.skr04_id = skr.id AND jrn.deleted_at IS NULL
                ) o
            WHERE j.id = a.journal_id AND a.skr04_id = s.id AND j.deleted_at IS NULL AND s.id IN (1600, 1800)
                AND o.journal_id = j.id AND s.id <> o.skr04_id AND o.skr04_id NOT IN (1401, 1406, 3801, 3806)
                AND j.date LIKE '$dateLike'
            ORDER BY j.date, j.id;
        "));
        return response()->json(NetIncomeResource::collection($accounts), 200);
    }

    public function fetchCashBook(Request $request)
    {
        $rows = LedgerAccount::where('skr04_id', 1600)->with(['journal', 'skr04RefAccount'])->get();
        return response()->json(CashBookResource::collection($rows), 200);
    }

    public function fetchDriverLog(Request $request)
    {
        $shifts = collect(DB::select("
            SELECT * FROM (
                SELECT id, began_at, ended_at, driver, vehicle, km_total, @c := ROUND(@c + km_total, 2) AS mileage, trip_count, created_at, updated_at, deleted_at
                FROM (SELECT @c := 0.00) AS excel, cab7_insika_shifts AS s
            ) mileage
            ORDER BY began_at DESC;
        "));
        return response()->json(DriverLogResource::collection($shifts), 200);
    }

    public function fetchStandardAccounts(Request $request)
    {
        $v = $request->validate([
            'skr'  => 'required|string|size:5',
            'lang' => 'required|string|min:2|max:5',
        ]);
        return response()->json(Skr04Account::wherePrivate(0)->get(['id', $v['lang']]), 200);
    }

    public function fetchBookingDetails(Request $request)
    {
        return response()->json(LedgerJournal::distinct()->select('client_details')->get(), 200);
    }

}
