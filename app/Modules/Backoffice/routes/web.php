<?php

use App\Modules\Backoffice\Http\Controllers\AdminDashboardController;
use App\Modules\Backoffice\Http\Controllers\FarmerDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/agricultor/dashboard', [FarmerDashboardController::class, 'index'])
        ->middleware('role:farmer')
        ->name('farmer.dashboard');

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.dashboard');
});
