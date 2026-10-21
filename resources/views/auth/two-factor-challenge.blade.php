@extends('layouts.app')

@section('content')
    <x-auth-card
        title="Verificación en dos pasos"
        subtitle="Ingresa el código de 6 dígitos de tu app de autenticación"
        side-title="¿Perdiste tu teléfono?"
        side-text="Usa uno de tus códigos de recuperación en el segundo campo."
    >
        <form method="POST" action="{{ route('two-factor.login') }}" class="flex flex-col gap-4">
            @csrf
            <x-ui.input name="code" label="Código" inputmode="numeric" autocomplete="one-time-code" autofocus />
            <x-ui.input name="recovery_code" label="Código de recuperación (opcional)" autocomplete="off" />
            <x-ui.button type="submit" size="lg" class="w-full">Verificar</x-ui.button>
        </form>
    </x-auth-card>
@endsection
