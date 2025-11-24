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
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Crear el nuevo usuario (Rol por defecto: user, pero se cambiará en onboarding)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_USER, // Default
            'onboarding_completed' => false,
        ]);

        // 3. Iniciar sesión
        Auth::login($user);

        // 4. Redirigir al onboarding
        return redirect()->route('onboarding.welcome');
    }
}