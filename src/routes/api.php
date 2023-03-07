<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\Auth\GoogleAuthController;
use App\Http\Controllers\Api\Auth\RegisteredUserController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\DocumentController;
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
    Route::group(['prefix' => 'register'], function() {
        Route::post('/', [RegisteredUserController::class, 'store']);
        Route::post('/google', [GoogleAuthController::class, 'store']);
    });
    Route::group(['prefix' => 'login'], function() {
        Route::post('/', [AuthenticatedSessionController::class, 'store']);
        Route::post('/google', [GoogleAuthController::class, 'login']);
    });
});

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::post('refresh', [AuthenticatedSessionController::class, 'refresh']);
});

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function() {
    Route::get('user', function() { return Auth::user(); });
    Route::apiResource('users', UserController::class)->except('store');
    Route::apiResource('documents', DocumentController::class);
    Route::apiResource('profiles', ProfileController::class);
});
