<?php
use App\Http\Controllers\UserProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\ProductCrudController;
use Illuminate\Support\Facades\Route;

// Importamos los controladores de Auth que nos faltaban
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController; // <-- Lo añadimos para el login

// Páginas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/nosotros', [PageController::class, 'about'])->name('about');
Route::get('/contacto', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contacto', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
Route::get('/buscar', [App\Http\Controllers\SearchController::class, 'index'])->name('search');

// --- RUTAS DE AUTENTICACIÓN ARREGLADAS ---

// Registro
Route::get('/register', [RegisterController::class, 'create'])->name('register'); // Muestra el form
Route::post('/register', [RegisterController::class, 'store']); // Procesa el form

// Login (Asumiendo que también crearás LoginController)
// Por ahora, solo mostramos la vista, pero necesitarás un 'store'
Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [LoginController::class, 'store']); 

// Logout
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

// RUTAS PARA USUARIOS LOGUEADOS
Route::middleware(['auth'])->group(function () {
    Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');

    // Onboarding Routes
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/', [App\Http\Controllers\OnboardingController::class, 'welcome'])->name('welcome');
        Route::post('/role', [App\Http\Controllers\OnboardingController::class, 'selectRole'])->name('role');
        Route::get('/intereses', [App\Http\Controllers\OnboardingController::class, 'userPreferences'])->name('user');
        Route::post('/intereses', [App\Http\Controllers\OnboardingController::class, 'savePreferences'])->name('preferences');
        Route::get('/verificacion', [App\Http\Controllers\OnboardingController::class, 'farmerVerification'])->name('farmer');
        Route::post('/verificacion', [App\Http\Controllers\OnboardingController::class, 'saveVerification'])->name('verification');
    });

    // Rutas de Agricultor

    // Rutas de Agricultor
    Route::middleware(['role:farmer'])->prefix('agricultor')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\FarmerController::class, 'index'])->name('farmer.dashboard');
        
        // Product Management (Reusing UserProductController but scoped to farmers)
        Route::name('dashboard.')->group(function () {
            Route::resource('productos', UserProductController::class);
        });
    });

    // Rutas de Administrador
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
        Route::get('/usuarios', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
        Route::post('/usuarios/{user}/verificar/{status}', [App\Http\Controllers\AdminController::class, 'verifyFarmer'])->name('verify');
        Route::post('/usuarios/{user}/ban', [App\Http\Controllers\AdminController::class, 'toggleBan'])->name('ban');
        
        // Admin Product CRUD (if separate from farmer's)
        Route::resource('productos', ProductCrudController::class);
    });
});

Route::get('/producto/{slug}', [ProductController::class, 'show'])->name('products.show');

// ¡YA NO ESTÁ LA LLAVE "}" EXTRA AQUÍ!