@extends('layouts.dashboard')

@section('title', 'Panel de Agricultor')
@section('header', 'Mi Resumen')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Mis Productos</div>
            <div class="stat-value">{{ $products }}</div>
            <div class="stat-icon"><i class="fas fa-box-open" style="color: #28a745;"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Ventas Totales</div>
            <div class="stat-value">{{ $sales }}</div>
            <div class="stat-icon"><i class="fas fa-shopping-cart" style="color: #007bff;"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Ingresos Estimados</div>
            <div class="stat-value">S/. {{ number_format($revenue, 2) }}</div>
            <div class="stat-icon"><i class="fas fa-wallet" style="color: #ffc107;"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Valoración Promedio</div>
            <div class="stat-value">4.8</div>
            <div class="stat-icon"><i class="fas fa-star" style="color: #ff4747;"></i></div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h3 class="table-title">Mis Productos Recientes</h3>
            <a href="{{ route('dashboard.productos.create') }}" class="btn btn-sm btn-success" style="background:#28a745; border:none; color:white; padding:8px 15px; border-radius:4px; text-decoration:none;">
                <i class="fas fa-plus"></i> Nuevo Producto
            </a>
        </div>
        
        <!-- Here we could include a partial or a simple list -->
        <div style="text-align: center; padding: 20px; color: #888;">
            <i class="fas fa-box" style="font-size: 3rem; margin-bottom: 10px; opacity: 0.3;"></i>
            <p>Gestiona tu inventario desde la sección "Mis Productos".</p>
            <a href="{{ route('dashboard.productos.index') }}" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">Ir a Mis Productos &rarr;</a>
        </div>
    </div>
@endsection
