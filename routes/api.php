<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\SearchController;

Route::get('/productos', [ProductApiController::class, 'index']);
Route::get('/search', [ProductApiController::class, 'search']);
Route::get('/suggestions', [SearchController::class, 'suggestions']);