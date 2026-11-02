<?php

use App\Http\Controllers\Password\PasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:5,1')->prefix('password')->group(function () {
    Route::post('forgot', [PasswordController::class, 'forgotPassword']);
});