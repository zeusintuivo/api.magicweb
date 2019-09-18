<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function bookDoubleEntry(Request $request)
    {
        return response()->json($request, 200);
    }
}
