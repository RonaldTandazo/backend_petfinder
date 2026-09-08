<?php

use App\Http\Controllers\Account\AccountController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('profile')->group(function () {
    Route::get('index', [AccountController::class, 'getProfile']);
    Route::get('edit', [AccountController::class, 'getProfileInfo']);
    Route::put('update', [AccountController::class, 'updateProfile']);
    Route::put('password', [AccountController::class, 'updatePassword']);
});