<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Catalog\CatalogController;

Route::middleware('auth:sanctum')->prefix('catalog')->group(function () {
    Route::get('publish/pet', [CatalogController::class, 'getPublishPetCatalog']);
});