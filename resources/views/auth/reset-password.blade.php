@extends('layouts.app')

@section('content')
    <x-auth-card
        title="Nueva contraseña"
        subtitle="Elige una contraseña de al menos 8 caracteres"
        side-text="Después podrás iniciar sesión con tu nueva contraseña."
    >
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="input-group">
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autocomplete="email" />
                <label for="email">Correo Electrónico</label>
            </div>
            @error('email')
                <div class="input-error">{{ $message }}</div>
            @enderror

            <div class="input-group">
                <input id="password" type="password" name="password" required autocomplete="new-password" />
                <label for="password">Nueva contraseña</label>
            </div>
            @error('password')
                <div class="input-error">{{ $message }}</div>
            @enderror

            <div class="input-group">
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
                <label for="password_confirmation">Confirmar contraseña</label>
            </div>

            <button type="submit" class="btn">Guardar contraseña</button>
        </form>
    </x-auth-card>
@endsection
