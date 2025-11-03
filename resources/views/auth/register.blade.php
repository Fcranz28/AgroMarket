@extends('layouts.app')

@push('styles')
    @vite(['resources/css/login.css'])
@endpush

@push('scripts')
    @vite(['resources/js/login.js'])
@endpush

@section('content')
    <div class="login-body">
    <div class="container-auth">

        <div class="toggle-panel">
            <h1>¡Bienvenido!</h1>
            <p>Si ya tienes una cuenta, inicia sesión para ver tus pedidos</p>
            <a href="{{ route('login') }}" class="btn">Iniciar Sesión</a>
        </div>

        <div class="form-panel">
            <form method="POST" action="{{ route('register') }}" class="form-container">
                @csrf
                <h1>Crear Cuenta</h1>
                <span>Usa tu correo para registrarte</span>

                <div class="input-group">
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" />
                    <label for="name">Nombre</label>
                </div>
                @error('name')
                    <div class="input-error">{{ $message }}</div>
                @enderror

                <div class="input-group">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" />
                    <label for="email">Correo Electrónico</label>
                </div>
                @error('email')
                    <div class="input-error">{{ $message }}</div>
                @enderror

                <div class="input-group">
                    <input id="password" type="password" name="password" required autocomplete="new-password" />
                    <label for="password">Contraseña</label>
                </div>
                @error('password')
                    <div class="input-error">{{ $message }}</div>
                @enderror

                <div class="input-group">
                    <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <label for="password-confirm">Confirmar Contraseña</label>
                </div>

                <button type="submit" class="btn">Registrarse</button>
            </form>
        </div>

    </div>
</div>
@endsection