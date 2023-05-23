<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\Auth\GoogleAuthController;
use App\Http\Controllers\Api\Auth\RegisteredUserController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\FolderController;
use App\Http\Controllers\Api\V1\PlanController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\Services\JobController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

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

// Foreign services endpoints
Route::group(['prefix' => 'v1'], function() {
    Route::apiResource('plans', PlanController::class)->only('index');
    Route::get('jobs/{job?}/{token?}', [JobController::class, 'index']);
    Route::post('contacts', [ContactController::class, 'store']);
    Route::get('ping', function() { return response()->json(); });
});

// General API endpoints
Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function() {
    Route::get('user', function() { return Auth::user(); });
    Route::apiResource('users', UserController::class)->except('store');
    Route::apiResource('documents', DocumentController::class)->middleware('accept-files');
    Route::apiResource('folders', FolderController::class);
    Route::apiResource('profiles', ProfileController::class);
    Route::get('files', [FolderController::class, 'files']);
});

// General Admin API
Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum','ensure.isAdmin']], function () {
    Route::get('contacts', [ContactController::class, 'index']);
    Route::delete('contacts/{contact}', [ContactController::class, 'destroy']);
});
