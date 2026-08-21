<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pet\PetController;

Route::prefix('pets')->middleware('auth:sanctum')->group(function () {
    Route::get('all', [PetController::class, 'index']);
    Route::get('{petId}', [PetController::class, 'show']);
    Route::post('register', [PetController::class, 'store']);
    Route::put('update/{petId}', [PetController::class, 'update']);
    Route::delete('delete/{petId}', [PetController::class, 'delete']);
});