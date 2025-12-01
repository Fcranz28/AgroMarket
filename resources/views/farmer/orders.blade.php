@extends('layouts.dashboard')

@section('title', 'Mis Pedidos')
@section('header', 'Gestión de Pedidos')

@section('content')
<div class="farmer-orders-container">
    <!-- Stats Cards -->
    <div class="farmer-stats-grid">
        <div class="farmer-stat-card">
            <div class="farmer-stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="farmer-stat-content">
                <h3>{{ $pendingOrders->count() }}</h3>
                <p>Pedidos Pendientes</p>
            </div>
        </div>
        
        <div class="farmer-stat-card">
            <div class="farmer-stat-icon total">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="farmer-stat-content">
                <h3>{{ $orders->count() }}</h3>
                <p>Total de Pedidos</p>
            </div>
        </div>
        
        <div class="farmer-stat-card">
            <div class="farmer-stat-icon revenue">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="farmer-stat-content">
                <h3>S/. {{ number_format($orders->sum('total'), 2) }}</h3>
                <p>Ventas Totales</p>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="farmer-orders-section">
        <div class="farmer-section-header">
            <h2>Pedidos Recientes</h2>
            <div class="farmer-filters">
                <button class="farmer-filter-btn active" data-filter="all">Todos</button>
                <button class="farmer-filter-btn" data-filter="pending">Pendientes</button>
                <button class="farmer-filter-btn" data-filter="processing">En Proceso</button>
                <button class="farmer-filter-btn" data-filter="shipped">Enviados</button>
            </div>
        </div>

        @if($orders->count() > 0)
            <div class="farmer-orders-grid">
                @foreach($orders as $order)
                    <div class="farmer-order-card" data-status="{{ $order->status }}">
                        <div class="farmer-order-header">
                            <div class="farmer-order-info">
                                <h3>Pedido #{{ $order->id }}</h3>
                                <span class="farmer-order-date">
                                    <i class="fas fa-calendar"></i>
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </span>
                            </div>
                            <span class="farmer-status-badge status-{{ $order->status }}">
                                @switch($order->status)
                                    @case('pending')
                                        Pendiente
                                        @break
                                    @case('processing')
                                        En Proceso
                                        @break
                                    @case('shipped')
                                        Enviado
                                        @break
                                    @case('delivered')
                                        Entregado
                                        @break
                                    @default
                                        {{ $order->status }}
                                @endswitch
                            </span>
                        </div>

                        <div class="farmer-customer-info">
                            <div class="farmer-customer-avatar">
                                {{ substr($order->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="farmer-customer-name">{{ $order->user->name }}</p>
                                <p class="farmer-customer-email">{{ $order->user->email }}</p>
                            </div>
                        </div>

                        <div class="farmer-order-items">
                            <h4>Productos de tu tienda:</h4>
                            @foreach($order->items as $item)
                                @if($item->product->user_id == auth()->id())
                                    <div class="farmer-order-item">
                                        <img src="{{ $item->product->image_path ? Storage::url($item->product->image_path) : asset('img/placeholder.png') }}" 
                                             alt="{{ $item->product->name }}">
                                        <div class="farmer-item-details">
                                            <p class="farmer-item-name">{{ $item->product->name }}</p>
                                            <p class="farmer-item-quantity">Cantidad: {{ $item->quantity }} x S/. {{ number_format($item->price, 2) }}</p>
                                        </div>
                                        <div class="farmer-item-total">
                                            S/. {{ number_format($item->price * $item->quantity, 2) }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="farmer-order-footer">
                            <div class="farmer-order-total">
                                <span>Total del pedido:</span>
                                <strong>S/. {{ number_format($order->total, 2) }}</strong>
                            </div>
                            <div class="farmer-order-actions">
                                <a href="{{ route('farmer.orders.show', $order) }}" class="farmer-btn farmer-btn-outline">
                                    <i class="fas fa-eye"></i> Ver Detalles
                                </a>
                                @if(in_array($order->status, ['pending', 'processing']))
                                    <button class="farmer-btn farmer-btn-primary" onclick="updateOrderStatus('{{ $order->uuid }}', '{{ $order->status }}')">
                                        <i class="fas fa-truck"></i> Actualizar Estado
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="farmer-empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h3>No tienes pedidos aún</h3>
                <p>Cuando los clientes compren tus productos, aparecerán aquí</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/farmer/orders.js'])
@endpush
