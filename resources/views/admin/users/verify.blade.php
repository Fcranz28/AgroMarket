@extends('layouts.dashboard')

@php
    $documents = [
        'face_photo' => 'Foto de rostro (selfie)',
        'dni_front' => 'DNI (frontal)',
        'dni_back' => 'DNI (reverso)',
    ];

    // Resultado de la consulta cuando el formulario se envió sin JavaScript
    $initialResult = session('apiResult');
@endphp

@section('title', 'Verificación de agricultor')
@section('header', 'Verificación de agricultor')

@section('content')
    <a href="{{ route('admin.users') }}" class="text-sm font-medium text-brand hover:underline">← Volver a usuarios</a>

    <div class="grid gap-6 xl:grid-cols-2">
        <x-ui.card class="flex flex-col gap-5">
            <div class="flex items-center gap-4">
                <span class="flex size-14 items-center justify-center rounded-full bg-brand text-lg font-semibold text-white">{{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}</span>
                <div class="min-w-0">
                    <h2 class="truncate text-xl font-semibold text-ink">{{ $user->name }}</h2>
                    <p class="truncate text-sm text-muted">{{ $user->email }} · {{ $user->phone ?? 'Sin teléfono' }}</p>
                    <x-ui.badge color="warning" class="mt-1">{{ $user->verification_status->label() }}</x-ui.badge>
                </div>
            </div>

            <h3 class="border-b border-line pb-2 font-semibold text-ink">Evidencias presentadas</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($documents as $document => $label)
                    <figure @class(['flex flex-col gap-2', 'sm:col-span-2' => $document === 'face_photo'])>
                        <figcaption class="text-sm font-medium text-muted">{{ $label }}</figcaption>
                        @if ($user->{$document})
                            <a href="{{ route('admin.verification.document', [$user, $document]) }}" target="_blank" class="block overflow-hidden rounded-xl border border-line">
                                <img src="{{ route('admin.verification.document', [$user, $document]) }}" alt="{{ $label }}" class="max-h-72 w-full object-contain">
                            </a>
                        @else
                            <p class="rounded-xl border border-dashed border-line p-6 text-center text-sm text-muted">Sin imagen</p>
                        @endif
                    </figure>
                @endforeach
            </div>
        </x-ui.card>

        <div class="flex flex-col gap-6">
            <x-ui.card
                class="flex flex-col gap-4"
                x-data="{
                    dni: @js(old('dni', $user->dni)),
                    loading: false,
                    result: @js($initialResult),
                    async check() {
                        if (! /^\d{8}$/.test(this.dni)) {
                            this.result = { success: false, message: 'Ingresa un DNI válido de 8 dígitos.' };
                            return;
                        }
                        this.loading = true;
                        try {
                            const response = await fetch(@js(route('admin.check.dni')), {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                                body: JSON.stringify({ dni: this.dni, user_id: {{ $user->id }} }),
                            });
                            this.result = await response.json();
                        } catch (error) {
                            this.result = { success: false, message: 'Error de conexión. Inténtalo nuevamente.' };
                        } finally {
                            this.loading = false;
                        }
                    },
                }"
            >
                <h3 class="font-semibold text-ink">Validación con RENIEC</h3>
                <x-ui.alert type="info">Consulta el DNI para comparar los datos oficiales con las evidencias.</x-ui.alert>

                <form method="POST" action="{{ route('admin.check.dni') }}" x-on:submit.prevent="check" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <label for="dni_input" class="sr-only">Número de DNI</label>
                    <input id="dni_input" name="dni" x-model="dni" inputmode="numeric" maxlength="8" pattern="[0-9]{8}" placeholder="8 dígitos" required class="min-w-0 flex-1 rounded-xl border border-line bg-surface px-4 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/25 focus:outline-none">
                    <x-ui.button type="submit" x-bind:disabled="loading"><i class="fas" x-bind:class="loading ? 'fa-spinner fa-spin' : 'fa-search'"></i> Consultar</x-ui.button>
                </form>

                <template x-if="result && result.success">
                    <dl class="grid gap-3 rounded-xl border border-success/30 bg-success/10 p-4 text-sm sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <dt class="text-muted">Nombre completo</dt>
                            <dd class="font-semibold text-ink" x-text="[result.data.first_name, result.data.first_last_name, result.data.second_last_name].join(' ')"></dd>
                        </div>
                        <div>
                            <dt class="text-muted">DNI</dt>
                            <dd class="font-semibold text-ink" x-text="result.data.document_number"></dd>
                        </div>
                        <div>
                            <dt class="text-muted">Estado</dt>
                            <dd class="font-semibold text-success"><i class="fas fa-check-circle"></i> Validado</dd>
                        </div>
                    </dl>
                </template>
                <template x-if="result && ! result.success">
                    <x-ui.alert type="error"><strong>Error en la consulta:</strong> <span x-text="result.message"></span></x-ui.alert>
                </template>
            </x-ui.card>

            <x-ui.card class="flex flex-col gap-4">
                <h3 class="font-semibold text-ink">Decisión</h3>
                <div class="grid gap-3 sm:grid-cols-2">
                    <form method="POST" action="{{ route('admin.verify', ['user' => $user->id, 'status' => 'approved']) }}">
                        @csrf
                        <x-ui.button type="submit" size="lg" class="w-full"><i class="fas fa-check-circle"></i> Aprobar</x-ui.button>
                    </form>
                    <form method="POST" action="{{ route('admin.verify', ['user' => $user->id, 'status' => 'rejected']) }}" x-data x-on:submit.prevent="Swal.fire({ title: '¿Rechazar la solicitud?', text: 'El agricultor recibirá un correo con la decisión.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Rechazar', cancelButtonText: 'Cancelar', confirmButtonColor: '#c53030' }).then((result) => result.isConfirmed && $el.submit())">
                        @csrf
                        <x-ui.button type="submit" variant="danger" size="lg" class="w-full"><i class="fas fa-times-circle"></i> Rechazar</x-ui.button>
                    </form>
                </div>
            </x-ui.card>
        </div>
    </div>
@endsection
