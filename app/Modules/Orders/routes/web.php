<?php

use App\Modules\Orders\Http\Controllers\CheckoutController;
use App\Modules\Orders\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Checkout (también para invitados)
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');

Route::middleware('auth')->group(function () {
    // Pedidos del comprador
    Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pedidos/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Pedidos del agricultor (como vendedor)
    Route::middleware('role:farmer')->prefix('agricultor')->group(function () {
        Route::get('/pedidos', [OrderController::class, 'farmerOrders'])->name('farmer.orders');
        Route::get('/pedidos/{order}/detalles', [OrderController::class, 'farmerShow'])->name('farmer.orders.show');
        Route::patch('/pedidos/{order}/status', [OrderController::class, 'updateStatus'])->name('farmer.orders.status');
    });
});
