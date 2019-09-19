<?php

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

// 1st test - OK
Route::get('/test', function (Request $request) {
    return $request->toArray();
});

// 2nd test - OK
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(['auth:api', 'throttle:20,1']);

// Accounting Management System routes
Route::get('/fetch/account/charts', 'BookingController@fetchAccountCharts');
Route::get('/read/hale/gdpdu/export', 'FileController@readHaleGdpduExport')->middleware(['auth:api', 'throttle:20,1']);
Route::post('/book/double/entry', 'BookingController@bookDoubleEntry')->middleware(['auth:api', 'throttle:20,1']);

// User routes
Route::get('/fetch/user/details', 'UserController@show')->middleware(['auth:api', 'throttle:20,1']);
// Route::post('/login', 'Auth\LoginController@loginApi')->middleware('throttle:20,1');
// Route::post('/logout', 'Auth\LoginController@logoutApi')->middleware('throttle:20,1');
// Route::post('/register', 'Auth\RegisterController@registerApi')->middleware('throttle:20,1');
// Route::get('/verify/user/{token}', 'Auth\RegisterController@verifyApi')->middleware('throttle:20,1');
// Route::post('/forgot/password', 'Auth\ForgotController@forgotPassword')->middleware('throttle:20,1');
// Route::post('/reset/password', 'Auth\ResetController@resetPasswordApi')->middleware('throttle:20,1');
// Route::post('/update/user', 'UserController@update')->middleware(['auth:api', 'throttle:20,1']);
// Route::post('/delete/user', 'UserController@delete')->middleware(['auth:api', 'throttle:20,1']);
// Route::get('/delete/user/{token}', 'UserController@forceDeleteFromMail')->middleware('throttle:20,1');













