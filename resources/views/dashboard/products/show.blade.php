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
                <h1>{{ $product->name }}</h1>
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
                    <span class="label">Publicado:</span>
                    <span class="value">{{ $product->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
            
            <div class="actions">
                <a href="{{ route('dashboard.productos.edit', $product) }}" class="btn btn-primary">Editar Producto</a>
                <a href="{{ route('dashboard.productos.index') }}" class="btn btn-secondary">Volver a la lista</a>
            </div>
        </div>
    </div>
</div>


@push('styles')
{{-- Styles loaded via app.css → dashboard/products/show.css --}}
@endpush
@endsection
