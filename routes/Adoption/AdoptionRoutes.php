<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Adoption\AdoptionController;

Route::middleware('auth:sanctum')->prefix('adoptions')->group(function () {
    Route::get('pets', [AdoptionController::class, 'getAdoptionPets']);
    Route::get('pets/{petId}', [AdoptionController::class, 'getAdoptionPet']);
    Route::post('store', [AdoptionController::class, 'store']);
    Route::put('update/{petId}', [AdoptionController::class, 'update']);
    Route::delete('delete/{petId}', [AdoptionController::class, 'delete']);
    Route::post('follow/{petId}', [AdoptionController::class, 'handleFollow']);
});