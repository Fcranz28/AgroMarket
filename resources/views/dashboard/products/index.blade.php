@extends('layouts.app')

@section('content')
<div class="container">
    <div class="header-actions">
        <h2>Mis Productos</h2>
        <a href="{{ route('dashboard.productos.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Agregar Producto
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="products-grid">
        @forelse($products as $product)
            <div class="product-card">
                <div class="product-image">
                    @if($product->image_path)
                        <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}">
                    @else
                        <img src="{{ asset('img/no-image.png') }}" alt="Sin imagen">
                    @endif
                </div>
                <div class="product-details">
                    <h3>{{ $product->name }}</h3>
                    <p class="price">S/. {{ number_format($product->price, 2) }} / {{ $product->unit }}</p>
                    <p class="stock">Stock: {{ $product->stock }}</p>
                    <div class="actions">
                        <a href="{{ route('dashboard.productos.edit', $product) }}" class="btn btn-sm btn-secondary">Editar</a>
                        <form action="{{ route('dashboard.productos.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de querer eliminar este producto?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="no-products">
                <p>No tienes productos registrados.</p>
                <a href="{{ route('dashboard.productos.create') }}" class="btn btn-primary">Crear mi primer producto</a>
            </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
    .container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 2rem;
    }

    .product-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }

    .product-card:hover {
        transform: translateY(-5px);
    }

    .product-image {
        height: 200px;
        overflow: hidden;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-details {
        padding: 1rem;
    }

    .product-details h3 {
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
        color: #2c3e50;
    }

    .price {
        font-weight: bold;
        color: #48bb78;
        font-size: 1.1rem;
    }

    .stock {
        color: #718096;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        text-decoration: none;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }

    .btn-primary {
        background-color: #48bb78;
        color: white;
    }

    .btn-primary:hover {
        background-color: #38a169;
    }

    .btn-secondary {
        background-color: #edf2f7;
        color: #4a5568;
    }

    .btn-secondary:hover {
        background-color: #e2e8f0;
    }

    .btn-danger {
        background-color: #fff5f5;
        color: #c53030;
    }

    .btn-danger:hover {
        background-color: #fed7d7;
    }

    .alert-success {
        background-color: #f0fff4;
        color: #2f855a;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
        border: 1px solid #c6f6d5;
    }

    .no-products {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 0;
        color: #718096;
    }

    .d-inline {
        display: inline-block;
    }
</style>
@endpush
@endsection
