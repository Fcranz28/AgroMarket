@extends('layouts.dashboard')

@section('title', 'Mis Productos')
@section('header', 'Mis Productos')

@section('content')
<div class="dashboard-products-index-container">
    <div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div></div>
        <a href="{{ route('dashboard.productos.create') }}" class="btn btn-primary" style="background: #48bb78; border: none; padding: 10px 20px; border-radius: 8px; color: white; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y12="12"></line>
            </svg>
            Agregar Producto
        </a>
    </div>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#48bb78',
                    timer: 3000
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#48bb78'
                });
            });
        </script>
    @endif

    <div class="dashboard-products-grid">
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
                        <form action="{{ route('dashboard.productos.destroy', $product) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-product-name="{{ $product->name }}">Eliminar</button>
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


@push('styles')
{{-- Styles loaded via dashboard.css → dashboard/products/index.css --}}
@endpush

@push('scripts')
    @vite(['resources/js/dashboard/products/index.js'])
@endpush
    </div>
@endsection
