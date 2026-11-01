<?php

use App\Http\Controllers\Legal\LegalDocumentAcceptanceController;
use App\Http\Controllers\Legal\LegalDocumentController;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Legal\LegalDocumentVersionController;

Route::middleware(['auth:sanctum', 'throttle:10,1'])->prefix('legal')->group(function () {
    // Route::post('legal/publish', [LegalDocumentVersionController::class, 'publish']);
    Route::post('acceptance', [LegalDocumentAcceptanceController::class, 'acceptance']);
});

Route::middleware('throttle:30,1')->prefix('legal')->group(function () {
    Route::get('document', [LegalDocumentController::class, 'getDocument']);
    Route::get('document/download/pdf', [LegalDocumentController::class, 'downloadPdf']);
});