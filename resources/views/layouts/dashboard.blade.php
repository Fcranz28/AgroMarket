<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - AgroMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/dashboard.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('home') }}" class="sidebar-logo">
                    <img src="{{ asset('img/Logo_Claro.png') }}" alt="AgroMarket" class="logo-claro">
                    <img src="{{ asset('img/Logo_Oscuro.png') }}" alt="AgroMarket" class="logo-oscuro">
                </a>
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
                        <a href="{{ route('farmer.orders') }}" class="{{ request()->routeIs('farmer.orders') ? 'active' : '' }}">
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
                    <button type="button" id="mobileMenuToggle" class="mobile-menu-btn" aria-label="Toggle menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2>@yield('header', 'Dashboard')</h2>
                </div>
                <div class="header-right">
                    <!-- Theme Toggle -->
                    <button type="button" id="themeToggle" class="theme-toggle" aria-label="Cambiar tema">
                        <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="5"></circle>
                            <line x1="12" y1="1" x2="12" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="23"></line>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                            <line x1="1" y1="12" x2="3" y2="12"></line>
                            <line x1="21" y1="12" x2="23" y2="12"></line>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                        </svg>
                        <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                    </button>

                    <!-- User Profile -->
                    <div class="user-profile">
                        <div class="user-avatar">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span>{{ auth()->user()->name }}</span>
                    </div>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Cerrar sesión">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Content Body -->
            <div class="content-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== THEME TOGGLE =====
            const themeToggle = document.getElementById('themeToggle');
            const rootElement = document.documentElement;
            
            // Check for saved theme preference or default to system preference
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            // Set initial theme
            if (savedTheme) {
                rootElement.setAttribute('data-theme', savedTheme);
            } else if (systemPrefersDark) {
                rootElement.setAttribute('data-theme', 'dark');
            } else {
                rootElement.setAttribute('data-theme', 'light');
            }
            
            // Toggle theme on button click
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    const currentTheme = rootElement.getAttribute('data-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    
                    rootElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    
                    // Add rotation animation
                    this.style.transform = 'rotate(360deg)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 300);
                });
            }

            // ===== MOBILE MENU TOGGLE =====
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (mobileMenuToggle && sidebar) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });

                // Close sidebar when clicking outside
                document.addEventListener('click', function(event) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickOnMenuButton = mobileMenuToggle.contains(event.target);
                    
                    if (!isClickInsideSidebar && !isClickOnMenuButton && sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
