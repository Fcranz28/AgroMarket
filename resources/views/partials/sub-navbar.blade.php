{{-- Sub-barra de navegación con estilo Chrome tabs invertido --}}
<nav class="sub-navbar">
    <div class="sub-navbar-container">
        <a href="{{ route('products.index') }}" class="sub-nav-tab {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <span>Categorías</span>
        </a>
        <a href="{{ route('contact.show') }}" class="sub-nav-tab {{ request()->routeIs('contact.*') ? 'active' : '' }}">
            <span>Contacto</span>
        </a>
        <a href="{{ route('about') }}" class="sub-nav-tab {{ request()->routeIs('about') ? 'active' : '' }}">
            <span>Nosotros</span>
        </a>
    </div>
</nav>
