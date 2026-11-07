<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Follow\FollowController;

Route::middleware('auth:sanctum')->prefix('follow')->group(function () {
    Route::post('pet', [FollowController::class, 'setPetFollow']);
    Route::get('index', [FollowController::class, 'petPetFollowindex']);
    Route::get('pets', [FollowController::class, 'getPetFollows']);
});