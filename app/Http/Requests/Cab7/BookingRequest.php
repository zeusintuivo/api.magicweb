<?php

namespace App\Http\Requests\Cab7;

use App\Http\Resources\Cab7\LedgerJournalResource;
use App\Models\Cab7\LedgerAccount;
use App\Models\Cab7\LedgerJournal;
use App\Models\Cab7\Skr04Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use function number_format;
use function trans;

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
                    'id'                   => 'nullable|integer',
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
            case 'delete/double/entry':
                return [
                    'id' => 'required|integer|gt:0',
                ];
            default:
                return [
                    'file_name' => 'required|string',
                ];
        }
    }

    /**
     * Update or create double entry
     *
     * @param array $v
     *
     * @return mixed
     */
    public function bookDoubleEntry(array $v)
    {
        // Prepare amounts and strings for double entries
        $id = @$v['id'];
        $debitAccount = Skr04Account::find($v['debit']['value']);
        $creditAccount = Skr04Account::find($v['credit']['value']);
        $signedAmount = $this->sign($v['debit'], $v['credit']) . number_format($v['amount'], 2, ',', '.');
        $vatCode = $debitAccount->vat_code ?: $creditAccount->vat_code;
        $vatDividers = [0 => 1.00, 8 => 1.07, 9 => 1.19];
        $debitNetAmount = round($v['amount'] / $vatDividers[$debitAccount->vat_code], 2);
        $debitNetAmountStr = number_format($debitNetAmount, 2, ',', '.');
        $debitVatAmount = $v['amount'] - $debitNetAmount;
        $debitVatAmountStr = number_format($debitVatAmount, 2, ',', '.');
        $creditNetAmount = round($v['amount'] / $vatDividers[$creditAccount->vat_code], 2);
        $creditNetAmountStr = number_format($creditNetAmount, 2, ',', '.');
        $creditVatAmount = $v['amount'] - $creditNetAmount;
        $creditVatAmountStr = number_format($creditVatAmount, 2, ',', '.');
        $debitDetails = $debitVatAmount ? " [EUR {$debitNetAmountStr}], {$debitAccount->pid} [EUR {$debitVatAmountStr}]" : " [EUR {$debitNetAmountStr}]";
        $creditDetails = $creditVatAmount ? " [EUR {$creditNetAmountStr}], {$creditAccount->pid} [EUR {$creditVatAmountStr}]" : " [EUR {$creditNetAmountStr}]";
        $systemDetails = "{$v['debit']['label']}{$debitDetails} <-- {$v['credit']['label']}{$creditDetails}";
        // dd($systemDetails);

        // Use transaction to ensure completeness
        return DB::transaction(function () use (
            $v, $id, $debitAccount, $creditAccount, $signedAmount, $vatCode,
            $debitNetAmount, $debitVatAmount, $creditNetAmount, $creditVatAmount,
            $systemDetails
        ) {
            // Destroy existing ledger journal entry [cascading]
            if ($entryUpdate = LedgerJournal::find($id)) {
                $entryUpdate->forceDelete();
            }

            // Ledger journal entry
            $journalEntry = new LedgerJournal();
            if ($id) $journalEntry->id = $id;
            $journalEntry->user_id = $this->user()->id;
            $journalEntry->date = $v['date'];
            $journalEntry->amount = $signedAmount;
            $journalEntry->vat_code = $vatCode;
            $journalEntry->client_details = $v['details']['label'];
            $journalEntry->system_details = $systemDetails;
            $journalEntry->original_bill_number = @$v['original_bill_number'];
            $journalEntry->save();
            // return $journalEntry;

            // Ledger account in debit
            $debitEntry = LedgerAccount::create([
                'journal_id'   => $journalEntry->id,
                'skr04_id'     => $debitAccount->id,
                'date'         => $v['date'],
                'skr04_ref_id' => $creditAccount->id,
                'debit'        => $debitNetAmount,
            ]);
            // return $debitEntry;

            // Ledger account in debit with VAT
            if ($debitVatAmount) {
                $debitVatEntry = LedgerAccount::create([
                    'journal_id'   => $journalEntry->id,
                    'skr04_id'     => $debitAccount->pid,
                    'date'         => $v['date'],
                    'skr04_ref_id' => $creditAccount->id,
                    'debit'        => $debitVatAmount,
                ]);
                // return $debitVatEntry;
            }

            // Ledger account in credit
            $creditEntry = LedgerAccount::create([
                'journal_id'   => $journalEntry->id,
                'skr04_id'     => $creditAccount->id,
                'date'         => $v['date'],
                'skr04_ref_id' => $debitAccount->id,
                'credit'       => $creditNetAmount,
            ]);
            // return $creditEntry;

            // Ledger account in credit with VAT
            if ($creditVatAmount) {
                $creditVatEntry = LedgerAccount::create([
                    'journal_id'   => $journalEntry->id,
                    'skr04_id'     => $creditAccount->pid,
                    'date'         => $v['date'],
                    'skr04_ref_id' => $debitAccount->id,
                    'credit'       => $creditVatAmount,
                ]);
                // return $creditVatEntry;
            }
            // dd($debitNetAmount + $debitVatAmount, $creditNetAmount + $creditVatAmount);

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
                'in' => trans('cab7.validation.error.trial_balance', ['trialBalance' => number_format($trialBalance, 2, ',', '.')]),
            ])->validate();

            // Notify success, and pass all 4 entries to client
            return [
                'notify' => [
                    'success' => trans('cab7.booking.notify.success', ['number' => 'all']),
                ],
                'entry'  => new LedgerJournalResource($journalEntry),
            ];
        });
    }

    private function _pad(string $input, int $padLength = 2, $padString = '00', string $padType = STR_PAD_LEFT)
    {
        return str_pad($input, $padLength, $padString, $padType);
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
        $payments = [1600, 1800, 2180];
        if (in_array($debitValue, $payments) && in_array($creditValue, $payments)) return '';
        if (in_array($debitValue, $payments)) return '+';
        if (in_array($creditValue, $payments)) return '-';
        return '';
    }

}
