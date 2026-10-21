<?php

use App\Modules\Moderation\Http\Controllers\Admin\AdminReportController;
use App\Modules\Moderation\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('/reportes', [ReportController::class, 'store'])->name('reports.store');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/reportes', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reportes/{report}', [AdminReportController::class, 'show'])->name('reports.show');
        Route::patch('/reportes/{report}/status', [AdminReportController::class, 'updateStatus'])->name('reports.status');
    });
});
