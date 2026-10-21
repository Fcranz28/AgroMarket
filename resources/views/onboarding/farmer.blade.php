@extends('layouts.app')

@section('content')
    <div class="mx-auto flex max-w-3xl flex-col gap-6 pt-10 pb-16">
        <div class="flex items-center gap-3">
            <div class="h-2 flex-1 overflow-hidden rounded-full bg-line" role="progressbar" aria-valuenow="2" aria-valuemin="1" aria-valuemax="3">
                <div class="h-full w-1/2 rounded-full bg-brand"></div>
            </div>
            <span class="text-sm text-muted">Paso 2 de 3</span>
        </div>

        <x-ui.card class="p-8 sm:p-10">
            <header class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-ink">Verificación de identidad</h1>
                <p class="mt-2 text-muted">Para garantizar la seguridad de nuestra comunidad, necesitamos validar que eres un agricultor real.</p>
            </header>

            <form action="{{ route('onboarding.verification') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
                @csrf

                <x-ui.image-upload name="face_photo" label="1. Foto de tu rostro (selfie)" hint="Asegúrate de que tu rostro esté bien iluminado" required />

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-ui.image-upload name="dni_front" label="2. DNI (frontal)" hint="Que se lean todos los datos" required />
                    <x-ui.image-upload name="dni_back" label="3. DNI (reverso)" hint="Que se lean todos los datos" required />
                </div>

                <x-ui.alert type="info">
                    <strong>Tus datos están protegidos.</strong>
                    Solo los usamos para verificar tu identidad y asegurar la calidad de los productos en AgroMarket.
                </x-ui.alert>

                <x-ui.button type="submit" size="lg">Enviar solicitud →</x-ui.button>
            </form>
        </x-ui.card>
    </div>
@endsection
