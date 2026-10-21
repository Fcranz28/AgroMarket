<?php

namespace App\Modules\Identity\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Enums\UserRole;
use App\Modules\Identity\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            'role' => UserRole::User, // Default
            'onboarding_completed' => false,
        ]);

        // 3. Enviar el correo de verificación e iniciar sesión
        event(new Registered($user));
        Auth::login($user);

        // 4. Redirigir al onboarding (pide verificar el correo antes de continuar)
        return redirect()->route('onboarding.welcome');
    }
}
