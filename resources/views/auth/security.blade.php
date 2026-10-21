@extends('layouts.app')

@php
    $user = auth()->user();
    $isEnabled = filled($user->two_factor_secret);
    $isConfirmed = filled($user->two_factor_confirmed_at);
    $statusMessages = [
        'two-factor-authentication-enabled' => 'Escanea el código QR con tu app de autenticación y confirma con un código.',
        'two-factor-authentication-confirmed' => 'La verificación en dos pasos quedó activada.',
        'two-factor-authentication-disabled' => 'La verificación en dos pasos quedó desactivada.',
        'recovery-codes-generated' => 'Generamos nuevos códigos de recuperación.',
    ];
@endphp

@section('content')
    <x-auth-card
        title="Verificación en dos pasos"
        subtitle="Además de tu contraseña, pediremos un código de tu app de autenticación (Google Authenticator, Authy...)."
        side-title="Protege tu cuenta"
        side-text="Recomendado para administradores y agricultores."
        :side-link="route('orders.index')"
        side-link-label="Volver a mi cuenta"
    >
        @if (isset($statusMessages[session('status')]))
            <div class="input-success" role="status">{{ $statusMessages[session('status')] }}</div>
        @endif

        @if (! $isEnabled)
            <form method="POST" action="{{ route('two-factor.enable') }}">
                @csrf
                <button type="submit" class="btn">Activar</button>
            </form>
        @elseif (! $isConfirmed)
            <div class="two-factor-qr">{!! $user->twoFactorQrCodeSvg() !!}</div>

            <form method="POST" action="{{ route('two-factor.confirm') }}">
                @csrf
                <div class="input-group">
                    <input id="code" type="text" name="code" inputmode="numeric" autocomplete="one-time-code" required />
                    <label for="code">Código de la app</label>
                </div>
                @error('code', 'confirmTwoFactorAuthentication')
                    <div class="input-error">{{ $message }}</div>
                @enderror
                <button type="submit" class="btn">Confirmar</button>
            </form>
        @else
            <span>Guarda estos códigos de recuperación en un lugar seguro. Cada uno sirve una sola vez si pierdes tu teléfono.</span>
            <code>
                @foreach ($user->recoveryCodes() as $recoveryCode)
                    {{ $recoveryCode }}<br>
                @endforeach
            </code>

            <form method="POST" action="{{ route('two-factor.regenerate-recovery-codes') }}">
                @csrf
                <button type="submit" class="link-button">Generar nuevos códigos</button>
            </form>
        @endif

        @if ($isEnabled)
            <form method="POST" action="{{ route('two-factor.disable') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="link-button">Desactivar verificación en dos pasos</button>
            </form>
        @endif
    </x-auth-card>
@endsection
