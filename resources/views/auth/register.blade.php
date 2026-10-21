@extends('layouts.app')

@push('scripts')
    @vite(['resources/js/firebase-auth.js'])
@endpush

@section('content')
    <x-auth-card
        title="Crear cuenta"
        subtitle="Usa tu correo para registrarte"
        side-title="¡Bienvenido!"
        side-text="Si ya tienes una cuenta, inicia sesión para ver tus pedidos."
        :side-link="route('login')"
        side-link-label="Iniciar sesión"
        side-first
    >
        <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
            @csrf

            <x-ui.input name="name" label="Nombre" required autocomplete="name" autofocus />
            <x-ui.input name="email" type="email" label="Correo electrónico" required autocomplete="email" />
            <x-ui.input name="password" type="password" label="Contraseña" hint="Al menos 8 caracteres." required autocomplete="new-password" />
            <x-ui.input name="password_confirmation" id="password-confirm" type="password" label="Confirmar contraseña" required autocomplete="new-password" />

            <x-ui.button type="submit" size="lg" class="w-full">Registrarse</x-ui.button>

            @include('auth.partials.social-login', ['label' => 'O regístrate con'])
        </form>
    </x-auth-card>
@endsection
