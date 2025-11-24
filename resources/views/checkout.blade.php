@extends('layouts.app')

@push('styles')
<meta name="stripe-key" content="{{ env('STRIPE_KEY') }}">
@endpush

@section('content')
<div class="checkout-container">
    <h1>Finalizar Compra</h1>
    
    <div class="checkout-grid">
        <!-- Order Summary -->
        <div class="order-summary">
            <h2>Resumen del Pedido</h2>
            <div id="cart-summary"></div>
            <div class="total-section">
                <strong>Total:</strong>
                <span id="total-amount">S/. 0.00</span>
            </div>
        </div>

        <!-- Checkout Form -->
        <div class="checkout-form-section">
            <form id="checkoutForm">
                @csrf
                
                <h2>Información del Cliente</h2>
                
                <div class="form-row">
                    <div class="form-group half">
                        <label for="guest_name">Nombres</label>
                        <input type="text" id="guest_name" name="guest_name" required 
                            value="{{ $user?->firstname ?? '' }}" placeholder="Tus nombres">
                    </div>
                    <div class="form-group half">
                        <label for="guest_lastname">Apellidos</label>
                        <input type="text" id="guest_lastname" name="guest_lastname" required 
                            value="{{ $user?->lastname ?? '' }}" placeholder="Tus apellidos">
                    </div>
                </div>

                <div class="form-group">
                    <label for="guest_email">Correo Electrónico</label>
                    <input type="email" id="guest_email" name="guest_email" required 
                        value="{{ $user?->email ?? '' }}" placeholder="tu@email.com">
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="document_type">Tipo de Documento</label>
                        <select id="document_type" name="document_type" required>
                            <option value="DNI">DNI</option>
                            <option value="RUC">RUC</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="Carnet de Extranjeria">Carnet de Extranjería</option>
                        </select>
                    </div>
                    <div class="form-group half">
                        <label for="document_number">Nro de Documento</label>
                        <input type="text" id="document_number" name="document_number" required 
                            placeholder="Número de documento">
                    </div>
                </div>

                <h2>Información de Envío</h2>
                
                <div class="form-group">
                    <label for="shipping_address">Dirección de Envío</label>
                    <textarea id="shipping_address" name="shipping_address" required rows="3" 
                        placeholder="Ingresa tu dirección completa"></textarea>
                </div>

                <div class="form-group">
                    <label for="phone">Teléfono</label>
                    <input type="tel" id="phone" name="phone" required 
                        placeholder="Ej: 987654321">
                </div>

                <!-- Stripe Payment Element -->
                <div id="payment-section" style="display: none;">
                    <h2>Información de Pago</h2>
                    <p class="payment-info">Todos los pagos son procesados de forma segura por Stripe</p>
                    
                    <div id="payment-element">
                        <!-- Stripe Elements will be inserted here -->
                    </div>

                    <button type="submit" id="submit-payment" class="btn btn-primary">
                        <span id="payment-spinner" class="spinner" style="display: none;"></span>
                        <span id="button-text">Pagar ahora</span>
                    </button>
                </div>

                <div id="payment-loading" style="text-align: center; padding: 40px;">
                    <div class="spinner"></div>
                    <p>Cargando sistema de pagos...</p>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .checkout-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .checkout-container h1 {
        text-align: center;
        margin-bottom: 30px;
        color: var(--text-color, #333);
    }

    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 30px;
    }

    .order-summary {
        background: var(--card-background-color, #fff);
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        height: fit-content;
        position: sticky;
        top: 100px;
    }

    .order-summary h2 {
        font-size: 1.25rem;
        margin-bottom: 20px;
        color: var(--text-color, #333);
    }

    #cart-summary-item {
        padding: 10px 0;
        border-bottom: 1px solid var(--border-color, #eee);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .total-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid var(--primary-color, #4caf50);
        display: flex;
        justify-content: space-between;
        font-size: 1.25rem;
        font-weight: bold;
        color: var(--text-color, #333);
    }

    .checkout-form-section {
        background: var(--card-background-color, #fff);
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .checkout-form-section h2 {
        font-size: 1.25rem;
        margin-bottom: 20px;
        margin-top: 30px;
        color: var(--text-color, #333);
    }

    .checkout-form-section h2:first-of-type {
        margin-top: 0;
    }

    .form-row {
        display: flex;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group.half {
        flex: 1;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-color, #333);
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border-color, #ddd);
        border-radius: 8px;
        font-size: 1rem;
        background: var(--input-background-color, #fff);
        color: var(--text-color, #333);
    }

    .form-group textarea {
        resize: vertical;
    }

    #payment-element {
        margin: 20px 0;
        padding: 15px;
        border: 1px solid var(--border-color, #ddd);
        border-radius: 8px;
        background: var(--input-background-color, #fff);
    }

    .payment-info {
        color: var(--text-color-light, #666);
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    .btn-primary {
        width: 100%;
        padding: 15px;
        background: var(--primary-color, #4caf50);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 20px;
    }

    .btn-primary:hover:not(:disabled) {
        background: var(--primary-color-dark, #45a049);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .spinner {
        border: 2px solid #f3f3f3;
        border-top: 2px solid var(--primary-color, #4caf50);
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
        display: inline-block;
        vertical-align: middle;
        margin-right: 10px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }

        .order-summary {
            position: static;
        }
    }
</style>

<script>
    // Display cart summary
    document.addEventListener('DOMContentLoaded', () => {
        const cart = JSON.parse(localStorage.getItem('carrito') || '[]');
        const cartSummary = document.getElementById('cart-summary');
        const totalAmount = document.getElementById('total-amount');
        
        if (cart.length === 0) {
            window.location.href = '/productos';
            return;
        }

        let total = 0;
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            
            const itemDiv = document.createElement('div');
            itemDiv.className = 'cart-summary-item';
            itemDiv.innerHTML = `
                <div>
                    <strong>${item.name}</strong><br>
                    <small>Cantidad: ${item.quantity} x S/. ${item.price.toFixed(2)}</small>
                </div>
                <div>S/. ${itemTotal.toFixed(2)}</div>
            `;
            cartSummary.appendChild(itemDiv);
        });

        totalAmount.textContent = `S/. ${total.toFixed(2)}`;
    });
</script>
@endsection
