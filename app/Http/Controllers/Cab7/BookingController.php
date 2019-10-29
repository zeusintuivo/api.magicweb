<?php

namespace App\Http\Controllers\Cab7;

use App\Exceptions\Cab7\TrialBalanceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cab7\BookingRequest;
use App\Models\Cab7\InsikaShift;
use App\Models\Cab7\LedgerAccount;
use App\Models\Cab7\LedgerJournal;
use App\Models\Cab7\Skr04Account;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function collect;
use function compact;
use function date_create;
use function date_diff;
use function explode;
use function fclose;
use function in_array;
use function mb_convert_encoding;
use function number_format;
use function preg_filter;
use function rand;
use function sapi_windows_cp_is_utf8;
use function storage_path;
use function str_replace;
use function strtotime;
use function utf8_decode;
use function utf8_encode;

class BookingController extends Controller
{
    public function bookBank(BookingRequest $request)
    {
        return response()->json($request, 200);
    }

    public function bookBill(BookingRequest $request)
    {
        return response()->json($request->bookDoubleEntry($request->validated()), 200);
    }

    public function bookShift(BookingRequest $request)
    {
        $fpath = storage_path("insika/shifts/$request->file_name");
        $handle = fopen($fpath, 'r');
        for ($count = 0; $line = fgets($handle); $count++) {
            if ($count) {
                try {
                    $c = explode(';', $line);
                    $began = date_create($c[0]);
                    $ended = date_create($c[1]);
                    $diff = date_diff($began, $ended);
                    $hours = $diff->format('%h') + $diff->format('%d') * 24;
                    $minutes = $diff->format('%i');
                } catch (Exception $e) {
                    break;
                }

                $shift = InsikaShift::updateOrCreate([
                    'id' => explode('/', $c[12])[0] / 2,
                ], [
                    'user_id'      => $request->user()->id,
                    'began_at'     => $began->format('Y-m-d H:i:s'),
                    'ended_at'     => $ended->format('Y-m-d H:i:s'),
                    'length'       => "$hours:$minutes",
                    'driver'       => $c[2],
                    'vehicle'      => $c[3],
                    'charge_total' => (float) str_replace(',', '.', explode(' ', $c[4])[0]),
                    'charge_tarif' => (float) str_replace(',', '.', explode(' ', $c[5])[0]),
                    'charge_extra' => (float) str_replace(',', '.', explode(' ', $c[6])[0]),
                    'km_total'     => (float) str_replace(',', '.', explode(' ', $c[7])[0]),
                    'km_taken'     => (float) str_replace(',', '.', explode(' ', $c[8])[0]),
                    'km_empty'     => (float) str_replace(',', '.', explode(' ', $c[9])[0]),
                    'trip_count'   => (int) $c[10],
                ]);

                // Book shift to the ledger
                $amount = $shift->charge_total;
                $tip = $amount * rand(3, 5) / 100;
                // dd($amount, $tip, $amount + $tip);
                $debitAccount = Skr04Account::find(1600);
                $creditAccount = Skr04Account::find(4200);
                $request->bookDoubleEntry([
                    'skr'             => 'skr04',
                    'date'            => explode(' ', $shift->began_at)[0],
                    'amount'          => round($amount + $tip, 1),
                    'debit'           => [
                        'label' => $debitAccount->de_DE,
                        'value' => $debitAccount->id,
                    ],
                    'credit'          => [
                        'label' => $creditAccount->de_DE,
                        'value' => $creditAccount->id,
                    ],
                    'details'         => [
                        'label' => "Taxischicht #{$shift->id} Pflichtfahrgebiet",
                    ],
                    'bill_own_number' => $shift->id,
                ]);
            }
        }
        fclose($handle);

        $number = $count - 1;
        return response()->json("$number Shifts have been imported.", 200);
    }

    public function bookTrip(BookingRequest $request)
    {
        return response()->json($request, 200);
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
        return response()->json(LedgerJournal::orderBy('date', 'desc')->get(), 200);
    }

    public function fetchBookingDetails(Request $request)
    {
        return response()->json(LedgerJournal::distinct()->select('client_details')->get(), 200);
    }
}
