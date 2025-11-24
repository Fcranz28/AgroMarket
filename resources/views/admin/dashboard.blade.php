@extends('layouts.dashboard')

@section('title', 'Panel de Administración')
@section('header', 'Resumen General')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Agricultores Pendientes</div>
            <div class="stat-value">{{ $pendingFarmers }}</div>
            <div class="stat-icon"><i class="fas fa-user-clock" style="color: #ffc107;"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Agricultores Totales</div>
            <div class="stat-value">{{ $totalFarmers }}</div>
            <div class="stat-icon"><i class="fas fa-tractor" style="color: #28a745;"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Usuarios Registrados</div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-icon"><i class="fas fa-users" style="color: #17a2b8;"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Ingresos Totales (Est.)</div>
            <div class="stat-value">S/. 12,450</div>
            <div class="stat-icon"><i class="fas fa-dollar-sign" style="color: #dc3545;"></i></div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h3 class="table-title">Actividad Reciente</h3>
            <a href="{{ route('admin.users') }}" class="btn btn-sm btn-primary" style="background:var(--primary-color); border:none; color:white; padding:8px 15px; border-radius:4px; text-decoration:none;">Ver Todo</a>
        </div>
        <p style="color:#888;">No hay actividad reciente para mostrar.</p>
    </div>
@endsection
