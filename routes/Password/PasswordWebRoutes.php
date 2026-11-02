<?php

use App\Http\Controllers\Password\PasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('password')->group(function () {
    Route::middleware('throttle:5,1')->post('reset', [PasswordController::class, 'reset'])->name('password.reset.web');
    
    Route::get('reset/{token}', [PasswordController::class, 'show'])->name('password.reset.form');
});