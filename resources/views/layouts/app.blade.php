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
<body
    class="bg-canvas font-sans text-ink antialiased"
    x-data
    x-on:notify.window="Swal.fire({ toast: true, position: 'top-end', timer: 2000, showConfirmButton: false, icon: $event.detail.type, title: $event.detail.message })"
>
    @include('partials.header')
    @include('partials.mobile-nav')

    <main class="content">
        @yield('content')
    </main>

    <livewire:orders.cart-sidebar />

    @include('partials.footer')

    @livewireScripts
    @stack('scripts')
</body>
</html>
