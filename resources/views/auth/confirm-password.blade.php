@extends('layouts.app')

@section('content')
    <x-auth-card
        title="Confirma tu contraseña"
        subtitle="Por seguridad, vuelve a ingresar tu contraseña para continuar"
        side-title="Zona protegida"
        side-text="Esta acción cambia la seguridad de tu cuenta."
    >
        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="input-group">
                <input id="password" type="password" name="password" required autofocus autocomplete="current-password" />
                <label for="password">Contraseña</label>
            </div>
            @error('password')
                <div class="input-error">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn">Confirmar</button>
        </form>
    </x-auth-card>
@endsection
