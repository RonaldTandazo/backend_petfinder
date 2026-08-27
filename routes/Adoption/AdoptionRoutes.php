<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Adoption\AdoptionController;

Route::middleware('auth:sanctum')->prefix('adoptions')->group(function () {
    Route::get('pets', [AdoptionController::class, 'getAdoptionPets']);
});