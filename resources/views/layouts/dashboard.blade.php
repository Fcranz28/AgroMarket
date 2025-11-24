<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - AgroMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/dashboard.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('home') }}" class="sidebar-logo">AgroMarket</a>
            </div>
            
            <ul class="sidebar-menu">
                @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Usuarios
                        </a>
                    </li>
                    <!-- Add more admin links here -->
                @elseif(auth()->user()->isFarmer())
                    <li>
                        <a href="{{ route('farmer.dashboard') }}" class="{{ request()->routeIs('farmer.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i> Resumen
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.productos.index') }}" class="{{ request()->routeIs('dashboard.productos.*') ? 'active' : '' }}">
                            <i class="fas fa-box"></i> Mis Productos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-bag"></i> Pedidos
                        </a>
                    </li>
                @endif
                
                <li>
                    <a href="{{ route('home') }}">
                        <i class="fas fa-store"></i> Ir a la Tienda
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <h2>@yield('header', 'Dashboard')</h2>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <div class="user-avatar">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span>{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="background:none; border:none; color:#666; cursor:pointer;">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Content Body -->
            <div class="content-body">
                @if(session('success'))
                    <div class="alert alert-success" style="background:#d4edda; color:#155724; padding:15px; margin-bottom:20px; border-radius:4px;">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" style="background:#f8d7da; color:#721c24; padding:15px; margin-bottom:20px; border-radius:4px;">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
