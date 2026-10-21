<?php

use App\Modules\Catalog\Http\Controllers\Api\ProductApiController;
use App\Modules\Catalog\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/productos', [ProductApiController::class, 'index']);
    Route::get('/search', [SearchController::class, 'search'])->name('api.search');
    Route::get('/suggestions', [SearchController::class, 'suggestions']);
});
