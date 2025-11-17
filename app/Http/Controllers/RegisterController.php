<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[a-záéíóúñ\s]+$/i',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|regex:/^[0-9]{9}$/',
            'address' => 'required|string|max:500',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
        ], [
            'name.regex' => 'El nombre solo debe contener letras y espacios.',
            'email.unique' => 'Este email ya está registrado.',
            'phone.regex' => 'El teléfono debe tener 9 dígitos.',
            'password.regex' => 'La contraseña debe tener: mayúscula, minúscula, número y símbolo (@$!%*?&).',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            return redirect('register')
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'is_admin' => false,
        ]);

        auth()->login($user);

        return redirect('/')->with('success', '¡Bienvenido! Tu cuenta ha sido creada.');
    }
}
