<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
 * Páginas públicas que no pertenecen a ningún dominio. El resto de las rutas vive en
 * app/Modules/<Módulo>/routes/web.php y routes/api.php.
 */

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/nosotros', [PageController::class, 'about'])->name('about');
Route::get('/contacto', [ContactController::class, 'show'])->name('contact.show');
