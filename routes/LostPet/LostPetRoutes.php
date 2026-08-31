<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LostPet\LostPetController;

Route::middleware('auth:sanctum')->prefix('lost-pets')->group(function () {
    Route::get('reports', [LostPetController::class, 'getLostPets']);
    Route::get('{lostPetId}', [LostPetController::class, 'show']);
    Route::post('store', [LostPetController::class, 'store']);
    Route::put('update/{lostPetId}', [LostPetController::class, 'update']);
    Route::delete('delete/{lostPetId}', [LostPetController::class, 'delete']);
});