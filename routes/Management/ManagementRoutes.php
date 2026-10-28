<?php

use App\Http\Controllers\Management\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('management')->group(function () {
    Route::delete('delete-profile', [ManagementController::class, 'deleteAccount']);
});