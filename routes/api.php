<?php

namespace App\Http\Controllers\Api;

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

Route::prefix('customer')->group(function () {
    Route::post('/register', [AuthController::class, 'register', ['as' => 'customer']]);
    Route::post('/login', [AuthController::class, 'login', ['as' => 'customer']]);
    Route::group(['middleware' => 'auth:api_customer'], function () {
        Route::get('/user', [AuthController::class, 'getUser', ['as' => 'customer']]);
        Route::get('/refresh', [AuthController::class, 'refershToken', ['as' => 'customer']]);
        Route::post('/logout', [AuthController::class, 'logout', ['as' => 'customer']]);
    });
});