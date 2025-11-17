<?php

namespace App\Http\Controllers; // <-- Revisa si esta es la carpeta

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException; // Para manejar errores

class RegisterController extends Controller
{
    /**
     * Muestra la vista del formulario de registro.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Procesa la petición de registro.
     */
    public function store(Request $request)
    {
        // 1. Validar los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' busca 'password_confirmation'
        ]);

        // 2. Crear el nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // 'is_admin' será 'false' por defecto, gracias a tu migración.
        ]);

        // 3. Iniciar sesión con el usuario recién creado
        Auth::login($user);

        // 4. Redirigir al usuario al 'home'
        return redirect()->route('home');
    }
}