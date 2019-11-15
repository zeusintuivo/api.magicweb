<?php

namespace App\Http\Controllers\Cab7;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cab7\BookingRequest;
use App\Models\Cab7\LedgerJournal;
use App\Models\Cab7\Skr04Account;
use Illuminate\Http\Request;
use function response;
use function str_replace;

/**
 * Class RichardController
 * @package App\Http\Controllers\Cab7
 */
class RichardController extends Controller
{
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

    public function numberCashbookEntries(BookingRequest $request)
    {
        $cashEntries = LedgerJournal::where('date', 'like', "{$request->month}%")->where('system_details', 'like', '%kasse%')->orderBy('date')->get();
        $cashEntriesMapped = $cashEntries->map(function (LedgerJournal $journal) {
            $reset = LedgerJournal::find($journal->id);
            $reset->internal_bill_number = 0;
            $reset->save();

            $debitAccount = $journal->ledgerAccounts()->with(['skr04Account'])->whereCredit('0.00')->first()->skr04Account;
            $creditAccount = $journal->ledgerAccounts()->with(['skr04Account'])->whereDebit('0.00')->first()->skr04Account;

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
            return $journal;
        });
        // return $cashEntriesMapped;

        foreach ($cashEntriesMapped as $entry) {
            $request->bookDoubleEntry($entry->toArray());
        }

        return response()->json('End reached. Everything should be OK!', 200);
    }
}
