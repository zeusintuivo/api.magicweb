<?php

namespace App\Http\Controllers\Cab7;

use App\Exceptions\Cab7\TrialBalanceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cab7\BookingRequest;
use App\Http\Resources\Cab7\CashBookResource;
use App\Http\Resources\Cab7\DriverLogResource;
use App\Http\Resources\Cab7\LedgerBalanceResource;
use App\Http\Resources\Cab7\LedgerJournalResource;
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
        $debit = $journal->ledgerAccounts()->first();
        $journal->accounts = [
            'debit' => $debit->skr04Account,
            'credit' => $debit->skr04RefAccount
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
        return response()->json(LedgerJournalResource::collection($entries), 200);
    }

    public function fetchLedgerAccounts(BookingRequest $request)
    {
        $accounts = collect(DB::select("
            SELECT skr04_id, round(sum(debit) - sum(credit), 2) balance,
                skr04.de_DE, skr04.en_GB, skr04.pid, skr04.side, skr04.vat_code, skr04.private
            FROM cab7_ledger_journal journal, cab7_ledger_accounts account, cab7_skr04_accounts skr04
            WHERE journal.id = account.journal_id AND account.skr04_id = skr04.id
              AND journal.deleted_at IS NULL AND journal.date BETWEEN ? AND ?
            GROUP BY skr04.id
            ORDER BY skr04.id;
        ", [$request['begin'], $request['end']]));

        return response()->json(LedgerBalanceResource::collection($accounts), 200);
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
