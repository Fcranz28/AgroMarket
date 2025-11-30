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
Route::get('/api/search', [App\Http\Controllers\SearchController::class, 'search'])->name('api.search');

// Checkout & Payment (Guest Access Allowed)
Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/payment/create-intent', [App\Http\Controllers\PaymentController::class, 'createPaymentIntent'])->name('payment.create-intent');
Route::post('/payment/process', [App\Http\Controllers\PaymentController::class, 'processPayment'])->name('payment.process');

// Firebase Authentication (without CSRF)
Route::post('/auth/firebase', [App\Http\Controllers\FirebaseAuthController::class, 'authenticate'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

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
    Route::get('/pedidos/{order}', [OrderController::class, 'show'])->name('orders.show');
    
    // Invoice Routes
    Route::get('/factura/{invoice}/descargar', [App\Http\Controllers\InvoiceController::class, 'download'])->name('invoice.download');
    Route::get('/factura/{invoice}/ver', [App\Http\Controllers\InvoiceController::class, 'view'])->name('invoice.view');
    Route::get('/factura/{invoice}', [App\Http\Controllers\InvoiceController::class, 'show'])->name('invoice.show');
    
    // Profile Routes
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');

    // Address Routes
    Route::resource('addresses', App\Http\Controllers\AddressController::class)->only(['index', 'store', 'destroy']);

    // Reviews
    Route::post('/productos/{product}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

    // Reports
    Route::post('/reportes', [App\Http\Controllers\ReportController::class, 'store'])->name('reports.store');

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
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
        Route::get('/usuarios', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
        Route::get('/usuarios/{user}/verificar', [App\Http\Controllers\AdminController::class, 'verifyView'])->name('verify.view');
        Route::post('/usuarios/consultar-dni', [App\Http\Controllers\AdminController::class, 'checkDni'])->name('check.dni');
        Route::post('/usuarios/{user}/verificar/{status}', [App\Http\Controllers\AdminController::class, 'verifyFarmer'])->name('verify');
        Route::post('/usuarios/{user}/ban', [App\Http\Controllers\AdminController::class, 'toggleBan'])->name('ban');
        
        // Admin Product CRUD (if separate from farmer's)
        Route::resource('productos', ProductCrudController::class);

        // Admin Reports
        Route::get('/reportes', [App\Http\Controllers\Admin\AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reportes/{report}', [App\Http\Controllers\Admin\AdminReportController::class, 'show'])->name('reports.show');
        Route::patch('/reportes/{report}/status', [App\Http\Controllers\Admin\AdminReportController::class, 'updateStatus'])->name('reports.status');
    });
});

// Stripe Webhook (outside auth middleware)
Route::post('/stripe/webhook', [App\Http\Controllers\PaymentController::class, 'webhook'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);


Route::get('/producto/{slug}', [ProductController::class, 'show'])->name('products.show');

// ¡YA NO ESTÁ LA LLAVE "}" EXTRA AQUÍ!