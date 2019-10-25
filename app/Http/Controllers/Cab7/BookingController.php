<?php

namespace App\Http\Controllers\Cab7;

use App\Http\Controllers\Controller;
use App\Models\Cab7\Skr04Account;
use App\Models\Cab7\LedgerAccount;
use App\Models\Cab7\LedgerJournal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function number_format;

class BookingController extends Controller
{
    public function bookDoubleEntry(Request $request)
    {
        // return response()->json($request, 200);
        $v = $request->validate([
            'skr'          => 'required|string|size:5',
            'lang'         => 'required|string|size:5',
            'date'         => 'required|date',
            'amount'       => 'required|string',
            'debit.label'  => 'required|string',
            'debit.value'  => 'required|integer',
            'credit.label' => 'required|string',
            'credit.value' => 'required|integer',
            'details'      => 'nullable|string|max:255',
        ]);

        $debitAccount = Skr04Account::find($v['debit']['value']);
        // return $debitAccount;
        $creditAccount = Skr04Account::find($v['credit']['value']);
        // return $creditAccount;
        $billCount = LedgerJournal::whereDate('date', $v['date'])->get()->count() + 1;
        $billNumber = "{$v['date']}-{$billCount}";
        // return $billCount;
        $debit = $v['debit']['value'];
        $out = 5000 <= $debit && $debit <= 7999;
        $signedAmount = ($out ? '-' : '+') . $v['amount'];
        // return $signedAmount;
        $vatCode = $debitAccount->vat_code ?: $creditAccount->vat_code;
        $vatCodes = [0 => 0.00, 8 => 0.07, 9 => 0.19];
        // dd($vatCodes[9]);
        $debitVatAmount = round($vatCodes[$debitAccount->vat_code] * $v['amount'], 2);
        $debitVatAmountStr = number_format($debitVatAmount, 2, ',', '.');
        $debitNetAmount = $v['amount'] - $debitVatAmount;
        $debitNetAmountStr = number_format($debitNetAmount, 2, ',', '.');
        $creditVatAmount = round($vatCodes[$creditAccount->vat_code] * $v['amount'], 2);
        $creditVatAmountStr = number_format($creditVatAmount, 2, ',', '.');
        $creditNetAmount = $v['amount'] - $creditVatAmount;
        $creditNetAmountStr = number_format($creditNetAmount, 2, ',', '.');
        // dd($debitVatAmount, $creditVatAmount, round(1.3554, 2));
        $debitDetails = $debitVatAmount ? " [EUR {$debitNetAmountStr}], {$debitAccount->pid} [EUR {$debitVatAmountStr}]" : " [EUR {$debitNetAmountStr}]";
        $creditDetails = $creditVatAmount ? " [EUR {$creditNetAmountStr}], {$creditAccount->pid} [EUR {$creditVatAmountStr}]" : " [EUR {$creditNetAmountStr}]";
        $systemDetails = "{$v['debit']['label']}{$debitDetails} <-- {$v['credit']['label']}{$creditDetails}";
        // return $systemDetails;

        // Use transaction to ensure completeness
        $result = DB::transaction(function () use (
            $request, $v, $debitAccount, $creditAccount, $billNumber,
            $signedAmount, $vatCode, $debitVatAmount, $creditVatAmount,
            $debitNetAmount, $creditNetAmount, $systemDetails
        ) {
            $journalEntry = new LedgerJournal();
            $journalEntry->date = $v['date'];
            $journalEntry->bill_number = $billNumber;
            $journalEntry->amount = $signedAmount;
            $journalEntry->vat_code = $vatCode;
            $journalEntry->client_details = $v['details'];
            $journalEntry->system_details = $systemDetails;
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
            $debitEntry->debit = $debitNetAmount;
            $debitEntry->save();
            // return $debitEntry;
            $debitVatEntry = new LedgerAccount();
            if ($debitVatAmount) {
                $debitVatEntry->user_id = $request->user()->id;
                $debitVatEntry->skr04 = $debitAccount->pid;
                $debitVatEntry->journal_id = $journalEntry->id;
                $debitVatEntry->skr = $v['skr'];
                $debitVatEntry->lang = $v['lang'];
                $debitVatEntry->date = $v['date'];
                $debitVatEntry->refer = $v['credit']['label'];
                $debitVatEntry->debit = $debitVatAmount;
                $debitVatEntry->save();
            }
            // return $debitVatEntry;
            $creditEntry = new LedgerAccount();
            $creditEntry->user_id = $request->user()->id;
            $creditEntry->skr04 = $creditAccount->id;
            $creditEntry->journal_id = $journalEntry->id;
            $creditEntry->skr = $v['skr'];
            $creditEntry->lang = $v['lang'];
            $creditEntry->date = $v['date'];
            $creditEntry->refer = $v['debit']['label'];
            $creditEntry->credit = $creditNetAmount;
            $creditEntry->save();
            // return $creditEntry;
            $creditVatEntry = new LedgerAccount();
            if ($creditVatAmount) {
                $creditVatEntry->user_id = $request->user()->id;
                $creditVatEntry->skr04 = $creditAccount->pid;
                $creditVatEntry->journal_id = $journalEntry->id;
                $creditVatEntry->skr = $v['skr'];
                $creditVatEntry->lang = $v['lang'];
                $creditVatEntry->date = $v['date'];
                $creditVatEntry->refer = $v['debit']['label'];
                $creditVatEntry->credit = $creditVatAmount;
                $creditVatEntry->save();
            }
            // return $creditVatEntry;
            return [
                'success'    => trans('cab7.booking.notify.success', ['number' => 'all']),
                'journal'    => $journalEntry,
                'debit'      => $debitEntry,
                'credit'     => $creditEntry,
                'debit_vat'  => $debitVatEntry,
                'credit_vat' => $creditVatEntry,
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
        return response()->json(Skr04Account::wherePrivate(0)->get(['id', $v['lang']]), 200);
    }

    public function fetchLedgerJournal(Request $request)
    {
        return response()->json(LedgerJournal::orderBy('id', 'desc')->get(), 200);
    }

    public function fetchBookingDetails(Request $request)
    {
        return response()->json(LedgerJournal::distinct()->select('client_details')->get(), 200);
    }
}
