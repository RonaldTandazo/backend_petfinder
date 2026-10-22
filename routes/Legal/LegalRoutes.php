<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Legal\LegalDocumentAcceptanceController;

Route::middleware('auth:sanctum')->prefix('legal')->group(function () {
    Route::post('acceptance', [LegalDocumentAcceptanceController::class, 'acceptance']);
});