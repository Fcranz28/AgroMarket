<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // 2. Intentar iniciar sesión
        // El 'remember' es para la casilla "Recuérdame"
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            
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

        // 5. Si falla, regresa al login con un error
        throw ValidationException::withMessages([
            'email' => 'El email o la contraseña son incorrectos.',
        ]);
    }
}