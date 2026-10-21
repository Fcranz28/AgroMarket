@extends('layouts.app')

@section('content')
    <x-auth-card
        title="Verificación en dos pasos"
        subtitle="Ingresa el código de 6 dígitos de tu app de autenticación"
        side-title="¿Perdiste tu teléfono?"
        side-text="Usa uno de tus códigos de recuperación en el segundo campo."
    >
        <form method="POST" action="{{ route('two-factor.login') }}">
            @csrf

            <div class="input-group">
                <input id="code" type="text" name="code" inputmode="numeric" autocomplete="one-time-code" autofocus />
                <label for="code">Código</label>
            </div>
            @error('code')
                <div class="input-error">{{ $message }}</div>
            @enderror

            <div class="input-group">
                <input id="recovery_code" type="text" name="recovery_code" autocomplete="off" />
                <label for="recovery_code">Código de recuperación (opcional)</label>
            </div>
            @error('recovery_code')
                <div class="input-error">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn">Verificar</button>
        </form>
    </x-auth-card>
@endsection
