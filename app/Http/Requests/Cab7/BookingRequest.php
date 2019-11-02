<?php

namespace App\Http\Requests\Cab7;

use App\Models\Cab7\LedgerAccount;
use App\Models\Cab7\LedgerJournal;
use App\Models\Cab7\Skr04Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use function in_array;
use function number_format;
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
                    'skr'                  => 'required|string|size:5',
                    'lang'                 => 'required|string|size:5',
                    'date'                 => 'required|date',
                    'amount'               => 'required|string',
                    'debit.label'          => 'required|string',
                    'debit.value'          => 'required|integer',
                    'credit.label'         => 'required|string',
                    'credit.value'         => 'required|integer',
                    'details.label'        => 'required|string|max:255',
                    'original_bill_number' => 'nullable|string',
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
        $vatDividers = [0 => 1.00, 8 => 1.07, 9 => 1.19];
        // dd($vatCodes[9]);
        $debitNetAmount = round($v['amount'] / $vatDividers[$debitAccount->vat_code], 2);
        $debitNetAmountStr = number_format($debitNetAmount, 2, ',', '.');
        $debitVatAmount = $v['amount'] - $debitNetAmount;
        $debitVatAmountStr = number_format($debitVatAmount, 2, ',', '.');
        $creditNetAmount = round($v['amount'] / $vatDividers[$creditAccount->vat_code], 2);
        $creditNetAmountStr = number_format($creditNetAmount, 2, ',', '.');
        $creditVatAmount = $v['amount'] - $creditNetAmount;
        $creditVatAmountStr = number_format($creditVatAmount, 2, ',', '.');
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
            if (!$journalEntry = LedgerJournal::whereOriginalBillNumber(@$v['original_bill_number'])->first()) {
                $billCount = LedgerJournal::whereDate('date', $v['date'])->count() + 1;
                $internalBillNumber = "{$v['date']}-{$billCount}";
                $journalEntry = new LedgerJournal();
                if (@$v['id']) $journalEntry->id = (int) $v['id'];
                $journalEntry->original_bill_number = @$v['original_bill_number'] ?: $internalBillNumber;
                $journalEntry->internal_bill_number = $internalBillNumber;
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

    /**
     * DEAD: (Debit + Expenses + Assets + Drawings) = CLIC: (Credit + Liabilities + Income/sales/revenue + Capital)
     *
     * @param array $debit
     * @param array $credit
     *
     * @return string
     */
    private function sign(array $debit, array $credit)
    {
        $debitValue = (int) $debit['value'];
        $creditValue = (int) $credit['value'];
        $payments = [1600, 1800];
        if (in_array($debitValue, $payments)) return '+';
        if (in_array($creditValue, $payments)) return '-';
        return '';
    }

}
