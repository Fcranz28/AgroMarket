@extends('layouts.app')

@push('scripts')
    @vite(['resources/js/firebase-auth.js'])
@endpush

@section('content')
    <x-auth-card
        title="Iniciar sesión"
        subtitle="Usa tu correo y contraseña"
        side-text="Regístrate con tus datos personales para usar todas las funciones del sitio."
        :side-link="route('register')"
        side-link-label="Registrarse"
    >
        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
            @csrf

            <x-ui.input name="email" type="email" label="Correo electrónico" required autocomplete="email" autofocus />
            <x-ui.input name="password" type="password" label="Contraseña" required autocomplete="current-password" />

            <div class="flex flex-wrap items-center justify-between gap-2 text-sm">
                <label class="flex items-center gap-2 text-muted">
                    <input type="checkbox" name="remember" id="remember" @checked(old('remember')) class="accent-brand">
                    Recuérdame
                </label>
                <a href="{{ route('password.request') }}" class="font-medium text-brand hover:underline">¿Olvidaste tu contraseña?</a>
            </div>

            <x-ui.button type="submit" size="lg" class="w-full" data-test="login-submit">Iniciar sesión</x-ui.button>

            @include('auth.partials.social-login', ['label' => 'O continúa con'])
        </form>
    </x-auth-card>
@endsection
