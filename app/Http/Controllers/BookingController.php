<?php

namespace App\Http\Controllers;

use App\Model\LedgerAccount;
use App\Models\AccountChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function response;

class BookingController extends Controller
{
    public function bookDoubleEntry(Request $request)
    {
        // return response()->json($request, 200);
        $v = $request->validate([
            'skr'    => 'required|string',
            'lang'   => 'required|string',
            'date'   => 'required|string|size:10',
            'amount' => 'required|string',
            'debit'  => 'required',
            'credit' => 'required',
        ]);
        $debitAccount = AccountChart::where($v['skr'], $v['debit']['value'])->where($v['lang'], $v['debit']['label'])->first();
        // return $debitAccount;
        $creditAccount = AccountChart::where($v['skr'], $v['credit']['value'])->where($v['lang'], $v['credit']['label'])->first();
        // return $creditAccount;

        // Use transaction to ensure completeness
        $debitEntry = new LedgerAccount();
        $creditEntry = new LedgerAccount();
        DB::connection('mysql-cab7')->transaction(function () use (
            $request, $v, $debitAccount, $creditAccount, $debitEntry, $creditEntry
        ) {
            $debitEntry->user_id = $request->user()->id;
            $debitEntry->account_chart_id = $debitAccount->id;
            $debitEntry->skr = $v['skr'];
            $debitEntry->lang = $v['lang'];
            $debitEntry->date = $v['date'];
            $debitEntry->details = $v['credit']['label'];
            $debitEntry->debit = (float) $v['amount'];
            $debitEntry->save();
            // return $debitEntry;
            $creditEntry->user_id = $request->user()->id;
            $creditEntry->account_chart_id = $creditAccount->id;
            $creditEntry->skr = $v['skr'];
            $creditEntry->lang = $v['lang'];
            $creditEntry->date = $v['date'];
            $creditEntry->details = $v['debit']['label'];
            $creditEntry->credit = (float) $v['amount'];
            $creditEntry->save();
            // return $creditEntry;
        });

        return response()->json([
            'success' => "Two entries successfully booked.",
            'debit'   => $debitEntry,
            'credit'  => $creditEntry,
        ], 200);
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
