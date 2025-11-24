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
                
                <div class="divider">
                    <span>O continúa con</span>
                </div>
                
                <div class="social-login">
                    <button type="button" class="social-btn google-login-btn">
                        <svg width="18" height="18" viewBox="0 0 18 18">
                            <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z"/>
                            <path fill="#34A853" d="M9.003 18c2.43 0 4.467-.806 5.956-2.18L12.05 13.56c-.806.54-1.836.86-3.047.86-2.344 0-4.328-1.584-5.036-3.711H.96v2.332C2.438 15.983 5.482 18 9.003 18z"/>
                            <path fill="#FBBC05" d="M3.964 10.71c-.18-.54-.282-1.117-.282-1.71 0-.593.102-1.17.282-1.71V4.958H.957C.347 6.173 0 7.548 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z"/>
                            <path fill="#EA4335" d="M9.003 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.464.891 11.426 0 9.003 0 5.482 0 2.438 2.017.96 4.958L3.967 7.29c.708-2.127 2.692-3.71 5.036-3.71z"/>
                        </svg>
                        <span>Google</span>
                    </button>
                    
                    <button type="button" class="social-btn facebook-login-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span>Facebook</span>
                    </button>
                </div>
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