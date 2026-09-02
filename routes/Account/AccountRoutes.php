<?php

use App\Http\Controllers\Account\AccountController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('account')->group(function () {
    Route::get('form-catalog', [AccountController::class, 'formCatalog']);
    Route::put('profile', [AccountController::class, 'updateProfile']);
    Route::put('password', [AccountController::class, 'updatePassword']);
});