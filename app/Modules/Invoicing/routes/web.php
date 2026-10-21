<?php

use App\Modules\Invoicing\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

// Comprobante de una compra como invitado (enlace firmado que se entrega al pagar)
Route::get('/pedidos/{order}/comprobante', [InvoiceController::class, 'guestReceipt'])
    ->middleware('signed')
    ->name('orders.guest-invoice');

// Descarga del PDF para invitados (enlace firmado que entrega la página anterior)
Route::get('/factura/{invoice}/descargar-invitado', [InvoiceController::class, 'guestDownload'])
    ->middleware('signed')
    ->name('invoice.guest-download');

Route::middleware('auth')->group(function () {
    Route::get('/factura/{invoice}/descargar', [InvoiceController::class, 'download'])->name('invoice.download');
    Route::get('/factura/{invoice}', [InvoiceController::class, 'show'])->name('invoice.show');
});
