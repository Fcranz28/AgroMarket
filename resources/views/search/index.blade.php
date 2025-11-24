@extends('layouts.app')

@section('content')
    <div class="search-results-container">
        <!-- Filters Sidebar -->
        <aside class="filters-sidebar">
            <h3>Filtros</h3>
            
            <div class="filter-section">
                <h4>Categorías</h4>
                <ul class="filter-list">
                    <li>
                        <button class="filter-btn active" data-category="all">
                            Todas las Categorías
                        </button>
                    </li>
                    @foreach($categories as $category)
                        <li>
                            <button class="filter-btn" data-category="{{ $category->slug }}">
                                {{ $category->name }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <div class="filter-section">
                <h4>Ordenar por</h4>
                <select id="sortSelect" class="filter-select">
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
            <div class="results-header">
                <h1 class="results-title">
                    <span id="resultsCount">...</span> resultados para 
                    <span class="search-query">"{{ $query }}"</span>
                </h1>
            </div>

            <!-- Products Grid -->
            <div class="search-results-grid" id="searchResultsGrid">
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p>Buscando productos...</p>
                </div>
            </div>
        </main>
    </div>

<style>
    .search-results-container {
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 30px;
        padding: 30px;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .filters-sidebar {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        height: fit-content;
        position: sticky;
        top: 100px;
    }
    
    .filters-sidebar h3 {
        font-size: 1.2rem;
        margin-bottom: 20px;
        color: #333;
    }
    
    .filter-section {
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .filter-section:last-child {
        border-bottom: none;
    }
    
    .filter-section h4 {
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 12px;
        color: #555;
    }
    
    .filter-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .filter-list li {
        margin-bottom: 8px;
    }
    
    .filter-btn {
        background: none;
        border: none;
        padding: 8px 12px;
        text-align: left;
        cursor: pointer;
        width: 100%;
        border-radius: 4px;
        transition: all 0.2s;
        color: #666;
        font-size: 0.9rem;
    }
    
    .filter-btn:hover {
        background: #f5f5f5;
        color: #333;
    }
    
    .filter-btn.active {
        background: #e3f2fd;
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .filter-select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 0.9rem;
    }
    
    .results-header {
        margin-bottom: 25px;
    }
    
    .results-title {
        font-size: 1.5rem;
        color: #333;
    }
    
    .search-query {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .search-results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }
    
    @media (max-width: 768px) {
        .search-results-container {
            grid-template-columns: 1fr;
        }
        
        .filters-sidebar {
            position: static;
        }
    }
</style>
@endsection

@push('scripts')
    <script>
        const initialQuery = "{{ $query }}";
        const initialCategory = "{{ $categorySlug }}";
    </script>
    @vite(['resources/js/search.js'])
@endpush
