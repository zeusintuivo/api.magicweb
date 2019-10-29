<?php

namespace App\Http\Controllers\Cab7;

use App\Http\Controllers\Controller;
use App\Models\Cab7\InsikaTrip;
use Exception;
use Illuminate\Http\Request;

/**
 * Class FileController
 * @package App\Http\Controllers
 */
class FileController extends Controller
{
    public function readHaleGdpduExport(Request $request)
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
