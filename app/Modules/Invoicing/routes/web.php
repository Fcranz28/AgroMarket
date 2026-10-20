<?php

use App\Modules\Invoicing\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

// Descarga para compras como invitado (enlace firmado)
Route::get('/factura/{invoice}/descargar-invitado', [InvoiceController::class, 'guestDownload'])
    ->middleware('signed')
    ->name('invoice.guest-download');

Route::middleware('auth')->group(function () {
    Route::get('/factura/{invoice}/descargar', [InvoiceController::class, 'download'])->name('invoice.download');
    Route::get('/factura/{invoice}', [InvoiceController::class, 'show'])->name('invoice.show');
});
