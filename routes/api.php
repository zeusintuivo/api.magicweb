<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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

Route::prefix('{locale}')->middleware(['throttle:20,1', 'locale'])->group(function () {

    // 1st test
    Route::get('/test', function (Request $request) {
        return response()->json([
            'request' => $request->toArray(),
            'locale'  => App::getLocale(),
        ], 200);
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
    Route::post('/login', 'AuthController@login')->middleware('guest');
    Route::match(['GET', 'POST'], '/logout', 'AuthController@logout')->middleware('auth');
    Route::post('/register', 'AuthController@register')->middleware('guest');
    Route::post('/verify/email', 'AuthController@verifyEmail')->middleware('guest');
    Route::post('/forgot/password', 'AuthController@forgotPassword')->middleware('guest');
    Route::post('/reset/password', 'AuthController@resetPassword')->middleware('guest');
    // Other user routes
    Route::get('/fetch/user/details', 'UserController@show')->middleware('auth');
    // Route::post('/update/user', 'UserController@update')->middleware('auth:api');
    // Route::post('/delete/user', 'UserController@delete')->middleware('auth:api');
    // Route::get('/delete/user/{token}', 'UserController@forceDeleteFromMail');
});













