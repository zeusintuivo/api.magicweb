<?php

namespace App\Http\Controllers\Cab7;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cab7\BookingRequest;
use App\Models\Cab7\LedgerJournal;
use App\Models\Cab7\Skr04Account;
use function array_pop;
use function explode;

/**
 * Class RichardController
 * @package App\Http\Controllers\Cab7
 */
class RichardController extends Controller
{
    public function rebookJournalEntries(BookingRequest $request)
    {
        $data = [];
        $count = 0;
        try {
            $entries = LedgerJournal::where('client_details', 'like', 'taxiwäsche%')->orderBy('date')->get();
            foreach ($entries as $journal) {
                $debitAccount = $journal->ledgerAccounts()->whereJournalId($journal->id)->orderBy('id')->first();
                $debitValue = $debitAccount->skr04_id;
                $creditValue = $debitAccount->skr04_ref_id;

                $data = [
                    'id'                   => $journal->id,
                    'skr'                  => 'skr04',
                    'lang'                 => 'de_DE',
                    'date'                 => $journal->date,
                    'amount'               => $journal->amount,
                    'debit'                => ['value' => 6530],
                    'credit'               => ['value' => $creditValue],
                    'details'              => [
                        'label' => $journal->client_details,
                    ],
                    'original_bill_number' => $journal->original_bill_number,
                ];
                // dd($journal, $data);
                // if ($count > 500) break;
                $request->bookDoubleEntry($data);
                $count++;
            }
        } catch (Exception $e) {
            dd("Error occurred rebooking journal entries:", $e->getMessage(), $data);
        }
        return response()->json("All journal entries successfully rebooked, $count entries have been updated", 200);
    }

    public function updateAmountAfterCast(BookingRequest $request)
    {
        $filepath = storage_path($request->file_name);
        $data = [];
        $line = [];
        $count = 0;
        // return $filepath;
        try {
            $handle = fopen($filepath, 'r');
            while ($line = fgets($handle)) {
                if ($count) {
                    $arr = explode('","', $line);
                    $date = $arr[1];
                    $amount = preg_replace('/^[-+]?(\d*)[,.]?(\d{1,3})[.,](\d{1,2})$/', '$1$2.$3', $arr[2]);
                    $vatCode = $arr[3];
                    $debit = (int) $arr[4];
                    $credit = (int) $arr[5];
                    $clientDetails = $arr[6];
                    if ($debit === $credit) continue;
                    // dd($date, $amount, $vatCode, $debit, $credit, $clientDetails);

                    $journal = LedgerJournal::where('date', $date)->whereVatCode($vatCode)->where('client_details', 'like', "%$clientDetails")->first();
                    $data = [
                        'id'                   => $journal->id,
                        'skr'                  => 'skr04',
                        'lang'                 => 'de_DE',
                        'date'                 => $date,
                        'amount'               => $amount,
                        'debit'                => ['value' => $debit],
                        'credit'               => ['value' => $credit],
                        'details'              => ['label' => $clientDetails],
                        'original_bill_number' => $journal->original_bill_number,
                    ];
                    // return $data;
                    $updated = $request->bookDoubleEntry($data);
                    // return $updated;
                }
                // if ($count > 300) break;
                $count++;
            }
            fclose($handle);
        } catch (Exception $e) {
            dd("Error occurred opening csv file:", $e->getMessage(), $data, $line, $count);
        }
        return response()->json("CSV file successfully read, $count entries have been updated", 200);
    }

    public function numberGroupEntries(BookingRequest $request)
    {
        $entries = LedgerJournal::where('date', 'like', "{$request->month}%")->where('system_details', 'like', "%{$request['query']}%")->orderBy('date')->orderBy('id')->get();
        $entriesMapped = $entries->map(function (LedgerJournal $journal) use ($request) {

            $debitAccount = $journal->ledgerAccounts()->whereJournalId($journal->id)->orderBy('id')->first();
            $date = explode('-', $request->month);
            $month = array_pop($date);
            $details = explode(' ### ', $journal->client_details);
            $clientDetails = array_shift($details) . " ### source:DB18{$month}";

            $journal->skr = 'skr04';
            $journal->lang = 'de_DE';
            $journal->debit = ['value' => $debitAccount->skr04_id];
            $journal->credit = ['value' => $debitAccount->skr04_ref_id];
            $journal->details = ['label' => $clientDetails];
            $journal->internal_bill_number = null;

            return $journal;
        });
        // return $entriesMapped;

        foreach ($entriesMapped as $entry) {
            $request->bookDoubleEntry($entry->toArray());
        }

        return response()->json('End reached. Everything should be OK!', 200);
    }

    /**
     * Do NOT rerun it twice on the same month!
     *
     * @param \App\Http\Requests\Cab7\BookingRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function rebookMoneyTransit(BookingRequest $request)
    {
        $entries = LedgerJournal::where('date', 'like', "{$request->month}%")->where('client_details', 'einzahlungsbeleg')->orderBy('date')->get();
        // return $entries->count();

        $mapped1 = $entries->map(function (LedgerJournal $journal) {
            $debitAccount = Skr04Account::find(1460);
            $creditAccount = Skr04Account::find(1600);

            $journal->skr = 'skr04';
            $journal->lang = 'de_DE';
            $journal->debit = [
                'label' => "{$debitAccount->id} {$debitAccount->de_DE}",
                'value' => $debitAccount->id,
            ];
            $journal->credit = [
                'label' => "{$creditAccount->id} {$creditAccount->de_DE}",
                'value' => $creditAccount->id,
            ];
            $journal->details = [
                'label' => $journal->client_details,
            ];
            $journal->original_bill_number = null;// !!!

            return $journal;
        });
        // return $mapped1;
        foreach ($mapped1 as $entry) $request->bookDoubleEntry($entry->toArray());

        $mapped2 = $entries->map(function (LedgerJournal $journal) {
            $debitAccount = Skr04Account::find(1800);
            $creditAccount = Skr04Account::find(1460);

            $journal->id = null;
            $journal->skr = 'skr04';
            $journal->lang = 'de_DE';
            $journal->debit = [
                'label' => "{$debitAccount->id} {$debitAccount->de_DE}",
                'value' => $debitAccount->id,
            ];
            $journal->credit = [
                'label' => "{$creditAccount->id} {$creditAccount->de_DE}",
                'value' => $creditAccount->id,
            ];
            $journal->details = [
                'label' => $journal->client_details,
            ];
            $journal->original_bill_number = null;// !!!

            return $journal;
        });
        // return $mapped2;
        foreach ($mapped2 as $entry) $request->bookDoubleEntry($entry->toArray());

        return response()->json('End reached. Everything should be OK!', 200);
    }

}
