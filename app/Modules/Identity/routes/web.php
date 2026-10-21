<?php

use App\Modules\Identity\Http\Controllers\AddressController;
use App\Modules\Identity\Http\Controllers\Admin\UserManagementController;
use App\Modules\Identity\Http\Controllers\Admin\VerificationDocumentController;
use App\Modules\Identity\Http\Controllers\FirebaseAuthController;
use App\Modules\Identity\Http\Controllers\OnboardingController;
use App\Modules\Identity\Http\Controllers\ProfileController;
use App\Modules\Identity\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

/*
 * Login, logout, recuperación de contraseña, verificación de correo, cambio de contraseña
 * y verificación en dos pasos los registra Laravel Fortify (config/fortify.php).
 */

// Registro (con límite de intentos) y login social
Route::get('/register', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware(['guest', 'throttle:5,1']);

Route::post('/auth/firebase', [FirebaseAuthController::class, 'authenticate'])
    ->middleware('throttle:10,1')
    ->name('auth.firebase');

Route::middleware('auth')->group(function () {
    // Perfil, direcciones y seguridad de la cuenta
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::resource('addresses', AddressController::class)->only(['index', 'store', 'destroy']);
    Route::view('/cuenta/seguridad', 'auth.security')->middleware('password.confirm')->name('security');

    // Onboarding (requiere el correo verificado)
    Route::prefix('onboarding')->name('onboarding.')->middleware(['verified', 'onboarding.pending'])->group(function () {
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
