@extends('layouts.app')

@section('content')
    <div class="categories-container">
        <!-- Barra lateral de categorías -->
        <aside class="categories-sidebar">
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