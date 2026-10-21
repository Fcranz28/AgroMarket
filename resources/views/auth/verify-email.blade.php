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
            <button type="submit" class="btn">Reenviar enlace</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="link-button">Cerrar sesión</button>
        </form>
    </x-auth-card>
@endsection
