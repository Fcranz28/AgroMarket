<?php

use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
 * Rutas que todavía no pertenecen a un módulo. Cada módulo de app/Modules registra las
 * suyas en app/Modules/<Módulo>/routes/web.php.
 */

// Páginas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/nosotros', [PageController::class, 'about'])->name('about');
Route::get('/contacto', [ContactController::class, 'show'])->name('contact.show');

// Payment (Guest Access Allowed)
Route::post('/payment/create-intent', [PaymentController::class, 'createPaymentIntent'])->name('payment.create-intent')->middleware('throttle:20,1');
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process')->middleware('throttle:10,1');

// Descarga de comprobantes para compras como invitado (enlace firmado)
Route::get('/factura/{invoice}/descargar-invitado', [InvoiceController::class, 'guestDownload'])
    ->middleware('signed')
    ->name('invoice.guest-download');

// RUTAS PARA USUARIOS LOGUEADOS
Route::middleware(['auth'])->group(function () {
    // Invoice Routes
    Route::get('/factura/{invoice}/descargar', [InvoiceController::class, 'download'])->name('invoice.download');
    Route::get('/factura/{invoice}', [InvoiceController::class, 'show'])->name('invoice.show');

    // Reports
    Route::post('/reportes', [ReportController::class, 'store'])->name('reports.store');

    // Rutas de Agricultor
    Route::middleware(['role:farmer'])->prefix('agricultor')->group(function () {
        Route::get('/dashboard', [FarmerController::class, 'index'])->name('farmer.dashboard');
    });

    // Rutas de Administrador
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // Admin Reports
        Route::get('/reportes', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reportes/{report}', [AdminReportController::class, 'show'])->name('reports.show');
        Route::patch('/reportes/{report}/status', [AdminReportController::class, 'updateStatus'])->name('reports.status');
    });
});

// Stripe Webhook (outside auth middleware)
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])
    ->name('stripe.webhook');
