@extends('layouts.app')

@section('content')
<div class="farmer-pending-container">
    <div class="farmer-pending-card">
        <div class="farmer-pending-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        
        <h1 class="farmer-pending-title">¡Verificación en Proceso!</h1>
        
        <div class="farmer-pending-badge">
            <span class="farmer-pending-dot"></span>
            Pendiente de Aprobación
        </div>

        <p class="farmer-pending-message">
            Tu cuenta de agricultor está siendo revisada por nuestro equipo. 
            Este proceso nos ayuda a mantener la seguridad y calidad de nuestra comunidad.
        </p>

        <div class="farmer-pending-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            <p>Te notificaremos por correo electrónico cuando tu cuenta haya sido aprobada para que puedas comenzar a vender.</p>
        </div>

        <a href="{{ route('home') }}" class="farmer-pending-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Volver al Inicio
        </a>
    </div>
</div>
@endsection
