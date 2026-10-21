<?php

use App\Modules\Identity\Http\Controllers\AddressController;
use App\Modules\Identity\Http\Controllers\Admin\UserManagementController;
use App\Modules\Identity\Http\Controllers\Admin\VerificationDocumentController;
use App\Modules\Identity\Http\Controllers\FirebaseAuthController;
use App\Modules\Identity\Http\Controllers\LoginController;
use App\Modules\Identity\Http\Controllers\OnboardingController;
use App\Modules\Identity\Http\Controllers\ProfileController;
use App\Modules\Identity\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

// Registro y login
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:5,1');

Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [LoginController::class, 'store']);

Route::post('/auth/firebase', [FirebaseAuthController::class, 'authenticate'])
    ->middleware('throttle:10,1')
    ->name('auth.firebase');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->name('logout');

Route::middleware('auth')->group(function () {
    // Perfil y direcciones
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::resource('addresses', AddressController::class)->only(['index', 'store', 'destroy']);

    // Onboarding
    Route::prefix('onboarding')->name('onboarding.')->middleware('onboarding.pending')->group(function () {
        Route::get('/', [OnboardingController::class, 'welcome'])->name('welcome');
        Route::post('/role', [OnboardingController::class, 'selectRole'])->name('role');
        Route::get('/intereses', [OnboardingController::class, 'userPreferences'])->name('user');
        Route::post('/intereses', [OnboardingController::class, 'savePreferences'])->name('preferences');
        Route::get('/verificacion', [OnboardingController::class, 'farmerVerification'])->name('farmer');
        Route::post('/verificacion', [OnboardingController::class, 'saveVerification'])->name('verification');
    });

    // Gestión de usuarios (admin)
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/usuarios', [UserManagementController::class, 'users'])->name('users');
        Route::get('/usuarios/{user}/verificar', [UserManagementController::class, 'verifyView'])->name('verify.view');
        Route::get('/usuarios/{user}/documentos/{document}', VerificationDocumentController::class)
            ->whereIn('document', ['face_photo', 'dni_front', 'dni_back'])
            ->name('verification.document');
        Route::post('/usuarios/consultar-dni', [UserManagementController::class, 'checkDni'])->name('check.dni');
        Route::post('/usuarios/{user}/verificar/{status}', [UserManagementController::class, 'verifyFarmer'])->name('verify');
        Route::post('/usuarios/{user}/ban', [UserManagementController::class, 'toggleBan'])->name('ban');
    });
});
