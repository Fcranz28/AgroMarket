@extends('layouts.app')

@push('styles')
<meta name="stripe-key" content="{{ env('STRIPE_KEY') }}">
@endpush

@section('content')
<div class="checkout-container">
    <h1>Finalizar Compra</h1>
    
    <div class="checkout-grid">
        <!-- Left Column: Customer Info + Payment -->
        <div class="checkout-left">
            <!-- Customer Information Card -->
            <div class="info-card">
                <h2>Información del Cliente</h2>
                
                <form id="checkoutForm">
                    @csrf
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="guest_name">Nombres</label>
                            <input type="text" id="guest_name" name="guest_name" required 
                                value="{{ $user?->firstname ?? '' }}" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="guest_lastname">Apellidos</label>
                            <input type="text" id="guest_lastname" name="guest_lastname" required 
                                value="{{ $user?->lastname ?? '' }}" placeholder="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="guest_email">Correo Electrónico</label>
                        <input type="email" id="guest_email" name="guest_email" required 
                            value="{{ $user?->email ?? '' }}" placeholder="">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="document_type">Tipo de Documento</label>
                            <select id="document_type" name="document_type" required>
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="Carnet de Extranjeria">Carnet de Extranjería</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="document_number">Número de Documento</label>
                            <input type="text" id="document_number" name="document_number" required 
                                placeholder="Ej: 12345678">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="shipping_address">Dirección de Envío</label>
                        <textarea id="shipping_address" name="shipping_address" required rows="2" 
                            placeholder=""></textarea>
                    </div>

                    <div class="form-group">
                        <label for="phone">Teléfono</label>
                        <input type="tel" id="phone" name="phone" required 
                            placeholder="Ej: 987654321">
                    </div>
                </form>
            </div>

            <!-- Payment Method Card -->
            <div class="info-card payment-card" id="payment-section">
                <h2>Método de Pago</h2>
                
                <div class="payment-tabs">
                    <button type="button" class="payment-tab active" data-method="card">
                        Tarjeta Crédito/Débito
                    </button>
                    <button type="button" class="payment-tab" data-method="mercadopago">
                        Mercado Pago
                    </button>
                </div>

                <div class="payment-content">
                    <!-- Card Payment -->
                    <div class="payment-method active" data-method="card">
                        <div class="visa-logo">
                            <svg width="60" height="20" viewBox="0 0 60 20" fill="none">
                                <path d="M24.6 14.7L26.8 5.3H29.4L27.2 14.7H24.6Z" fill="#1A1F71"/>
                                <path d="M37.8 5.5C37.3 5.3 36.5 5.1 35.5 5.1C32.9 5.1 31.1 6.4 31.1 8.3C31.1 9.7 32.4 10.5 33.4 11C34.4 11.5 34.8 11.8 34.8 12.3C34.8 13 34 13.3 33.2 13.3C32.1 13.3 31.5 13.1 30.7 12.7L30.4 12.5L30.1 14.4C30.7 14.7 31.8 15 33 15C35.7 15 37.5 13.7 37.5 11.7C37.5 10.6 36.7 9.8 35 9.1C34.1 8.7 33.6 8.4 33.6 7.9C33.6 7.5 34.1 7 35.1 7C35.9 7 36.5 7.2 37 7.4L37.3 7.5L37.6 5.6L37.8 5.5Z" fill="#1A1F71"/>
                                <path d="M41.6 11.8C41.8 11.2 42.7 8.8 42.7 8.8C42.7 8.8 42.9 8.2 43 7.9L43.2 8.9C43.2 8.9 43.7 11.2 43.8 11.8H41.6ZM44.8 5.3H42.8C42.2 5.3 41.7 5.5 41.4 6.1L37.7 14.7H40.4C40.4 14.7 40.9 13.3 41 13H44.3C44.4 13.3 44.6 14.7 44.6 14.7H47L44.8 5.3Z" fill="#1A1F71"/>
                                <path d="M21.5 5.3L19 12.2L18.7 10.8C18.2 9.3 16.7 7.7 15 6.8L17.3 14.7H20L24.2 5.3H21.5Z" fill="#1A1F71"/>
                                <path d="M15.8 5.3H11.4L11.4 5.5C14.5 6.2 16.7 8.1 17.5 10.8L16.7 6.1C16.6 5.5 16.1 5.3 15.8 5.3Z" fill="#F7981D"/>
                            </svg>
                            <span>Tarjeta de Crédito/Débito</span>
                        </div>
                        
                        <div id="payment-element">
                            <!-- Stripe Elements will be inserted here -->
                        </div>

                        <button type="submit" id="submit-payment" form="checkoutForm" class="btn-pay">
                            <span id="payment-spinner" class="spinner" style="display: none;"></span>
                            <span id="button-text">Pagar ahora</span>
                        </button>

                        <div class="security-badge">
                            <svg width="12" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z"/>
                            </svg>
                            Pagos seguros encriptados por Stripe
                        </div>
                    </div>

                    <!-- Mercado Pago (Placeholder) -->
                    <div class="payment-method" data-method="mercadopago">
                        <p class="coming-soon">Mercado Pago estará disponible próximamente</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Order Summary -->
        <div class="checkout-right">
            <div class="order-summary">
                <h2>Resumen del Pedido</h2>
                
                <div id="cart-items"></div>

                <div class="summary-breakdown">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal-amount">S/. 0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>IGV (18%)</span>
                        <span id="tax-amount">S/. 0.00</span>
                    </div>
                    <div class="summary-row total-row">
                        <span>Total</span>
                        <span id="total-amount">S/. 0.00</span>
                    </div>
                </div>
            </div>
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
        margin-bottom: 40px;
        color: #333;
        font-size: 2rem;
        font-weight: 600;
    }

    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 30px;
    }

    /* Left Column */
    .checkout-left {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .info-card {
        background: white;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .info-card h2 {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0 0 20px 0;
        color: #333;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #555;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9375rem;
        color: #333;
        background: #fff;
        transition: border-color 0.2s;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: #E53935;
    }

    .form-group textarea {
        resize: vertical;
        font-family: inherit;
    }

    /* Payment Tabs */
    .payment-tabs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 24px;
    }

    .payment-tab {
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: white;
        color: #666;
        font-size: 0.9375rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .payment-tab:hover {
        border-color: #E53935;
        color: #E53935;
    }

    .payment-tab.active {
        background: #E53935;
        color: white;
        border-color: #E53935;
    }

    .payment-content {
        position: relative;
    }

    .payment-method {
        display: none;
    }

    .payment-method.active {
        display: block;
    }

    .visa-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #eee;
    }

    .visa-logo span {
        font-size: 0.9375rem;
        font-weight: 500;
        color: #333;
    }

    #payment-element {
        margin-bottom: 20px;
    }

    .btn-pay {
        width: 100%;
        padding: 14px;
        background: #E53935;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-pay:hover:not(:disabled) {
        background: #D32F2F;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(229, 57, 53, 0.3);
    }

    .btn-pay:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .security-badge {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #eee;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8125rem;
        color: #666;
    }

    .security-badge svg {
        color: #4CAF50;
    }

    .coming-soon {
        text-align: center;
        padding: 40px 20px;
        color: #999;
        font-size: 0.9375rem;
    }

    /* Right Column - Order Summary */
    .order-summary {
        background: #F9F9F9;
        border-radius: 8px;
        padding: 24px;
        position: sticky;
        top: 100px;
    }

    .order-summary h2 {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0 0 20px 0;
        color: #333;
    }

    #cart-items {
        margin-bottom: 20px;
    }

    .cart-item {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e5e5;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .cart-item img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        background: white;
    }

    .cart-item-details {
        flex: 1;
    }

    .cart-item-name {
        font-size: 0.875rem;
        font-weight: 500;
        color: #333;
        margin-bottom: 4px;
    }

    .cart-item-quantity {
        font-size: 0.8125rem;
        color: #666;
    }

    .cart-item-price {
        font-size: 0.875rem;
        font-weight: 600;
        color: #333;
        text-align: right;
    }

    .summary-breakdown {
        border-top: 1px solid #e5e5e5;
        padding-top: 16px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 0.9375rem;
        color: #666;
    }

    .summary-row.total-row {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 2px solid #ddd;
        font-size: 1.125rem;
        font-weight: 700;
        color: #333;
    }

    .summary-row.total-row span:last-child {
        color: #E53935;
    }

    /* Spinner */
    .spinner {
        border: 2px solid #f3f3f3;
        border-top-color: #E53935;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
        display: inline-block;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }

        .checkout-right {
            order: -1;
        }

        .order-summary {
            position: static;
        }

        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    // Display cart summary with tax calculation
    function displayCartSummary() {
        const cart = JSON.parse(localStorage.getItem('carrito') || '[]');
        const cartItems = document.getElementById('cart-items');
        const subtotalElement = document.getElementById('subtotal-amount');
        const taxElement = document.getElementById('tax-amount');
        const totalElement = document.getElementById('total-amount');
        
        if (cart.length === 0) {
            window.location.href = '/productos';
            return;
        }

        let subtotal = 0;
        cartItems.innerHTML = '';
        
        cart.forEach(item => {
            const itemPrice = parseFloat(item.price);
            const itemTotal = itemPrice * item.cantidad;
            subtotal += itemTotal;
            
            // Get image path
            let image = '/img/placeholder.png';
            if (item.image_path) {
                image = `/storage/${item.image_path}`;
            } else if (item.image_url) {
                image = item.image_url;
            } else if (item.image) {
                image = item.image;
            }

            const itemDiv = document.createElement('div');
            itemDiv.className = 'cart-item';
            itemDiv.innerHTML = `
                <img src="${image}" alt="${item.name}">
                <div class="cart-item-details">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-quantity">Cant: ${item.cantidad}</div>
                </div>
                <div class="cart-item-price">S/. ${itemTotal.toFixed(2)}</div>
            `;
            cartItems.appendChild(itemDiv);
        });

        // Calculate tax (18% IGV)
        const tax = subtotal * 0.18;
        const total = subtotal + tax;

        subtotalElement.textContent = `S/. ${subtotal.toFixed(2)}`;
        taxElement.textContent = `S/. ${tax.toFixed(2)}`;
        totalElement.textContent = `S/. ${total.toFixed(2)}`;
    }

    // Payment tab switching
    function initPaymentTabs() {
        const tabs = document.querySelectorAll('.payment-tab');
        const methods = document.querySelectorAll('.payment-method');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const method = tab.getAttribute('data-method');
                
                // Update active tab
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // Update active method
                methods.forEach(m => m.classList.remove('active'));
                document.querySelector(`.payment-method[data-method="${method}"]`).classList.add('active');
            });
        });
    }

    // Run on DOM ready or immediately if already loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            displayCartSummary();
            initPaymentTabs();
        });
    } else {
        displayCartSummary();
        initPaymentTabs();
    }
</script>
@endsection
