<?php

namespace App\Http\Controllers;

use App\Models\AccountChart;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function bookDoubleEntry(Request $request)
    {
        return response()->json($request, 200);
    }

    public function fetchAccountCharts(Request $request)
    {
        $validated = $request->validate([
            'skr' => 'required|string|min:3|max:5'
        ]);
        return response()->json(AccountChart::all([$validated['skr'], 'name']), 200);
    }
}
