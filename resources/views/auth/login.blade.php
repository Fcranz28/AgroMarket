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

        <div class="form-panel">
            <form method="POST" action="{{ route('login') }}" class="form-container">
                @csrf
                <h1>Iniciar Sesión</h1>
                <span>Usa tu correo y contraseña</span>

                <div class="input-group">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" />
                    <label for="email">Correo Electrónico</label>
                </div>
                @error('email')
                    <div class="input-error">{{ $message }}</div>
                @enderror

                <div class="input-group">
                    <input id="password" type="password" name="password" required autocomplete="current-password" />
                    <label for="password">Contraseña</label>
                </div>
                @error('password')
                    <div class="input-error">{{ $message }}</div>
                @enderror
                
                <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                
                <div style="margin: 1rem 0; text-align: left; font-size: 0.875rem;">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" style="color: var(--text-color-light, #777); padding-left: 5px;">Recuérdame</label>
                </div>

                <button type="submit" class="btn">Iniciar Sesión</button>
            </form>
        </div>

        <div class="toggle-panel">
            <h1>¡Hola, Amigo!</h1>
            <p>Regístrate con tus datos personales para usar todas las funciones del sitio</p>
            <a href="{{ route('register') }}" class="btn">Registrarse</a>
        </div>

    </div>
</div>
@endsection