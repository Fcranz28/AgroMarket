<?php

use App\Modules\Catalog\Http\Controllers\Admin\ProductCrudController;
use App\Modules\Catalog\Http\Controllers\FarmerProductController;
use App\Modules\Catalog\Http\Controllers\ProductController;
use App\Modules\Catalog\Http\Controllers\ReviewController;
use App\Modules\Catalog\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

// Catálogo público
Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
Route::get('/producto/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/buscar', [SearchController::class, 'index'])->name('search');

Route::middleware('auth')->group(function () {
    Route::post('/productos/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Productos del agricultor
    Route::middleware('role:farmer')->prefix('agricultor')->name('dashboard.')->group(function () {
        Route::resource('productos', FarmerProductController::class);
    });

    // Productos (admin)
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('productos', ProductCrudController::class);
    });
});
