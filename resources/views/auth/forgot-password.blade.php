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
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="input-group">
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" />
                <label for="email">Correo Electrónico</label>
            </div>
            @error('email')
                <div class="input-error">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn">Enviar enlace</button>
        </form>
    </x-auth-card>
@endsection
