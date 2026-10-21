<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel') - AgroMarket</title>
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.theme-script')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
@php
    $user = auth()->user();

    $menu = $user->isAdmin()
        ? [
            ['route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'fa-chart-line', 'label' => 'Resumen'],
            ['route' => 'admin.users', 'active' => 'admin.users*', 'icon' => 'fa-users', 'label' => 'Usuarios'],
            ['route' => 'admin.productos.index', 'active' => 'admin.productos.*', 'icon' => 'fa-box', 'label' => 'Productos'],
            ['route' => 'admin.reports.index', 'active' => 'admin.reports.*', 'icon' => 'fa-exclamation-circle', 'label' => 'Reportes'],
        ]
        : [
            ['route' => 'farmer.dashboard', 'active' => 'farmer.dashboard', 'icon' => 'fa-home', 'label' => 'Resumen'],
            ['route' => 'dashboard.productos.index', 'active' => 'dashboard.productos.*', 'icon' => 'fa-box', 'label' => 'Mis productos'],
            ['route' => 'farmer.orders', 'active' => 'farmer.orders*', 'icon' => 'fa-shopping-bag', 'label' => 'Pedidos'],
        ];
@endphp
<body
    class="min-h-screen bg-canvas font-sans text-ink antialiased"
    x-data="{ menuOpen: false }"
    x-on:keydown.escape.window="menuOpen = false"
    x-on:notify.window="Swal.fire({ toast: true, position: 'top-end', timer: 2000, showConfirmButton: false, icon: $event.detail.type, title: $event.detail.message })"
>
    <div x-cloak x-show="menuOpen" x-transition.opacity x-on:click="menuOpen = false" class="fixed inset-0 z-40 bg-black/40 lg:hidden"></div>

    <aside
        x-bind:class="menuOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-line bg-surface transition-transform duration-200 lg:translate-x-0"
        aria-label="Menú del panel"
        data-test="panel-sidebar"
    >
        <a href="{{ route('home') }}" class="flex h-16 items-center border-b border-line px-6">
            <img src="{{ asset('img/Logo_Claro.png') }}" alt="AgroMarket" class="h-9 w-auto dark:hidden">
            <img src="{{ asset('img/Logo_Oscuro.png') }}" alt="AgroMarket" class="hidden h-9 w-auto dark:block">
        </a>

        <nav class="flex flex-1 flex-col gap-1 p-4">
            @foreach ($menu as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @class([
                        'flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition',
                        'bg-night text-lime shadow-soft' => request()->routeIs($item['active']),
                        'text-ink hover:bg-brand-soft hover:text-brand' => ! request()->routeIs($item['active']),
                    ])
                ><i class="fas {{ $item['icon'] }} w-4 text-center"></i> {{ $item['label'] }}</a>
            @endforeach

            <a href="{{ route('home') }}" class="mt-auto flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium text-muted transition hover:bg-brand-soft hover:text-brand"><i class="fas fa-store w-4 text-center"></i> Ir a la tienda</a>
        </nav>
    </aside>

    <div class="flex min-h-screen flex-col lg:pl-64">
        <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-line bg-surface/90 px-4 backdrop-blur-md sm:px-6">
            <button type="button" data-test="panel-menu" x-on:click="menuOpen = true" class="rounded-lg p-2 text-ink hover:bg-brand-soft lg:hidden" aria-label="Abrir menú"><i class="fas fa-bars"></i></button>
            <h1 class="truncate text-lg font-semibold text-ink">@yield('header', 'Panel')</h1>

            <div class="ml-auto flex items-center gap-2 sm:gap-3">
                @include('partials.theme-toggle')

                <span class="hidden items-center gap-2 text-sm sm:flex">
                    <span class="flex size-8 items-center justify-center rounded-full bg-night font-semibold text-lime">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    <span class="max-w-40 truncate text-ink">{{ $user->name }}</span>
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg p-2 text-muted transition hover:bg-danger/10 hover:text-danger" title="Cerrar sesión" aria-label="Cerrar sesión"><i class="fas fa-sign-out-alt"></i></button>
                </form>
            </div>
        </header>

        <main class="flex flex-1 flex-col gap-6 p-4 sm:p-6 lg:p-8">
            @if (session('success'))
                <x-ui.alert type="success" dismissible>{{ session('success') }}</x-ui.alert>
            @endif
            @if (session('error'))
                <x-ui.alert type="error" dismissible>{{ session('error') }}</x-ui.alert>
            @endif

            @yield('content')
        </main>
    </div>

    @livewireScriptConfig
    @stack('scripts')
</body>
</html>
