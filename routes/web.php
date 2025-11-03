<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\ProductCrudController;
use App\Http\Controllers\ProfileController;

// Páginas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/nosotros', [PageController::class, 'about'])->name('about');
Route::get('/contacto', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contacto', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/productos', [ProductController::class, 'index'])->name('products.index');

// Rutas de autenticación (Laravel Breeze las crea automáticamente)
// GET /login, POST /login, GET /register, POST /register, POST /logout

// Rutas para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
    // Laravel Breeze/Jetstream suele gestionar el perfil
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
});

// Rutas de Administrador (requieren auth y un rol de 'admin')
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Esto crea todas las rutas para el CRUD (index, create, store, edit, update, destroy)
    Route::resource('productos', ProductCrudController::class);
});

Route::get('/producto/{slug}', [ProductController::class, 'show'])->name('products.show');
