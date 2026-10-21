<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroMarket</title>
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Aplica el tema guardado antes de pintar para evitar el parpadeo --}}
    <script>
        try {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark' || theme === 'light') {
                document.documentElement.dataset.theme = theme;
            }
        } catch (e) {}
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-canvas font-sans text-ink antialiased">
    @include('partials.header')
    @include('partials.mobile-nav')

    <main class="content">
        @yield('content')
    </main>

    {{-- Carrito lateral (lo maneja carrito.js hasta que pase a Livewire) --}}
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3>Tu Carrito</h3>
            <button class="close-cart" id="closeCart" aria-label="Cerrar carrito">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </button>
        </div>
        <div class="cart-content" id="cartContent"></div>
        <div class="cart-footer">
            <div class="cart-total">Total: S/. <span id="cartTotal">0.00</span></div>
            <button class="checkout-button" id="checkoutButton">Proceder al Pago</button>
        </div>
    </div>

    @include('partials.footer')

    @livewireScripts
    @stack('scripts')
</body>
</html>
