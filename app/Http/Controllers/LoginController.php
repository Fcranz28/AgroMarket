<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter; // Added
use Illuminate\Support\Str; // Added
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Muestra la vista del formulario de login.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Procesa la petición de inicio de sesión.
     */
    public function store(Request $request)
    {
        // 1. Validar los datos (email y password)
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Rate Limiting Key
        $throttleKey = Str::lower($request->email) . '|' . $request->ip();

        // Check if too many attempts
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        // 2. Intentar iniciar sesión
        // El 'remember' es para la casilla "Recuérdame"
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            
            // Clear rate limiter on success
            RateLimiter::clear($throttleKey);

            // 3. Si tiene éxito, regenera la sesión
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isFarmer()) {
                return redirect()->route('farmer.dashboard');
            }

            // 4. Redirige al home
            return redirect()->intended(route('home'));
        }

        // Increment rate limiter on failure
        RateLimiter::hit($throttleKey);

        // 5. Si falla, regresa al login con un error
        throw ValidationException::withMessages([
            'email' => 'El email o la contraseña son incorrectos.',
        ]);
    }
}