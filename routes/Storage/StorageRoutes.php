<?php

use App\Http\Controllers\Storage\TemporaryFileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('storage')->group(function () {
    Route::post('temp', [TemporaryFileController::class, 'store']);
    Route::get('temp/{key}', [TemporaryFileController::class, 'show'])
        ->where('key', '[A-Za-z0-9\-]+\.[A-Za-z0-9]+');
    Route::delete('temp/{key}', [TemporaryFileController::class, 'destroy'])
        ->where('key', '[A-Za-z0-9\-]+\.[A-Za-z0-9]+');
});
