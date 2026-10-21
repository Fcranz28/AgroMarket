@extends('layouts.app')

@section('content')
    <x-auth-card
        title="Verifica tu correo"
        subtitle="Te enviamos un enlace de verificación. Ábrelo para continuar con tu cuenta."
        side-title="¿No te llegó?"
        side-text="Revisa tu carpeta de spam o pide un nuevo enlace."
    >
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-ui.button type="submit" size="lg" class="w-full">Reenviar enlace</x-ui.button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="text-sm font-medium text-muted hover:text-brand hover:underline">Cerrar sesión</button>
        </form>
    </x-auth-card>
@endsection
