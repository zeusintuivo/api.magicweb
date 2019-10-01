<?php

namespace App\Http\Controllers\Cab7;

use App\Http\Controllers\Controller;
use App\Models\Cab7\AccountChart;
use App\Models\Cab7\LedgerAccount;
use App\Models\Cab7\LedgerJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function date_parse;
use function response;

class BookingController extends Controller
{
    public function bookDoubleEntry(Request $request)
    {
        // return response()->json($request, 200);
        $v = $request->validate([
            'skr'    => 'required|string',
            'lang'   => 'required|string',
            'date'   => 'required|date',
            'amount' => 'required|string',
            'debit'  => 'required',
            'credit' => 'required',
        ]);
        $debitAccount = AccountChart::where($v['skr'], $v['debit']['value'])->where($v['lang'], $v['debit']['label'])->first();
        // return $debitAccount;
        $creditAccount = AccountChart::where($v['skr'], $v['credit']['value'])->where($v['lang'], $v['credit']['label'])->first();
        // return $creditAccount;
        $date = date_parse($v['date']);
        $billCount = LedgerJournal::whereDate('date', $v['date'])->get()->count() + 1;
        $billNumber = "{$date['month']}{$date['day']}{$billCount}";
        // return response()->json((int) $billNumber, 200);

        // Use transaction to ensure completeness
        $result = DB::connection('mysql-mweb')->transaction(function () use (
            $request, $v, $debitAccount, $creditAccount, $billNumber
        ) {
            $journalEntry = new LedgerJournal();
            $journalEntry->date = $v['date'];
            $journalEntry->bill = (int) $billNumber;
            $journalEntry->amount = (float) $v['amount'];
            $journalEntry->details = "{$v['credit']['label']} --> {$v['debit']['label']}";
            $journalEntry->save();
            // return $journalEntry;
            $debitEntry = new LedgerAccount();
            $debitEntry->user_id = $request->user()->id;
            $debitEntry->account_chart_id = $debitAccount->id;
            $debitEntry->ledger_journal_id = $journalEntry->id;
            $debitEntry->skr = $v['skr'];
            $debitEntry->lang = $v['lang'];
            $debitEntry->date = $v['date'];
            $debitEntry->refer = $v['credit']['label'];
            $debitEntry->debit = (float) $v['amount'];
            $debitEntry->save();
            // return $debitEntry;
            $creditEntry = new LedgerAccount();
            $creditEntry->user_id = $request->user()->id;
            $creditEntry->account_chart_id = $creditAccount->id;
            $creditEntry->ledger_journal_id = $journalEntry->id;
            $creditEntry->skr = $v['skr'];
            $creditEntry->lang = $v['lang'];
            $creditEntry->date = $v['date'];
            $creditEntry->refer = $v['debit']['label'];
            $creditEntry->credit = (float) $v['amount'];
            $creditEntry->save();
            // return $creditEntry;
            return [
                'success' => "3 entries booked successfully.",
                'journal' => $journalEntry,
                'debit'   => $debitEntry,
                'credit'  => $creditEntry,
            ];
        });

        return response()->json($result, 200);
    }

    public function fetchAccountCharts(Request $request)
    {
        $v = $request->validate([
            'skr'  => 'required|string|size:5',
            'lang' => 'required|string|size:5',
        ]);
        return response()->json(AccountChart::whereNotNull($v['skr'])->whereNotNull($v['lang'])->get([$v['skr'], $v['lang']]), 200);
    }
}
