<?php

use App\Http\Controllers\Api\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\V1\Auth\RegisteredUserController;
use App\Http\Controllers\Api\V1\UserController;
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

Route::group(['middleware' => 'guest'], function() {
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::group(['middleware' => 'auth'], function() {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);
});

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function() {
    Route::get('user', function(Request $request) {
        return $request->user();
    });
    Route::get('users', [UserController::class, 'index']);
});
