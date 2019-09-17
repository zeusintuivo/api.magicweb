<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Exception;
use Illuminate\Http\Request;
use function explode;
use function response;
use function storage_path;
use function str_replace;

/**
 * Class FileController
 * @package App\Http\Controllers
 */
class FileController extends Controller
{
    public function readHaleGdpduExport(Request $request)
    {
        $filepath = storage_path('insika/GdpduExport_2018-02-16_2019-09-12/insikaTrips.csv');
        try {
            $handle = fopen($filepath, 'r');
            while ($line = fgets($handle)) {
                // Process line here
                $arr = explode(';', $line);
                $trip = [
                    'id'         => (int) $arr[0],
                    'user_id'    => 1,
                    'created_at' => date('Y-m-d H:i:s', strtotime($arr[1])),
                    'started_at' => date('Y-m-d H:i:s', strtotime($arr[2])),
                    'ended_at'   => date('Y-m-d H:i:s', strtotime($arr[3])),
                    'occupied_distance'   => (float) $arr[6],
                    // 'amount_vat19' => (float) $arr[7],
                    // 'amount_vat07' => (float) $arr[8],
                    'gross_fare'     => (float) $arr[7] ?: (float) $arr[8],
                    'vat'        => (int) $arr[7] ? 19 : 7,
                ];
                // return $trip;
                Trip::updateOrCreate($trip);
            }
        } catch (Exception $e) {
            return response()->json("Error occurred opening gdpdu export file: {$e->getMessage()}", 500);
        }
        $count = Trip::all()->count();
        return response()->json("Export file successfully read, $count trips inserted into table `trips`", 200);
    }
}
