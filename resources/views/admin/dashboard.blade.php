@extends('layouts.dashboard')

@section('title', 'Panel de Administración')
@section('header', 'Resumen General')

@section('content')
    {{-- Styles loaded via dashboard.css --}}

    <div class="admin-dashboard-wrapper">
        <!-- Top Stats Grid -->
        <div class="admin-dashboard-grid">
            <!-- Card 1: Total Views (Users) -->
            <div class="admin-stat-card">
                <div class="admin-stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="admin-stat-value">{{ number_format($totalUsers) }}</div>
                <div class="admin-stat-label">Vistas Totales</div>
                <div class="admin-stat-trend trend-up">
                    <i class="fas fa-arrow-up"></i> 0.43% <span class="admin-trend-label">vs semana pasada</span>
                </div>
            </div>

            <!-- Card 2: Total Profit -->
            <div class="admin-stat-card">
                <div class="admin-stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="admin-stat-value">S/. {{ number_format($totalRevenue, 2) }}</div>
                <div class="admin-stat-label">Ganancia Total</div>
                <div class="admin-stat-trend trend-up">
                    <i class="fas fa-arrow-up"></i> 4.35% <span class="admin-trend-label">vs semana pasada</span>
                </div>
            </div>

            <!-- Card 3: Total Products -->
            <div class="admin-stat-card">
                <div class="admin-stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="admin-stat-value">{{ number_format($totalProducts) }}</div>
                <div class="admin-stat-label">Productos Totales</div>
                <div class="admin-stat-trend trend-up">
                    <i class="fas fa-arrow-up"></i> 2.59% <span class="admin-trend-label">vs semana pasada</span>
                </div>
            </div>

            <!-- Card 4: Total Users (Farmers) -->
            <div class="admin-stat-card">
                <div class="admin-stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="admin-stat-value">{{ number_format($totalFarmers) }}</div>
                <div class="admin-stat-label">Agricultores</div>
                <div class="admin-stat-trend trend-down">
                    <i class="fas fa-arrow-down"></i> 0.95% <span class="admin-trend-label">vs semana pasada</span>
                </div>
            </div>
        </div>

        <!-- Middle Charts Grid -->
        <div class="admin-charts-grid">
            <!-- Weekly Sales Bar Chart -->
            <div class="admin-chart-card">
                <div class="admin-chart-header">
                    <div>
                        <h3 class="admin-chart-title">Ventas Semanales</h3>
                        <p class="admin-chart-subtitle">Resumen de ventas de la semana</p>
                    </div>
                </div>
                <div id="weeklySalesChart"></div>
            </div>

            <!-- Weekly Target Donut Chart -->
            <div class="admin-chart-card">
                <div class="admin-chart-header">
                    <h3 class="admin-chart-title">Objetivo Semanal</h3>
                    <div style="cursor:pointer;"><i class="fas fa-ellipsis-h" style="color:var(--text-light);"></i></div>
                </div>
                <div id="weeklyTargetChart" class="admin-chart-target-container"></div>
                <div class="admin-target-info">
                    <div class="admin-target-item">
                        <p>Objetivo</p>
                        <h4>S/. 20k</h4>
                    </div>
                    <div class="admin-target-item">
                        <p>Ingresos</p>
                        <h4>S/. 16k</h4>
                    </div>
                    <div class="admin-target-item">
                        <p>Hoy</p>
                        <h4>S/. 1.5k</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Statistics Chart -->
        <div class="admin-chart-card admin-full-width-chart">
            <div class="admin-chart-header">
                <div>
                    <h3 class="admin-chart-title">Estadísticas</h3>
                    <p class="admin-chart-subtitle">Ingresos vs Gastos</p>
                </div>
                <div class="admin-chart-actions">
                    <button class="admin-chart-btn active">Semana</button>
                    <button class="admin-chart-btn">Quincena</button>
                    <button class="admin-chart-btn">Mes</button>
                </div>
            </div>
            <div id="statisticsChart"></div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        @vite(['resources/js/admin/dashboard.js'])
    @endpush
@endsection
