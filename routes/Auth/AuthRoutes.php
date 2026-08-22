<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::prefix('auth')->group(function () {
    Route::prefix('register')->group(function () {
        Route::post('user', [AuthController::class, 'registerUser']);
        Route::post('shelter', [AuthController::class, 'registerShelter']);
    });

    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
});