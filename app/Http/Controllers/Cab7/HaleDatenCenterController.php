<?php

namespace App\Http\Controllers\Cab7;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cab7\BookingRequest;
use App\Models\Cab7\InsikaShift;
use App\Models\Cab7\InsikaTrip;
use App\Models\Cab7\Skr04Account;
use Exception;
use Illuminate\Http\Request;
use function date;
use function explode;
use function fclose;
use function fgets;
use function fopen;
use function response;
use function storage_path;
use function strtotime;

class HaleDatenCenterController extends Controller
{
    public function readShifts(BookingRequest $request)
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
                    $hours = str_pad((string) $diff->format('%h') + $diff->format('%d') * 24, 2, '0', STR_PAD_LEFT);
                    $minutes = $diff->format('%I');
                    $seconds = $diff->format('%S');
                } catch (Exception $e) {
                    break;
                }

                $shift = InsikaShift::updateOrCreate([
                    'id' => explode('/', $c[12])[0] / 2,
                ], [
                    'user_id'      => $request->user()->id,
                    'began_at'     => $began->format('Y-m-d H:i:s'),
                    'ended_at'     => $ended->format('Y-m-d H:i:s'),
                    'duration'     => "$hours:$minutes:$seconds",
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
                // return $shift;

                // Book shift to the ledger
                $debitAccount = Skr04Account::find(1600);
                $creditAccount = Skr04Account::find(4300);
                // Book shift revenue
                $request->bookDoubleEntry([
                    'id'              => $count,
                    'skr'             => 'skr04',
                    'date'            => explode(' ', $shift->began_at)[0],
                    'amount'          => $shift->charge_total,
                    'debit'           => [
                        'label' => $debitAccount->de_DE,
                        'value' => $debitAccount->id,
                    ],
                    'credit'          => [
                        'label' => $creditAccount->de_DE,
                        'value' => $creditAccount->id,
                    ],
                    'details'         => [
                        'label' => "Taxischicht im Pflichtfahrgebiet",
                    ],
                    'bill_own_number' => $shift->id,
                ]);
                // Book shift's tip
                $request->bookDoubleEntry([
                    'skr'     => 'skr04',
                    'date'    => explode(' ', $shift->began_at)[0],
                    'amount'  => round($shift->charge_total * (rand(2, 4) / 100), 1),
                    'debit'   => [
                        'label' => $debitAccount->de_DE,
                        'value' => $debitAccount->id,
                    ],
                    'credit'  => [
                        'label' => $creditAccount->de_DE,
                        'value' => $creditAccount->id,
                    ],
                    'details' => [
                        'label' => "Trinkgeld aus einer Taxischicht",
                    ],
                ]);
            }
        }
        fclose($handle);

        return response()->json(--$count . " Shifts have been imported. " . date('Y-m-d H:i:s'), 200);
    }

    public function readTrips(BookingRequest $request)
    {
        $fpath = storage_path("insika/trips/$request->file_name");
        $handle = fopen($fpath, 'r');
        for ($count = 0; $line = fgets($handle); $count++) {
            if ($count) {
                try {
                    $c = explode(';', $line);
                    $began = date_create($c[0]);
                    $ended = date_create($c[1]);
                    $diff = date_diff($began, $ended);
                    $hours = str_pad((string) $diff->format('%h') + $diff->format('%d') * 24, 2, '0', STR_PAD_LEFT);
                    $minutes = $diff->format('%I');
                    $seconds = $diff->format('%S');
                } catch (Exception $e) {
                    break;
                }

                $trip = InsikaTrip::updateOrCreate([
                    'id' => (int) preg_filter('/\D/', '', $c[9]),
                ], [
                    'user_id'  => $request->user()->id,
                    'began_at' => $began->format('Y-m-d H:i:s'),
                    'ended_at' => $ended->format('Y-m-d H:i:s'),
                    'duration' => "$hours:$minutes:$seconds",
                    'driver'   => $c[2],
                    'vehicle'  => $c[3],
                    'fare'     => (float) str_replace(',', '.', explode(' ', $c[4])[0]),
                    'vat'      => (int) explode(',', explode(' ', $c[5])[0])[0],
                    'km'       => (float) str_replace(',', '.', explode(' ', $c[7])[0]),
                ]);

                // Book trip to the ledger
                // $debitAccount = Skr04Account::find(null);
                // $creditAccount = Skr04Account::find(null);
                // $request->bookDoubleEntry([
                //     'skr'             => 'skr04',
                //     'date'            => explode(' ', $trip->began_at)[0],
                //     'amount'          => $c[4],
                //     'debit'           => [
                //         'label' => $debitAccount->de_DE,
                //         'value' => $debitAccount->id,
                //     ],
                //     'credit'          => [
                //         'label' => $creditAccount->de_DE,
                //         'value' => $creditAccount->id,
                //     ],
                //     'details'         => [
                //         'label' => "Taxifahrt #{$trip->id} Pflichtfahrgebiet",
                //     ],
                //     'bill_own_number' => $trip->id,
                // ]);
            }
        }
        fclose($handle);

        return response()->json(--$count . " Trips have been imported. " . date('Y-m-d H:i:s'), 200);
    }

    /**
     * Not in use
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function readGdpduExport(Request $request)
    {
        $v = $request->validate(['directory' => 'required|string|size:52']);
        $filepath = storage_path("insika/{$v['directory']}/insikaTrips.csv");
        try {
            $handle = fopen($filepath, 'r');
            while ($line = fgets($handle)) {
                // Process line here
                $arr = explode(';', $line);
                $trip = [
                    'id'                => (int) $arr[0],
                    'user_id'           => 1,
                    'created_at'        => date('Y-m-d H:i:s', strtotime($arr[1])),
                    'started_at'        => date('Y-m-d H:i:s', strtotime($arr[2])),
                    'ended_at'          => date('Y-m-d H:i:s', strtotime($arr[3])),
                    'occupied_distance' => (float) $arr[6],
                    // 'amount_vat19' => (float) $arr[7],
                    // 'amount_vat07' => (float) $arr[8],
                    'gross_fare'        => (float) $arr[7] ?: (float) $arr[8],
                    'vat'               => (int) $arr[7] ? 19 : 7,
                ];
                // return $trip;
                InsikaTrip::updateOrCreate($trip);
            }
            fclose($handle);
        } catch (Exception $e) {
            return response()->json("Error occurred opening gdpdu export file: {$e->getMessage()}", 500);
        }
        $count = InsikaTrip::all()->count();
        return response()->json("Export file successfully read, $count trips inserted into table `trips`", 200);
    }
}
