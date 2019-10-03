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
})->middleware(['auth', 'throttle:20,1']);

// Accounting Management System routes
Route::get('/fetch/account/charts', 'Cab7\BookingController@fetchAccountCharts');
Route::post('/read/hale/gdpdu/export', 'Cab7\FileController@readHaleGdpduExport')->middleware(['auth', 'throttle:20,1']);
Route::post('/book/double/entry', 'Cab7\BookingController@bookDoubleEntry')->middleware(['auth', 'throttle:50,1']);

// Auth user routes - mutual for all projects
Route::post('/login', 'AuthController@login');//->middleware(['guest', 'throttle:20,1']);
Route::match(['GET', 'POST'], '/logout', 'AuthController@logout')->middleware(['auth', 'throttle:20,1']);
Route::post('/register', 'AuthController@register')->middleware(['guest', 'throttle:20,1']);
Route::post('/verify/email', 'AuthController@verifyEmail')->middleware(['guest', 'throttle:20,1']);
Route::post('/forgot/password', 'AuthController@forgotPassword')->middleware(['guest', 'throttle:20,1']);
Route::post('/reset/password', 'AuthController@resetPassword')->middleware(['guest', 'throttle:20,1']);
// Other user routes
Route::get('/fetch/user/details', 'UserController@show')->middleware(['auth', 'throttle:20,1']);
// Route::post('/update/user', 'UserController@update')->middleware(['auth:api', 'throttle:20,1']);
// Route::post('/delete/user', 'UserController@delete')->middleware(['auth:api', 'throttle:20,1']);
// Route::get('/delete/user/{token}', 'UserController@forceDeleteFromMail')->middleware('throttle:20,1');













