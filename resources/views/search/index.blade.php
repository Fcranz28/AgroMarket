@extends('layouts.app')

@section('content')
    <div class="search-results-container" data-query="{{ $query ?? '' }}" data-category="{{ $categorySlug ?? 'all' }}">
        <!-- Filters Sidebar -->
        <aside class="search-filters-sidebar">
            <h3>Filtros</h3>
            
            <div class="search-filter-section">
                <h4>Categorías</h4>
                <ul class="search-filter-list">
                    <li>
                        <button class="search-filter-btn active" data-category="all">
                            Todas las Categorías
                        </button>
                    </li>
                    @foreach($categories as $category)
                        <li>
                            <button class="search-filter-btn" data-category="{{ $category->slug }}">
                                {{ $category->name }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <div class="search-filter-section">
                <h4>Ordenar por</h4>
                <select id="sortSelect" class="search-filter-select">
                    <option value="featured">Destacados</option>
                    <option value="price-asc">Precio: Menor a Mayor</option>
                    <option value="price-desc">Precio: Mayor a Menor</option>
                    <option value="name-asc">Nombre: A-Z</option>
                    <option value="name-desc">Nombre: Z-A</option>
                </select>
            </div>
        </aside>

        <!-- Search Results -->
        <main class="search-results-main">
            <div class="search-results-header">
                <h1 class="search-results-title">
                    <span id="resultsCount">...</span> resultados para 
                    <span class="search-query">"{{ $query }}"</span>
                </h1>
            </div>

            <!-- Products Grid -->
            <div class="search-results-grid" id="searchResultsGrid">
                <div class="search-loading-spinner">
                    <div class="search-spinner"></div>
                    <p>Buscando productos...</p>
                </div>
            </div>
        </main>
    </div>

@endsection

@push('scripts')
@vite(['resources/js/search/index.js'])
@endpush
