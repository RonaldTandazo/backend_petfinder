<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::prefix('auth')->group(function () {
    Route::middleware('throttle:3,1')->prefix('register')->group(function () {
        Route::post('user', [AuthController::class, 'registerUser']);
        Route::post('shelter', [AuthController::class, 'registerShelter']);
    });

    Route::middleware('throttle:5,1')->post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});