@extends('layouts.app')

@if (! $downloadUrl)
    @push('styles')
        {{-- The receipt is issued by a queue worker: reload until it is ready --}}
        <meta http-equiv="refresh" content="5">
    @endpush
@endif

@section('content')
    @if ($downloadUrl)
        <x-ui.status-card title="¡Gracias por tu compra!" icon="check" tone="success">
            <p class="text-muted">Tu pago fue confirmado y tu comprobante está listo.</p>
            <x-ui.button size="lg" :href="$downloadUrl">Descargar comprobante</x-ui.button>
            <a href="{{ route('home') }}" class="text-sm font-medium text-brand hover:underline">Volver a la tienda</a>
        </x-ui.status-card>
    @else
        <x-ui.status-card title="Estamos generando tu comprobante">
            <x-ui.badge color="warning" class="gap-2">
                <span class="size-2 animate-pulse rounded-full bg-current"></span>
                En proceso
            </x-ui.badge>
            <p class="text-muted">Tu pago fue confirmado. En unos segundos aparecerá el botón para descargar tu comprobante; puedes dejar esta página abierta.</p>
            <a href="{{ route('home') }}" class="text-sm font-medium text-brand hover:underline">Volver a la tienda</a>
        </x-ui.status-card>
    @endif
@endsection
