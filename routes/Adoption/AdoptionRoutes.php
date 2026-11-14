<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Adoption\AdoptionController;

Route::middleware('auth:sanctum')->prefix('adoptions')->group(function () {
    Route::post('store', [AdoptionController::class, 'store']);
    
    Route::put('update/{petId}', [AdoptionController::class, 'update']);
    
    Route::delete('delete/{petId}', [AdoptionController::class, 'delete']);

    Route::put('status/{petId}', [AdoptionController::class, 'changeStatus']);
    
    Route::prefix('pets')->group(function () {
        Route::get('', [AdoptionController::class, 'getAdoptionPets']);
        Route::get('{petId}', [AdoptionController::class, 'getAdoptionPet']);
    });
});