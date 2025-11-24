@extends('layouts.app')

@section('content')
<div class="container">
    <div class="product-detail-card">
        <div class="product-image-container">
            @if($product->image_path)
                <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}">
            @else
                <img src="{{ asset('img/no-image.png') }}" alt="Sin imagen">
            @endif
        </div>
        
        <div class="product-info">
            <div class="header">
                <h1>{{ $product->name }} <span class="badge badge-admin">Admin View</span></h1>
                <span class="category-badge">{{ $product->category->name ?? 'Sin categoría' }}</span>
            </div>
            
            <div class="price-tag">
                S/. {{ number_format($product->price, 2) }} <span class="unit">/ {{ $product->unit }}</span>
            </div>
            
            <div class="description">
                <h3>Descripción</h3>
                <p>{{ $product->description }}</p>
            </div>
            
            <div class="meta-info">
                <div class="meta-item">
                    <span class="label">Stock Disponible:</span>
                    <span class="value">{{ $product->stock }}</span>
                </div>
                <div class="meta-item">
                    <span class="label">Slug:</span>
                    <span class="value">{{ $product->slug }}</span>
                </div>
            </div>
            
            <div class="actions">
                <a href="{{ route('admin.productos.edit', $product->id) }}" class="btn btn-primary">Editar Producto</a>
                <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary">Volver a la lista</a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    
    .product-detail-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
    
    @media (max-width: 768px) {
        .product-detail-card {
            grid-template-columns: 1fr;
        }
    }
    
    .product-image-container {
        height: 400px;
        background-color: #f7fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-info {
        padding: 2rem;
        display: flex;
        flex-direction: column;
    }
    
    .header {
        margin-bottom: 1rem;
    }
    
    h1 {
        margin: 0 0 0.5rem 0;
        color: #2d3748;
        font-size: 2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .badge-admin {
        background-color: #e53e3e;
        color: white;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        vertical-align: middle;
    }
    
    .category-badge {
        background-color: #e2e8f0;
        color: #4a5568;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .price-tag {
        font-size: 2rem;
        font-weight: bold;
        color: #48bb78;
        margin-bottom: 2rem;
    }
    
    .unit {
        font-size: 1rem;
        color: #718096;
        font-weight: normal;
    }
    
    .description {
        margin-bottom: 2rem;
        flex-grow: 1;
    }
    
    .description h3 {
        font-size: 1.1rem;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }
    
    .description p {
        color: #718096;
        line-height: 1.6;
    }
    
    .meta-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }
    
    .meta-item {
        display: flex;
        flex-direction: column;
    }
    
    .label {
        font-size: 0.875rem;
        color: #a0aec0;
        margin-bottom: 0.25rem;
    }
    
    .value {
        font-weight: 600;
        color: #2d3748;
    }
    
    .actions {
        display: flex;
        gap: 1rem;
    }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        flex: 1;
        transition: all 0.2s;
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
</style>
@endpush
@endsection
