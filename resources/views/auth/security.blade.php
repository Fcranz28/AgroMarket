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
            <x-ui.alert type="success">{{ $statusMessages[session('status')] }}</x-ui.alert>
        @endif

        <div class="flex items-center justify-center gap-2 text-sm">
            Estado:
            @if ($isConfirmed)
                <x-ui.badge color="success">Activada</x-ui.badge>
            @elseif ($isEnabled)
                <x-ui.badge color="warning">Pendiente de confirmar</x-ui.badge>
            @else
                <x-ui.badge color="neutral">Desactivada</x-ui.badge>
            @endif
        </div>

        @if (! $isEnabled)
            <form method="POST" action="{{ route('two-factor.enable') }}">
                @csrf
                <x-ui.button type="submit" size="lg" class="w-full">Activar</x-ui.button>
            </form>
        @elseif (! $isConfirmed)
            <div class="mx-auto rounded-xl bg-white p-3">{!! $user->twoFactorQrCodeSvg() !!}</div>

            <form method="POST" action="{{ route('two-factor.confirm') }}" class="flex flex-col gap-4">
                @csrf
                <x-ui.input name="code" label="Código de la app" bag="confirmTwoFactorAuthentication" inputmode="numeric" autocomplete="one-time-code" required />
                <x-ui.button type="submit" size="lg" class="w-full">Confirmar</x-ui.button>
            </form>
        @else
            <p class="text-sm text-muted">Guarda estos códigos de recuperación en un lugar seguro. Cada uno sirve una sola vez si pierdes tu teléfono.</p>
            <ul class="grid grid-cols-2 gap-2 rounded-xl bg-canvas p-4 font-mono text-sm text-ink">
                @foreach ($user->recoveryCodes() as $recoveryCode)
                    <li>{{ $recoveryCode }}</li>
                @endforeach
            </ul>

            <form method="POST" action="{{ route('two-factor.regenerate-recovery-codes') }}" class="text-center">
                @csrf
                <button type="submit" class="text-sm font-medium text-brand hover:underline">Generar nuevos códigos</button>
            </form>
        @endif

        @if ($isEnabled)
            <form method="POST" action="{{ route('two-factor.disable') }}" class="text-center">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm font-medium text-danger hover:underline">Desactivar verificación en dos pasos</button>
            </form>
        @endif
    </x-auth-card>
@endsection
