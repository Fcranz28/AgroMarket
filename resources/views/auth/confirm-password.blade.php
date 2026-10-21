@extends('layouts.app')

@section('content')
    <x-auth-card
        title="Confirma tu contraseña"
        subtitle="Por seguridad, vuelve a ingresar tu contraseña para continuar"
        side-title="Zona protegida"
        side-text="Esta acción cambia la seguridad de tu cuenta."
    >
        <form method="POST" action="{{ route('password.confirm') }}" class="flex flex-col gap-4">
            @csrf
            <x-ui.input name="password" type="password" label="Contraseña" required autofocus autocomplete="current-password" />
            <x-ui.button type="submit" size="lg" class="w-full">Confirmar</x-ui.button>
        </form>
    </x-auth-card>
@endsection
