@extends('layouts.app')

@section('content')
<div class="container checkout-container">
    <h1 class="page-title">Finalizar Compra</h1>

    <div class="checkout-grid">
        <!-- Resumen del Pedido -->
        <div class="order-summary">
            <h3>Resumen del Pedido</h3>
            <div id="checkout-items" class="checkout-items">
                <!-- Items cargados vía JS -->
            </div>
            <div class="checkout-total">
                <span>Total a Pagar:</span>
                <span id="checkout-total-amount">S/. 0.00</span>
            </div>
        </div>

        <!-- Opciones de Pago -->
        <div class="payment-options">
            <h3>Método de Pago</h3>
            
            <div class="payment-tabs">
                <button class="payment-tab active" data-target="visa">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa">
                    Tarjeta Visa
                </button>
                <button class="payment-tab" data-target="mercadopago">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/d/d4/MercadoPago_logo.png" alt="Mercado Pago" style="height: 20px;">
                    Mercado Pago
                </button>
            </div>

            <div class="payment-content">
                <!-- Formulario Visa -->
                <div id="visa" class="payment-form active">
                    <form id="visa-form">
                        <div class="form-group">
                            <label>Número de Tarjeta</label>
                            <input type="text" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Fecha de Expiración</label>
                                <input type="text" class="form-control" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>CVV</label>
                                <input type="text" class="form-control" placeholder="123" maxlength="3" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nombre del Titular</label>
                            <input type="text" class="form-control" placeholder="Como aparece en la tarjeta" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Pagar con Visa</button>
                    </form>
                </div>

                <!-- Mercado Pago -->
                <div id="mercadopago" class="payment-form">
                    <p class="text-center mb-4">Serás redirigido a Mercado Pago para completar tu compra de forma segura.</p>
                    <button id="mp-button" class="btn btn-mp btn-block">
                        Pagar con Mercado Pago
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .checkout-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .page-title {
        text-align: center;
        margin-bottom: 2rem;
        color: #2d3748;
    }

    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    @media (max-width: 768px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }
    }

    .order-summary, .payment-options {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .checkout-items {
        margin-bottom: 1.5rem;
        max-height: 300px;
        overflow-y: auto;
    }

    .checkout-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .checkout-item img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 5px;
    }

    .checkout-item-details {
        flex: 1;
    }

    .checkout-item h4 {
        margin: 0;
        font-size: 0.9rem;
        color: #2d3748;
    }

    .checkout-total {
        display: flex;
        justify-content: space-between;
        font-size: 1.2rem;
        font-weight: bold;
        color: #2d3748;
        padding-top: 1rem;
        border-top: 2px solid #e2e8f0;
    }

    /* Payment Tabs */
    .payment-tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .payment-tab {
        flex: 1;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        background: #f8fafc;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }

    .payment-tab.active {
        border-color: #48bb78;
        background: #f0fff4;
        color: #2f855a;
    }

    .payment-tab img {
        height: 24px;
        object-fit: contain;
    }

    .payment-form {
        display: none;
    }

    .payment-form.active {
        display: block;
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
    }

    .btn-block {
        width: 100%;
        padding: 1rem;
        font-size: 1.1rem;
    }

    .btn-mp {
        background-color: #009ee3;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-mp:hover {
        background-color: #007eb5;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar items del carrito
        const cartItemsContainer = document.getElementById('checkout-items');
        const totalAmountElement = document.getElementById('checkout-total-amount');
        
        let cart = [];
        try {
            cart = JSON.parse(localStorage.getItem('carrito')) || [];
        } catch (e) {
            cart = [];
        }

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p class="text-center text-muted">Tu carrito está vacío.</p>';
            totalAmountElement.textContent = 'S/. 0.00';
        } else {
            let total = 0;
            cartItemsContainer.innerHTML = '';
            
            cart.forEach(item => {
                const itemTotal = item.price * item.cantidad;
                total += itemTotal;
                
                // Image logic matching carrito.js
                let image = '/img/placeholder.png';
                if (item.image_path) {
                    image = `/storage/${item.image_path}`;
                } else if (item.image_url) {
                    image = item.image_url;
                } else if (item.image) {
                    image = item.image;
                }

                const div = document.createElement('div');
                div.className = 'checkout-item';
                div.innerHTML = `
                    <img src="${image}" alt="${item.name}">
                    <div class="checkout-item-details">
                        <h4>${item.name}</h4>
                        <p class="text-muted small">${item.cantidad} x S/. ${Number(item.price).toFixed(2)}</p>
                    </div>
                    <div class="checkout-item-total">
                        S/. ${itemTotal.toFixed(2)}
                    </div>
                `;
                cartItemsContainer.appendChild(div);
            });
            
            totalAmountElement.textContent = `S/. ${total.toFixed(2)}`;
        }

        // Tabs Logic
        const tabs = document.querySelectorAll('.payment-tab');
        const forms = document.querySelectorAll('.payment-form');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all
                tabs.forEach(t => t.classList.remove('active'));
                forms.forEach(f => f.classList.remove('active'));

                // Add active class to clicked
                tab.classList.add('active');
                const targetId = tab.dataset.target;
                document.getElementById(targetId).classList.add('active');
            });
        });

        // Payment Simulation
        document.getElementById('visa-form').addEventListener('submit', (e) => {
            e.preventDefault();
            Swal.fire({
                title: 'Procesando Pago...',
                text: 'Validando tarjeta Visa',
                timer: 2000,
                didOpen: () => {
                    Swal.showLoading();
                }
            }).then(() => {
                Swal.fire({
                    title: '¡Pago Exitoso!',
                    text: 'Tu pedido ha sido confirmado.',
                    icon: 'success'
                }).then(() => {
                    localStorage.removeItem('carrito');
                    window.location.href = "{{ route('orders.index') }}";
                });
            });
        });

        document.getElementById('mp-button').addEventListener('click', () => {
            Swal.fire({
                title: 'Redirigiendo...',
                text: 'Conectando con Mercado Pago',
                timer: 2000,
                didOpen: () => {
                    Swal.showLoading();
                }
            }).then(() => {
                Swal.fire({
                    title: '¡Pago Exitoso!',
                    text: 'Tu pedido ha sido confirmado.',
                    icon: 'success'
                }).then(() => {
                    localStorage.removeItem('carrito');
                    window.location.href = "{{ route('orders.index') }}";
                });
            });
        });
    });
</script>
@endpush
@endsection
