@extends('layouts.app')

@section('content')
    <x-auth-card
        title="Nueva contraseña"
        subtitle="Elige una contraseña de al menos 8 caracteres"
        side-text="Después podrás iniciar sesión con tu nueva contraseña."
    >
        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <x-ui.input name="email" type="email" label="Correo electrónico" :value="$request->email" required autocomplete="email" />
            <x-ui.input name="password" type="password" label="Nueva contraseña" required autocomplete="new-password" />
            <x-ui.input name="password_confirmation" type="password" label="Confirmar contraseña" required autocomplete="new-password" />

            <x-ui.button type="submit" size="lg" class="w-full">Guardar contraseña</x-ui.button>
        </form>
    </x-auth-card>
@endsection
