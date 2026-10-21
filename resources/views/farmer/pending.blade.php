@extends('layouts.app')

@section('content')
    <x-ui.status-card title="¡Verificación en proceso!">
        <x-ui.badge color="warning" class="gap-2">
            <span class="size-2 animate-pulse rounded-full bg-current"></span>
            Pendiente de aprobación
        </x-ui.badge>

        <p class="text-muted">Tu cuenta de agricultor está siendo revisada por nuestro equipo. Este proceso nos ayuda a mantener la seguridad y calidad de nuestra comunidad.</p>

        <x-ui.alert type="info" class="text-left">Te avisaremos por correo cuando tu cuenta sea aprobada para que puedas empezar a vender.</x-ui.alert>

        <x-ui.button variant="secondary" :href="route('home')">← Volver al inicio</x-ui.button>
    </x-ui.status-card>
@endsection
