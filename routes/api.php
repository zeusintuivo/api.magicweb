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
Route::get('/fetch/account/charts', 'Cab7\BookingController@fetchAccountCharts');
Route::get('/read/hale/gdpdu/export', 'Cab7\FileController@readHaleGdpduExport')->middleware(['auth', 'throttle:20,1']);
Route::post('/book/double/entry', 'Cab7\BookingController@bookDoubleEntry')->middleware(['auth', 'throttle:50,1']);

// Auth user routes
Route::post('/user/login', 'Izgrev\AuthController@loginUser')->middleware(['guest', 'throttle:20,1']);
Route::post('/user/register', 'Izgrev\AuthController@registerUser')->middleware(['guest', 'throttle:20,1']);
Route::post('/user/forgot/password', 'Izgrev\AuthController@forgotPasswordUser')->middleware(['guest', 'throttle:20,1']);
Route::post('/user/reset/password/{token}', 'Izgrev\AuthController@resetPasswordUser')->middleware(['guest', 'throttle:20,1']);
Route::get('/user/verify/email/{token}', 'Izgrev\AuthController@verifyEmailUser')->middleware(['guest', 'throttle:20,1']);
Route::post('/user/logout', 'Izgrev\AuthController@logoutUser')->middleware('throttle:20,1');
// Other user routes
Route::get('/fetch/user/details', 'Cab7\UserController@show')->middleware(['auth:api', 'throttle:20,1']);
// Route::post('/update/user', 'UserController@update')->middleware(['auth:api', 'throttle:20,1']);
// Route::post('/delete/user', 'UserController@delete')->middleware(['auth:api', 'throttle:20,1']);
// Route::get('/delete/user/{token}', 'UserController@forceDeleteFromMail')->middleware('throttle:20,1');













