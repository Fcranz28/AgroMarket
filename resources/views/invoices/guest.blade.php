@extends('layouts.app')

@if (! $downloadUrl)
    @push('styles')
        {{-- The receipt is issued by a queue worker: reload until it is ready --}}
        <meta http-equiv="refresh" content="5">
    @endpush
@endif

@section('content')
<div class="farmer-pending-container">
    <div class="farmer-pending-card">
        <div class="farmer-pending-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        @if ($downloadUrl)
            <h1 class="farmer-pending-title">¡Gracias por tu compra!</h1>

            <p class="farmer-pending-message">Tu pago fue confirmado y tu comprobante está listo.</p>

            <a href="{{ $downloadUrl }}" class="btn-invoice-download" style="display: inline-block; background-color: #e74c3c; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
                Descargar comprobante
            </a>
        @else
            <h1 class="farmer-pending-title">Estamos generando tu comprobante</h1>

            <div class="farmer-pending-badge">
                <span class="farmer-pending-dot"></span>
                En proceso
            </div>

            <p class="farmer-pending-message">
                Tu pago fue confirmado. En unos segundos aparecerá el botón para descargar tu comprobante;
                puedes dejar esta página abierta.
            </p>
        @endif

        <p class="farmer-pending-message">
            <a href="{{ route('home') }}">Volver a la tienda</a>
        </p>
    </div>
</div>
@endsection
