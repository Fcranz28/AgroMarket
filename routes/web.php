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

// --- RUTAS DE AUTENTICACIÓN ARREGLADAS ---

// Registro
Route::get('/register', [RegisterController::class, 'create'])->name('register'); // Muestra el form
Route::post('/register', [RegisterController::class, 'store']); // Procesa el form

// Login (Asumiendo que también crearás LoginController)
// Por ahora, solo mostramos la vista, pero necesitarás un 'store'
Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [LoginController::class, 'store']); // <-- Necesitarás esto

// RUTAS PARA USUARIOS LOGUEADOS (Dashboard)
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {

    // Esto crea:
    // dashboard.productos.index   (GET /dashboard/productos) -> "Mis Productos"
    // dashboard.productos.create  (GET /dashboard/productos/create) -> "Agregar Producto"
    // dashboard.productos.store   (POST /dashboard/productos)
    // dashboard.productos.edit    (GET /dashboard/productos/{id}/edit)
    // dashboard.productos.update  (PUT/PATCH /dashboard/productos/{id})
    // dashboard.productos.destroy (DELETE /dashboard/productos/{id})
    Route::resource('productos', UserProductController::class);
});

// Logout
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');


// --- FIN DE RUTAS DE AUTH ---

// Rutas para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
});

// Rutas de Administrador (requieren auth y un rol de 'admin')
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Esto crea todas las rutas para el CRUD (index, create, store, edit, update, destroy)
    Route::resource('productos', ProductCrudController::class);
});

Route::get('/producto/{slug}', [ProductController::class, 'show'])->name('products.show');

// ¡YA NO ESTÁ LA LLAVE "}" EXTRA AQUÍ!