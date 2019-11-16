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
use function array_search;
use function count;
use function date;
use function explode;
use function in_array;
use function number_format;
use function range;
use function strtotime;
use function trans;
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
                    'id'                   => 'nullable|integer',
                    'skr'                  => 'required|string|size:5',
                    'lang'                 => 'required|string|size:5',
                    'date'                 => 'required|date',
                    'amount'               => 'required|string',
                    'debit.value'          => 'required|integer',
                    'credit.value'         => 'required|integer',
                    'details.label'        => 'required|string|max:255',
                    'original_bill_number' => 'nullable|string',
                ];
            case 'delete/double/entry':
                return [
                    'id' => 'required|integer|gt:0',
                ];
            case 'rebook/money/transit':
                return [
                    'month' => 'required|string|size:7'
                ];
            case 'number/group/entries':
                return [
                    'query' => 'required|string|min:5',
                    'month' => 'required|string|size:7'
                ];
            case 'rebook/journal/entries':
                return [];
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
        $id = (int) @$v['id'];
        $lang = (string) $v['lang'];
        $date = (string) $v['date'];
        $amount = (float) str_replace(['-', '+'], [], $v['amount']);
        $debitAccount = Skr04Account::find($v['debit']['value']);
        $creditAccount = Skr04Account::find($v['credit']['value']);
        $vatCode = $debitAccount->vat_code ?: $creditAccount->vat_code;
        $vatDividers = [0 => 1.00, 8 => 1.07, 9 => 1.19];
        $debitNetAmount = round($amount / $vatDividers[$debitAccount->vat_code], 2);
        $debitNetAmountStr = number_format($debitNetAmount, 2, ',', '.');
        $debitVatAmount = $amount - $debitNetAmount;
        $debitVatAmountStr = number_format($debitVatAmount, 2, ',', '.');
        $creditNetAmount = round($amount / $vatDividers[$creditAccount->vat_code], 2);
        $creditNetAmountStr = number_format($creditNetAmount, 2, ',', '.');
        $creditVatAmount = $amount - $creditNetAmount;
        $creditVatAmountStr = number_format($creditVatAmount, 2, ',', '.');
        $debitDetails = $debitVatAmount ? "[EUR {$debitNetAmountStr}], {$debitAccount->pid} [EUR {$debitVatAmountStr}]" : " [EUR {$debitNetAmountStr}]";
        $creditDetails = $creditVatAmount ? "[EUR {$creditNetAmountStr}], {$creditAccount->pid} [EUR {$creditVatAmountStr}]" : " [EUR {$creditNetAmountStr}]";
        $systemDetails = "{$debitAccount->id} {$debitAccount->$lang} {$debitDetails} <-- {$creditAccount->id} {$creditAccount->$lang} {$creditDetails}";
        // dd($systemDetails);
        $internalBillNumber = $this->num($debitAccount, $creditAccount, $date, $id);// keep here
        // dd($internalBillNumber);

        // Use transaction to ensure completeness
        return DB::transaction(function () use (
            $v, $id, $date, $debitAccount, $creditAccount, $amount, $vatCode,
            $debitNetAmount, $debitVatAmount, $creditNetAmount, $creditVatAmount,
            $systemDetails, $internalBillNumber
        ) {
            // Destroy existing ledger journal entry [cascading]
            if ($journalEntryUpdate = LedgerJournal::find($id)) {
                $journalEntryUpdate->forceDelete();
            }
            // Ledger journal entry
            $journalEntry = new LedgerJournal();
            if ($id) $journalEntry->id = $id;
            $journalEntry->user_id = $this->user()->id;
            $journalEntry->date = $date;
            $journalEntry->amount = $this->swap($creditAccount->id) ? (-1 * $amount) : $amount;
            $journalEntry->vat_code = $vatCode;
            $journalEntry->direct_account = $this->swap($creditAccount->id) ? $creditAccount->id : $debitAccount->id;
            $journalEntry->offset_account = $this->swap($creditAccount->id) ? $debitAccount->id : $creditAccount->id;
            $journalEntry->client_details = $v['details']['label'];
            $journalEntry->system_details = $systemDetails;
            $journalEntry->internal_bill_number = $internalBillNumber;
            $journalEntry->original_bill_number = @$v['original_bill_number'];
            $journalEntry->save();
            // return $journalEntry;

            // Ledger account in debit
            $debitEntry = LedgerAccount::create([
                'journal_id'   => $journalEntry->id,
                'skr04_id'     => $debitAccount->id,
                'date'         => $date,
                'skr04_ref_id' => $creditAccount->id,
                'debit'        => $debitNetAmount,
            ]);
            // return $debitEntry;

            // Ledger account in debit with VAT
            if ($debitVatAmount) {
                $debitVatEntry = LedgerAccount::create([
                    'journal_id'   => $journalEntry->id,
                    'skr04_id'     => $debitAccount->pid,
                    'date'         => $date,
                    'skr04_ref_id' => $creditAccount->id,
                    'debit'        => $debitVatAmount,
                ]);
                // return $debitVatEntry;
            }

            // Ledger account in credit
            $creditEntry = LedgerAccount::create([
                'journal_id'   => $journalEntry->id,
                'skr04_id'     => $creditAccount->id,
                'date'         => $date,
                'skr04_ref_id' => $debitAccount->id,
                'credit'       => $creditNetAmount,
            ]);
            // return $creditEntry;

            // Ledger account in credit with VAT
            if ($creditVatAmount) {
                $creditVatEntry = LedgerAccount::create([
                    'journal_id'   => $journalEntry->id,
                    'skr04_id'     => $creditAccount->pid,
                    'date'         => $date,
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

    private function pad(string $input, int $padLength = 2, $padString = '00', string $padType = STR_PAD_LEFT)
    {
        return str_pad($input, $padLength, $padString, $padType);
    }

    private function num(Skr04Account $debit, Skr04Account $credit, string $date, int $id)
    {
        $month = date('Y-m', strtotime($date));
        $padString = '000000';// 6 numbers required

        // 1600 Cash account
        if ($debit->id === 1800 || $credit->id === 1800) {
            $collection = DB::select("
                SELECT month(date) month, GROUP_CONCAT(id ORDER BY date, id SEPARATOR ',') items FROM cab7_ledger_journal
                WHERE system_details LIKE '%1800 Bank%' AND date LIKE '{$month}%' AND deleted_at IS NULL
                GROUP BY month;
            ")[0];
            // dd($collection);
            $month = $collection->month;
            $ids = explode(',', $collection->items);
            $index = array_search((string) $id, $ids);
            // dd($collection, $index, $id);
            $number = ($index === false ? count($ids) : $index) + 1;
            return str_pad($number, 6, $padString, STR_PAD_LEFT);
        }

        return $padString;
    }

    /**
     * Swap amount and direct/offset accounts
     *
     * @param int $creditAccountId
     *
     * @return bool
     */
    private function swap(int $creditAccountId)
    {
        return in_array($creditAccountId, [1600, 1800, 2180, 7000]);
    }

    /**
     * DEAD: (Debit + Expenses + Assets + Drawings) = CLIC: (Credit + Liabilities + Income/sales/revenue + Capital)
     * SKR04 based
     *
     * @param Skr04Account $debit
     * @param Skr04Account $credit
     *
     * @return string
     */
    private function sign(Skr04Account $debit, Skr04Account $credit)
    {
        // if ($debit->side === 'dead' && $credit->side === 'dead') return '-';
        // if ($debit->side === 'dead' && $credit->side === 'clic') return '+';
        // if ($debit->side === 'clic' && $credit->side === 'dead') return '';
        // if ($debit->side === 'clic' && $credit->side === 'clic') return '-';

        // DEAD/CLIC accounts
        // if (in_array($debit, range(5000, 7999))) return '-';
        // if (in_array($credit, range(4000, 4999))) return '+';

        // SKR04 group of accounts
        // $capitalAssetsAccounts = range(0, 999);
        // $currentAssetsAccounts = range(1000, 1999);
        // $proprietaryCapitalAccounts = range(2000, 2999);
        // $outsideCapitalAccounts = range(3000, 3999);
        // $revenueIncomeAccounts = range(4000, 4999);
        // $operatingExpenditure = range(5000, 6999);
        // $otherRevenueAndExpenditure = range(7000, 7999);
        // $freeAvailableAccounts = range(8000, 8999);
        // $carryForwardAccounts = range(9000, 9999);

        return '';
    }

}
