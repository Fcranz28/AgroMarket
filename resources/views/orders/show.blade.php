@extends('layouts.app')

@section('content')
<div class="order-details-container">
    <div class="order-header">
        <div>
            <a href="{{ auth()->user()->isFarmer() ? route('farmer.orders') : route('orders.index') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver a Mis Pedidos
            </a>
            <h1>Pedido #{{ $order->id }}</h1>
            <p class="order-date">Realizado el {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <span class="status-badge status-{{ $order->status }}">
            {{ ucfirst($order->status) }}
        </span>
    </div>

    <div class="order-content-grid">
        <!-- Order Items -->
        <div class="order-items-section">
            <h2>Productos</h2>
            <div class="items-list">
                @foreach($order->items as $item)
                    <div class="item-card">
                        <img src="{{ $item->product->image_path ? Storage::url($item->product->image_path) : asset('img/placeholder.png') }}" 
                             alt="{{ $item->product->name }}">
                        <div class="item-info">
                            <h3>{{ $item->product->name }}</h3>
                            <p class="item-description">{{ Str::limit($item->product->description, 100) }}</p>
                            <div class="item-meta">
                                <span class="quantity">Cantidad: {{ $item->quantity }}</span>
                                <span class="unit-price">S/. {{ number_format($item->price, 2) }} c/u</span>
                            </div>
                        </div>
                        <div class="item-total">
                            <strong>S/. {{ number_format($item->price * $item->quantity, 2) }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Order Summary Sidebar -->
        <aside class="order-summary-sidebar">
            <!-- Payment Info -->
            <div class="summary-card">
                <h3>Resumen del Pedido</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>S/. {{ number_format($order->total, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Envío</span>
                    <span>Gratis</span>
                </div>
                <div class="summary-total">
                    <strong>Total</strong>
                    <strong>S/. {{ number_format($order->total, 2) }}</strong>
                </div>
                @if($order->invoice)
                    <div class="invoice-actions" style="margin-top: 1rem; text-align: center;">
                        <a href="{{ route('invoice.download', $order->invoice) }}" class="btn-invoice-download" style="display: inline-block; background-color: #e74c3c; color: white; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                            <i class="fas fa-file-pdf"></i> Descargar Factura
                        </a>
                    </div>
                @endif
            </div>

            <!-- Shipping Info -->
            <div class="summary-card">
                <h3>Información de Envío</h3>
                <p><strong>Dirección:</strong></p>
                <p class="address-text">{{ $order->shipping_address }}</p>
                @if($order->phone)
                    <p class="phone-text">
                        <i class="fas fa-phone"></i> {{ $order->phone }}
                    </p>
                @endif
            </div>

            <!-- Payment Status -->
            <div class="summary-card">
                <h3>Estado de Pago</h3>
                <div class="payment-status">
                    @if($order->payment_status === 'paid')
                        <i class="fas fa-check-circle" style="color: #4caf50;"></i>
                        <span>Pagado</span>
                    @else
                        <i class="fas fa-clock" style="color: #ff9800;"></i>
                        <span>Pendiente de Pago</span>
                    @endif
                </div>
                @if($order->stripe_payment_intent_id)
                    <p class="payment-id">ID: {{ Str::limit($order->stripe_payment_intent_id, 20) }}</p>
                @endif
            </div>


        </aside>
    </div>
</div>

@push('styles')
{{-- Styles loaded via app.css → orders/show.css --}}
@endpush
@endsection
