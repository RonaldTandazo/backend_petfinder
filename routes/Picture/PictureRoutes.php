<?php

use App\Http\Controllers\Picture\PictureController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('pictures')->group(function () {
    Route::put('/', [PictureController::class, 'update']);
    Route::get('{picture}/file', [PictureController::class, 'file']);
    Route::delete('{picture}', [PictureController::class, 'destroy']);
});
