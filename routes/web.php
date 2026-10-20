<?php

use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\ProductCrudController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserProductController;
use Illuminate\Support\Facades\Route;

/*
 * Rutas que todavía no pertenecen a un módulo. Cada módulo de app/Modules registra las
 * suyas en app/Modules/<Módulo>/routes/web.php.
 */

// Páginas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/nosotros', [PageController::class, 'about'])->name('about');
Route::get('/contacto', [ContactController::class, 'show'])->name('contact.show');
Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
Route::get('/buscar', [SearchController::class, 'index'])->name('search');

// Checkout & Payment (Guest Access Allowed)
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/payment/create-intent', [PaymentController::class, 'createPaymentIntent'])->name('payment.create-intent')->middleware('throttle:20,1');
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process')->middleware('throttle:10,1');

// Descarga de comprobantes para compras como invitado (enlace firmado)
Route::get('/factura/{invoice}/descargar-invitado', [InvoiceController::class, 'guestDownload'])
    ->middleware('signed')
    ->name('invoice.guest-download');

// RUTAS PARA USUARIOS LOGUEADOS
Route::middleware(['auth'])->group(function () {
    Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pedidos/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Invoice Routes
    Route::get('/factura/{invoice}/descargar', [InvoiceController::class, 'download'])->name('invoice.download');
    Route::get('/factura/{invoice}', [InvoiceController::class, 'show'])->name('invoice.show');

    // Reviews
    Route::post('/productos/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Reports
    Route::post('/reportes', [ReportController::class, 'store'])->name('reports.store');

    // Rutas de Agricultor
    Route::middleware(['role:farmer'])->prefix('agricultor')->group(function () {
        Route::get('/dashboard', [FarmerController::class, 'index'])->name('farmer.dashboard');

        // Farmer Orders (as seller)
        Route::get('/pedidos', [OrderController::class, 'farmerOrders'])->name('farmer.orders');
        Route::get('/pedidos/{order}/detalles', [OrderController::class, 'farmerShow'])->name('farmer.orders.show');
        Route::patch('/pedidos/{order}/status', [OrderController::class, 'updateStatus'])->name('farmer.orders.status');

        // Product Management (Reusing UserProductController but scoped to farmers)
        Route::name('dashboard.')->group(function () {
            Route::resource('productos', UserProductController::class);
        });
    });

    // Rutas de Administrador
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // Admin Product CRUD (if separate from farmer's)
        Route::resource('productos', ProductCrudController::class);

        // Admin Reports
        Route::get('/reportes', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reportes/{report}', [AdminReportController::class, 'show'])->name('reports.show');
        Route::patch('/reportes/{report}/status', [AdminReportController::class, 'updateStatus'])->name('reports.status');
    });
});

// Stripe Webhook (outside auth middleware)
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])
    ->name('stripe.webhook');

Route::get('/producto/{slug}', [ProductController::class, 'show'])->name('products.show');
