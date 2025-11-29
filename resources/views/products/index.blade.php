@extends('layouts.app')

@push('styles')
    @vite(['resources/css/categorias.css'])
@endpush

@section('content')
    <div class="categories-container">
        <!-- Botón de Filtro Móvil -->
        <button class="mobile-filter-btn" id="mobileFilterBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" y1="21" x2="4" y2="14"></line>
                <line x1="4" y1="10" x2="4" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12" y2="3"></line>
                <line x1="20" y1="21" x2="20" y2="16"></line>
                <line x1="20" y1="12" x2="20" y2="3"></line>
                <line x1="1" y1="14" x2="7" y2="14"></line>
                <line x1="9" y1="8" x2="15" y2="8"></line>
                <line x1="17" y1="16" x2="23" y2="16"></line>
            </svg>
            <span>Filtrar Categorías</span>
        </button>

        <!-- Barra lateral de categorías (Desktop) -->
        <aside class="categories-sidebar desktop-sidebar">
            <h2>Categorías</h2>
            <ul class="category-list">
                <li>
                    <button class="category-btn active" data-category="all">
                        Todos los Productos
                    </button>
                </li>
                @foreach($categories as $category)
                    <li>
                        <button class="category-btn" data-category="{{ $category->slug }}">
                            {{ $category->name }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </aside>

        <!-- Sidebar Móvil (Off-canvas) -->
        <div class="mobile-sidebar-overlay" id="mobileSidebarOverlay"></div>
        <aside class="mobile-categories-sidebar" id="mobileSidebar">
            <div class="mobile-sidebar-header">
                <h2>Categorías</h2>
                <button class="close-sidebar-btn" id="closeSidebarBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <ul class="category-list mobile-category-list">
                <li>
                    <button class="category-btn active" data-category="all">
                        Todos los Productos
                    </button>
                </li>
                @foreach($categories as $category)
                    <li>
                        <button class="category-btn" data-category="{{ $category->slug }}">
                            {{ $category->name }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </aside>

        <!-- Contenido principal -->
        <main class="products-container">
            <div class="products-header">
                <h1 class="category-title">Todos los Productos</h1>
                <div class="filters">
                    <select id="sortSelect" class="sort-select">
                        <option value="featured">Destacados</option>
                        <option value="price-asc">Precio: Menor a Mayor</option>
                        <option value="price-desc">Precio: Mayor a Menor</option>
                        <option value="name-asc">Nombre: A-Z</option>
                        <option value="name-desc">Nombre: Z-A</option>
                    </select>
                </div>
            </div>

            <!-- Grid de productos -->
            <div class="products-grid" id="productsGrid">
                <!-- Los productos se cargarán aquí dinámicamente -->
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p>Cargando productos...</p>
                </div>
            </div>

            <!-- Paginación -->
            <div class="pagination" id="pagination">
                <!-- La paginación se generará dinámicamente -->
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/categorias.js'])
@endpush