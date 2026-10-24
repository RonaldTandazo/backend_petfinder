<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Help\FeedbackController;
use App\Http\Controllers\Help\SupportController;

Route::middleware('auth:sanctum')->prefix('help')->group(function () {
    Route::post('feedback/store', [FeedbackController::class, 'store']);
    Route::post('support/store', [SupportController::class, 'store']);
});