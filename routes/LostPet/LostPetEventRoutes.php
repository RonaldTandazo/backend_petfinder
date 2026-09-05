<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LostPet\LostPetEventController;

Route::middleware('auth:sanctum')->prefix('lost-pet/events')->group(function () {
    Route::post('store', [LostPetEventController::class, 'store']);
    Route::delete('delete/{lostPetEventId}', [LostPetEventController::class, 'delete']);
});