@extends('layouts.dashboard')

@section('title', 'Panel de Agricultor')
@section('header', 'Mi Resumen')

@section('content')
    <div class="farmer-dashboard-wrapper">
        <!-- Stats Grid -->
        <div class="farmer-stats-grid">
            <!-- Card 1: Mis Productos -->
            <div class="farmer-stat-card">
                <div class="farmer-stat-content">
                    <div class="farmer-stat-label">MIS PRODUCTOS</div>
                    <div class="farmer-stat-value">{{ $products }}</div>
                </div>
                <div class="farmer-stat-icon icon-success">
                    <i class="fas fa-box-open"></i>
                </div>
            </div>

            <!-- Card 2: Ventas Totales -->
            <div class="farmer-stat-card">
                <div class="farmer-stat-content">
                    <div class="farmer-stat-label">VENTAS TOTALES</div>
                    <div class="farmer-stat-value">{{ $sales }}</div>
                </div>
                <div class="farmer-stat-icon icon-info">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>

            <!-- Card 3: Ingresos Estimados -->
            <div class="farmer-stat-card">
                <div class="farmer-stat-content">
                    <div class="farmer-stat-label">INGRESOS ESTIMADOS</div>
                    <div class="farmer-stat-value">S/. {{ number_format($revenue, 2) }}</div>
                </div>
                <div class="farmer-stat-icon icon-warning">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>

            <!-- Card 4: Valoración Promedio -->
            <div class="farmer-stat-card">
                <div class="farmer-stat-content">
                    <div class="farmer-stat-label">VALORACIÓN PROMEDIO</div>
                    <div class="farmer-stat-value">4.8</div>
                </div>
                <div class="farmer-stat-icon icon-danger">
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="farmer-charts-grid">
            <!-- Monthly Sales Bar Chart -->
            <div class="farmer-chart-card">
                <div class="farmer-chart-header">
                    <div>
                        <h3 class="farmer-chart-title">Ventas Mensuales</h3>
                        <p class="farmer-chart-subtitle">Resumen de ventas del año</p>
                    </div>
                </div>
                <div id="monthlySalesChart"></div>
            </div>

            <!-- Monthly Target Donut Chart -->
            <div class="farmer-chart-card">
                <div class="farmer-chart-header">
                    <h3 class="farmer-chart-title">Objetivo Mensual</h3>
                    <div style="cursor:pointer;"><i class="fas fa-ellipsis-h" style="color:var(--text-secondary);"></i></div>
                </div>
                <div id="monthlyTargetChart" class="farmer-chart-target-container"></div>
                <div class="farmer-target-info">
                    <div class="farmer-target-item">
                        <p>Objetivo</p>
                        <h4>S/. 20k</h4>
                    </div>
                    <div class="farmer-target-item">
                        <p>Ingresos</p>
                        <h4>S/. 16k</h4>
                    </div>
                    <div class="farmer-target-item">
                        <p>Hoy</p>
                        <h4>S/. 1.5k</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products Section -->
        <div class="farmer-recent-products">
            <div class="section-header">
                <h3>Mis Productos Recientes</h3>
                <a href="{{ route('dashboard.productos.create') }}" class="btn-new-product">
                    <i class="fas fa-plus"></i> Nuevo Producto
                </a>
            </div>

            <div class="products-table-container">
                @if($recentProducts->count() > 0)
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentProducts as $product)
                                <tr>
                                    <td>
                                        <div class="product-cell">
                                            <div class="product-img-mini">
                                                @if($product->image_path)
                                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
                                                @else
                                                    <i class="fas fa-box"></i>
                                                @endif
                                            </div>
                                            <span>{{ $product->name }}</span>
                                        </div>
                                    </td>
                                    <td>S/. {{ number_format($product->price, 2) }}</td>
                                    <td>{{ $product->stock }} {{ $product->unit }}</td>
                                    <td>
                                        <span class="status-badge {{ $product->stock > 0 ? 'active' : 'inactive' }}">
                                            {{ $product->stock > 0 ? 'Disponible' : 'Agotado' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('dashboard.productos.edit', $product) }}" class="action-btn edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state-container">
                        <i class="fas fa-box empty-state-icon"></i>
                        <p>Gestiona tu inventario desde la sección "Mis Productos".</p>
                        <a href="{{ route('dashboard.productos.index') }}" class="empty-state-link">Ir a Mis Productos &rarr;</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        @vite(['resources/js/farmer/dashboard.js'])
    @endpush
@endsection
