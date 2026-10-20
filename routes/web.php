<?php

use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
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

// RUTAS PARA USUARIOS LOGUEADOS
Route::middleware(['auth'])->group(function () {
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
