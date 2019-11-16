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
        $entries = LedgerJournal::with(['ledgerAccounts'])->orderBy('date')->orderBy('internal_bill_number')->get();
        return response()->json(LedgerJournalResource::collection($entries), 200);
    }

    public function fetchLedgerAccounts(Request $request)
    {
        $accounts = LedgerAccountView::all();
        return response()->json($accounts, 200);
    }

    public function filterLedgerAccountsDateRange(Request $request)
    {
        $validated = $request->validate([
            'begin' => 'required|date',
            'end'   => 'required|date',
        ]);

        $accounts = collect(DB::select("
            SELECT skr04_id, round(sum(debit) - sum(credit), 2) AS balance, pid, vat_code, private, de_DE, en_GB
            FROM cab7_ledger_accounts parent, cab7_skr04_accounts node
            WHERE parent.skr04_id = node.id AND date BETWEEN ? AND ?
            GROUP BY skr04_id
            ORDER BY skr04_id;
        ", [$validated['begin'], $validated['end']]));

        return response()->json(LedgerBalanceResource::collection($accounts), 200);
    }

    public function fetchCashBook(Request $request)
    {
        $rows = LedgerAccount::where('skr04_id', 1600)->with(['journal', 'skr04RefAccount'])->orderBy('date', 'desc')->get();
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
