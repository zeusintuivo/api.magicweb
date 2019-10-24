<?php

namespace App\Http\Controllers\Cab7;

use App\Http\Controllers\Controller;
use App\Models\Cab7\Skr04Account;
use App\Models\Cab7\LedgerAccount;
use App\Models\Cab7\LedgerJournal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $debitAccount = Skr04Account::find($v['debit']['value']);
        // return $debitAccount;
        $creditAccount = Skr04Account::find($v['credit']['value']);
        // return $creditAccount;
        $date = date_parse($v['date']);
        $billCount = LedgerJournal::whereDate('date', $v['date'])->get()->count() + 1;
        $billNumber = "{$date['year']}-{$date['month']}-{$date['day']}-{$billCount}";
        // return response()->json($billNumber, 200);

        // Use transaction to ensure completeness
        $result = DB::transaction(function () use ($request, $v, $debitAccount, $creditAccount, $billNumber) {
            $journalEntry = new LedgerJournal();
            $journalEntry->date = $v['date'];
            $journalEntry->bill = $billNumber;
            $journalEntry->amount = (float) $v['amount'];
            $giver = "{$v['debit']['value']} {$v['debit']['label']}";
            $receiver = "{$v['credit']['value']} {$v['credit']['label']}";
            $journalEntry->details = "{$giver} --> {$receiver}";
            $journalEntry->save();
            // return $journalEntry;
            $debitEntry = new LedgerAccount();
            $debitEntry->user_id = $request->user()->id;
            $debitEntry->skr04 = $debitAccount->id;
            $debitEntry->journal_id = $journalEntry->id;
            $debitEntry->skr = $v['skr'];
            $debitEntry->lang = $v['lang'];
            $debitEntry->date = $v['date'];
            $debitEntry->refer = $v['credit']['label'];
            $debitEntry->debit = (float) $v['amount'];
            $debitEntry->save();
            // return $debitEntry;
            $creditEntry = new LedgerAccount();
            $creditEntry->user_id = $request->user()->id;
            $creditEntry->skr04 = $creditAccount->id;
            $creditEntry->journal_id = $journalEntry->id;
            $creditEntry->skr = $v['skr'];
            $creditEntry->lang = $v['lang'];
            $creditEntry->date = $v['date'];
            $creditEntry->refer = $v['debit']['label'];
            $creditEntry->credit = (float) $v['amount'];
            $creditEntry->save();
            // return $creditEntry;
            return [
                'success' => trans('cab7.booking.notify.success'),
                'journal' => $journalEntry,
                'debit'   => $debitEntry,
                'credit'  => $creditEntry,
            ];
        });

        return response()->json($result, 200);
    }

    public function fetchStandardAccounts(Request $request)
    {
        $v = $request->validate([
            'skr'  => 'required|string|size:5',
            'lang' => 'required|string|size:5',
        ]);
        return response()->json(Skr04Account::get(['id', $v['lang']]), 200);
    }

    public function fetchLedgerJournal(Request $request)
    {
        return response()->json(LedgerJournal::orderBy('id', 'desc')->get(), 200);
    }
}
