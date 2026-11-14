<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LostPet\LostPetController;

Route::middleware('auth:sanctum')->prefix('lost-pets')->group(function () {
    Route::get('', [LostPetController::class, 'getLostPets']);

    Route::get('{lostPetId}', [LostPetController::class, 'getLostPet']);

    Route::post('store', [LostPetController::class, 'store']);

    Route::put('update/{lostPetId}', [LostPetController::class, 'update']);
    
    Route::delete('delete/{lostPetId}', [LostPetController::class, 'delete']);

    Route::put('status/{lostPetId}', [LostPetController::class, 'changeStatus']);
});