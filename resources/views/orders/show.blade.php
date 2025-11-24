@extends('layouts.app')

@section('content')
<div class="order-details-container">
    <div class="order-header">
        <div>
            <a href="{{ route('orders.index') }}" class="back-link">
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

            @if($order->status === 'pending')
                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block">
                    <i class="fas fa-credit-card"></i>
                    Pagar Ahora
                </a>
            @endif
        </aside>
    </div>
</div>

<style>
    .order-details-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--border-color, #e0e0e0);
    }

    .back-link {
        color: var(--primary-color, #4caf50);
        text-decoration: none;
        font-size: 0.95rem;
        display: inline-block;
        margin-bottom: 10px;
        transition: opacity 0.2s;
    }

    .back-link:hover {
        opacity: 0.8;
    }

    .order-header h1 {
        margin: 10px 0 5px;
        color: var(--text-color, #333);
        font-size: 2rem;
    }

    .order-date {
        color: var(--text-color-light, #666);
        margin: 0;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-processing {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-shipped {
        background: #d4edda;
        color: #155724;
    }

    .status-delivered {
        background: #d4edda;
        color: #155724;
    }

    .order-content-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 30px;
    }

    .order-items-section {
        background: var(--card-background-color, #fff);
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .order-items-section h2 {
        margin: 0 0 20px;
        color: var(--text-color, #333);
        font-size: 1.5rem;
    }

    .items-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .item-card {
        display: flex;
        gap: 20px;
        padding: 20px;
        border: 1px solid var(--border-color, #e0e0e0);
        border-radius: 10px;
        transition: all 0.2s;
    }

    .item-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .item-card img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
    }

    .item-info {
        flex: 1;
    }

    .item-info h3 {
        margin: 0 0 10px;
        color: var(--text-color, #333);
        font-size: 1.1rem;
    }

    .item-description {
        color: var(--text-color-light, #666);
        font-size: 0.9rem;
        margin: 0 0 15px;
    }

    .item-meta {
        display: flex;
        gap: 20px;
        font-size: 0.9rem;
        color: var(--text-color-light, #666);
    }

    .item-total {
        display: flex;
        align-items: center;
        font-size: 1.2rem;
        color: var(--primary-color, #4caf50);
    }

    .order-summary-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
        height: fit-content;
        position: sticky;
        top: 100px;
    }

    .summary-card {
        background: var(--card-background-color, #fff);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .summary-card h3 {
        margin: 0 0 20px;
        color: var(--text-color, #333);
        font-size: 1.1rem;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color, #e0e0e0);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        color: var(--text-color, #333);
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px solid var(--border-color, #e0e0e0);
        font-size: 1.2rem;
        color: var(--primary-color, #4caf50);
    }

    .address-text {
        color: var(--text-color, #333);
        line-height: 1.6;
        margin: 10px 0;
    }

    .phone-text {
        color: var(--text-color-light, #666);
        margin: 15px 0 0;
    }

    .payment-status {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
        font-weight: 500;
        color: var(--text-color, #333);
    }

    .payment-status i {
        font-size: 1.5rem;
    }

    .payment-id {
        color: var(--text-color-light, #666);
        font-size: 0.85rem;
        margin: 10px 0 0;
        font-family: monospace;
    }

    .btn-block {
        width: 100%;
        text-align: center;
        padding: 15px;
        font-size: 1.05rem;
    }

    .btn {
        padding: 8px 20px;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--primary-color, #4caf50);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-color-dark, #45a049);
    }

    @media (max-width: 768px) {
        .order-content-grid {
            grid-template-columns: 1fr;
        }

        .order-summary-sidebar {
            position: static;
        }

        .item-card {
            flex-direction: column;
        }

        .item-card img {
            width: 100%;
            height: 200px;
        }
    }
</style>
@endsection
