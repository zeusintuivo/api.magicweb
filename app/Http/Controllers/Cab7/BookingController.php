<?php

namespace App\Http\Controllers\Cab7;

use App\Exceptions\Cab7\TrialBalanceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cab7\BookingRequest;
use App\Models\Cab7\LedgerAccountBalance;
use App\Models\Cab7\LedgerJournal;
use App\Models\Cab7\Skr04Account;
use Illuminate\Http\Request;
use function response;

class BookingController extends Controller
{
    public function fetchLedgerJournal(Request $request)
    {
        return response()->json(LedgerJournal::orderBy('id', 'desc')->get(), 200);
    }

    public function fetchLedgerAccounts(Request $request)
    {
        $v = $request->validate([
            'skr'  => 'required|string|size:5',
            'lang' => 'required|string|size:5',
        ]);
        $accounts = LedgerAccountBalance::all();
        return response()->json($accounts, 200);
    }

    public function fetchCashBook(Request $request)
    {
        return response()->json($request, 200);
    }

    public function fetchDriverLog(Request $request)
    {
        return response()->json($request, 200);
    }

    public function bookDoubleEntry(BookingRequest $request)
    {
        return response()->json($request->bookDoubleEntry($request->validated()), 200);
    }

    public function fetchStandardAccounts(Request $request)
    {
        $v = $request->validate([
            'skr'  => 'required|string|size:5',
            'lang' => 'required|string|size:5',
        ]);
        return response()->json(Skr04Account::wherePrivate(0)->get(['id', $v['lang']]), 200);
    }

    public function fetchBookingDetails(Request $request)
    {
        return response()->json(LedgerJournal::distinct()->select('client_details')->get(), 200);
    }

}
