<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('{locale}')->group(function () {

    // 1st test
    Route::get('/test', function (Request $request) {

        $user = new User();
        return $user->accountForceDelete();

        // return response()->json([
        //     'request' => $request->toArray(),
        //     'locale'  => App::getLocale(),
        // ], 200);
    });

    // 2nd test
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth');

    // Auth user routes - mutual for all projects
    Route::post('/login', 'AuthController@login')->middleware('guest')->name('login');
    Route::post('/auth/check', 'AuthController@authCheck')->middleware('auth');
    Route::post('/logout', 'AuthController@logout')->middleware('auth');
    Route::post('/register', 'AuthController@register')->middleware('guest')->name('register');
    Route::post('/resend/verification', 'AuthController@resendVerification')->middleware('guest')->name('resend/verification');
    Route::post('/verify/email', 'AuthController@verifyEmail')->middleware('guest')->name('verify/email');
    Route::post('/forgot/password', 'AuthController@forgotPassword')->middleware('guest')->name('forgot/password');
    Route::post('/reset/password', 'AuthController@resetPassword')->middleware('guest')->name('reset/password');
    Route::post('/account/delete/request', 'AuthController@accountDeleteRequest')->middleware('auth')->name('account/delete/request');
    Route::post('/account/delete/confirm', 'AuthController@accountDeleteConfirm')->middleware('guest')->name('account/delete/confirm');
    // Async client validations
    Route::post('/validate/email/exists', 'AuthController@validateEmailExists')->middleware('guest');
    Route::post('/validate/email/unique', 'AuthController@validateEmailUnique')->middleware('guest');

    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::post('/fetch/users', 'AdminController@fetchUsers')->middleware('auth');
    });

    // Cab7 routes
    Route::prefix('cab7')->group(function () {
        // User routes (deprecated)
        Route::prefix('user')->group(function () {
            Route::get('/fetch', 'Cab7\UserController@show')->middleware('auth');
            // Route::post('/update/user', 'UserController@update')->middleware('auth:api');
            // Route::post('/delete/user', 'UserController@delete')->middleware('auth:api');
            // Route::get('/delete/user/{token}', 'UserController@forceDeleteFromMail');
        });
        // Accounting Management System Resources
        Route::prefix('amsr')->group(function () {
            Route::get('/fetch/standard/accounts', 'Cab7\BookingController@fetchStandardAccounts');
            Route::post('/fetch/ledger/journal', 'Cab7\BookingController@fetchLedgerJournal')->middleware('auth');
            Route::post('/fetch/booking/details', 'Cab7\BookingController@fetchBookingDetails')->middleware('auth');
            Route::post('/read/hale/gdpdu/export', 'Cab7\FileController@readHaleGdpduExport')->middleware('auth');
            Route::post('/book/bank', 'Cab7\BookingController@bookBank')->middleware('auth')->name('book/bank');
            Route::post('/book/bill', 'Cab7\BookingController@bookBill')->middleware('auth')->name('book/bill');
            Route::post('/book/shift', 'Cab7\BookingController@bookShift')->middleware('auth')->name('book/shift');
            Route::post('/book/trip', 'Cab7\BookingController@bookTrip')->middleware('auth')->name('book/trip');
        });
    });

});

// Database administering route group
Route::prefix('db')->group(function () {
    Route::get('/create/table/users', 'Db\Tables\Users@createTableUsers');
    Route::get('/create/table/email/authentications', 'Db\Tables\Users@createTableEmailAuthentications');
    Route::get('/dump/child/tables', 'DB\Tables\Users@dumpChildTables');
});












