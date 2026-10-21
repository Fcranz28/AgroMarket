@extends('layouts.app')

@section('content')
    <x-auth-card
        title="Recuperar contraseña"
        subtitle="Te enviaremos un enlace para crear una nueva contraseña"
        side-title="¿La recordaste?"
        side-text="Vuelve a iniciar sesión con tu correo y contraseña."
        :side-link="route('login')"
        side-link-label="Iniciar sesión"
    >
        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4">
            @csrf
            <x-ui.input name="email" type="email" label="Correo electrónico" required autofocus autocomplete="email" />
            <x-ui.button type="submit" size="lg" class="w-full">Enviar enlace</x-ui.button>
        </form>
    </x-auth-card>
@endsection
