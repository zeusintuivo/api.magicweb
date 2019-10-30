<?php

namespace App\Http\Requests\Cab7;

use App\Models\Cab7\LedgerAccount;
use App\Models\Cab7\LedgerJournal;
use App\Models\Cab7\Skr04Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use const STR_PAD_LEFT;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        switch (Route::currentRouteName()) {
            case 'book/double/entry':
                return [
                    'skr'             => 'required|string|size:5',
                    'lang'            => 'required|string|size:5',
                    'date'            => 'required|date',
                    'amount'          => 'required|string',
                    'debit.label'     => 'required|string',
                    'debit.value'     => 'required|integer',
                    'credit.label'    => 'required|string',
                    'credit.value'    => 'required|integer',
                    'details.label'   => 'required|string|max:255',
                    'bill_own_number' => 'nullable|string',
                ];
            default:
                return ['file_name' => 'required|string'];
        }
    }

    public function bookDoubleEntry(array $v)
    {
        $debitAccount = Skr04Account::find($v['debit']['value']);
        // return $debitAccount;
        $creditAccount = Skr04Account::find($v['credit']['value']);
        // return $creditAccount;
        $signedAmount = $this->sign($v['debit'], $v['credit']) . number_format($v['amount'], 2, ',', '.');
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
        return DB::transaction(function () use (
            $v, $debitAccount, $creditAccount, $signedAmount, $vatCode,
            $debitNetAmount, $debitVatAmount, $creditNetAmount, $creditVatAmount,
            $systemDetails
        ) {
            if (!$journalEntry = LedgerJournal::whereBillOwnNumber(@$v['bill_own_number'])->first()) {
                $billCount = LedgerJournal::whereDate('date', $v['date'])->count() + 1;
                $sequenceNumber = "{$v['date']}-{$billCount}";
                $journalEntry = new LedgerJournal();
                if (@$v['id']) $journalEntry->id = (int) $v['id'];
                $journalEntry->bill_own_number = @$v['bill_own_number'] ?: $sequenceNumber;
                $journalEntry->sequence_number = $sequenceNumber;
                $journalEntry->user_id = $this->user()->id;
                $journalEntry->date = $v['date'];
                $journalEntry->amount = $signedAmount;
                $journalEntry->vat_code = $vatCode;
                $journalEntry->client_details = $v['details']['label'];
                $journalEntry->system_details = $systemDetails;
                $journalEntry->save();
            }
            // return $journalEntry;
            if (!$debitEntry = LedgerAccount::whereJournalId($journalEntry->id)->whereSkr04($debitAccount->id)->first()) {
                $debitEntry = LedgerAccount::create([
                    'journal_id' => $journalEntry->id,
                    'skr04'      => $debitAccount->id,
                    'date'       => $v['date'],
                    'skr04_ref'  => $creditAccount->id,
                    'debit'      => $debitNetAmount,
                ]);
            }
            // return $debitEntry;
            $debitVatEntry = null;
            if ($debitVatAmount) {
                if (!$debitVatEntry = LedgerAccount::whereJournalId($journalEntry->id)->whereSkr04($debitAccount->pid)->first()) {
                    $debitVatEntry = LedgerAccount::create([
                        'journal_id' => $journalEntry->id,
                        'skr04'      => $debitAccount->pid,
                        'date'       => $v['date'],
                        'skr04_ref'  => $creditAccount->id,
                        'debit'      => $debitVatAmount,
                    ]);
                }
            }
            // return $debitVatEntry;
            if (!$creditEntry = LedgerAccount::whereJournalId($journalEntry->id)->whereSkr04($creditAccount->id)->first()) {
                $creditEntry = LedgerAccount::create([
                    'journal_id' => $journalEntry->id,
                    'skr04'      => $creditAccount->id,
                    'date'       => $v['date'],
                    'skr04_ref'  => $debitAccount->id,
                    'credit'     => $creditNetAmount,
                ]);
            }
            // return $creditEntry;
            $creditVatEntry = null;
            if ($creditVatAmount) {
                if (!$creditVatEntry = LedgerAccount::whereJournalId($journalEntry->id)->whereSkr04($creditAccount->pid)->first()) {
                    $creditVatEntry = LedgerAccount::create([
                        'journal_id' => $journalEntry->id,
                        'skr04'      => $creditAccount->pid,
                        'date'       => $v['date'],
                        'skr04_ref'  => $debitAccount->id,
                        'credit'     => $creditVatAmount,
                    ]);
                }
            }
            // return $creditVatEntry;

            // Break execution if trial balance failes
            $trialBalance = collect(DB::select('
                SELECT @b := ROUND(@b + s.debit - s.credit, 2) AS balance
                FROM (SELECT @b := 0.00) AS excel, cab7_ledger_accounts AS s ORDER BY id;
            '))->last()->balance;
            // return $trialBalance;
            Validator::make([
                'trial_balance' => (int) $trialBalance,
            ], [
                'trial_balance' => 'in:0',
            ], [
                'in' => trans('cab7.validation.error.trial_balance', compact('trialBalance')),
            ])->validate();

            // Notify success, and pass all 4 entries to client
            return [
                'success'    => trans('cab7.booking.notify.success', ['number' => 'all']),
                'journal'    => $journalEntry,
                'debit'      => $debitEntry,
                'credit'     => $creditEntry,
                'debit_vat'  => $debitVatEntry,
                'credit_vat' => $creditVatEntry,
            ];
        });
    }

    private function sign(array $debit, array $credit)
    {
        // This cases are proofed
        $debitValue = (int) $debit['value'];
        if ((5000 <= $debitValue) && ($debitValue <= 6999)) return '-';
        if (in_array($debitValue, [2100])) return '-';

        // This logic is still under question?
        $debitGroup = (int) str_pad($debit['value'], 4, '0', STR_PAD_LEFT)[0];
        $creditGroup = (int) str_pad($credit['value'], 4, '0', STR_PAD_LEFT)[0];
        if ($debitGroup - $creditGroup < 0) return '+';
        if ($debitGroup - $creditGroup > 0) return '-';
        if ($debitGroup - $creditGroup == 0) return '';

        // No sign in common
        return '';
    }

}
