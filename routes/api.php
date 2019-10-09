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

    // Accounting Management System routes
    Route::get('/fetch/account/charts', 'Cab7\BookingController@fetchAccountCharts');
    Route::post('/read/hale/gdpdu/export', 'Cab7\FileController@readHaleGdpduExport')->middleware('auth');
    Route::post('/book/double/entry', 'Cab7\BookingController@bookDoubleEntry')->middleware('auth');

    // Auth user routes - mutual for all projects
    Route::post('/login', 'AuthController@login')->middleware('guest')->name('login');
    Route::post('/auth/check', 'AuthController@authCheck')->middleware('auth')->name('auth-check');
    Route::post('/logout', 'AuthController@logout')->middleware('auth')->name('logout');
    Route::post('/register', 'AuthController@register')->middleware('guest')->name('register');
    Route::post('/resend/verification', 'AuthController@resendVerification')->middleware('guest')->name('resend/verification');
    Route::post('/verify/email', 'AuthController@verifyEmail')->middleware('guest')->name('verify/email');
    Route::post('/forgot/password', 'AuthController@forgotPassword')->middleware('guest')->name('forgot/password');
    Route::post('/reset/password', 'AuthController@resetPassword')->middleware('guest')->name('reset/password');
    Route::post('/account/delete/request', 'AuthController@accountDeleteRequest')->middleware('auth')->name('account/delete/request');
    Route::post('/account/delete/confirm', 'AuthController@accountDeleteConfirm')->middleware('guest')->name('account/delete/confirm');
    // Async client validations
    Route::post('/validate/email/exists', 'AuthController@validateEmailExists')->middleware('guest')->name('validate-email-exists');
    Route::post('/validate/email/unique', 'AuthController@validateEmailUnique')->middleware('guest')->name('validate-email-unique');
    // Other user routes
    Route::get('/fetch/user/details', 'UserController@show')->middleware('auth');
    // Route::post('/update/user', 'UserController@update')->middleware('auth:api');
    // Route::post('/delete/user', 'UserController@delete')->middleware('auth:api');
    // Route::get('/delete/user/{token}', 'UserController@forceDeleteFromMail');
});













