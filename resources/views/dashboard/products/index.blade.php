@extends('layouts.dashboard')

@section('title', 'Mis Productos')
@section('header', 'Mis Productos')

@section('content')
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

    .dashboard-products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 2rem;
    }

    .product-card {
        background: var(--bg-card);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: transform 0.2s;
        border: 1px solid var(--border-color);
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
        border-color: var(--accent-primary);
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
        color: var(--text-primary);
    }

    .price {
        font-weight: bold;
        color: var(--accent-primary);
        font-size: 1.1rem;
    }

    .stock {
        color: var(--text-secondary);
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
        background-color: #4299e1;
        color: white;
        border: 1px solid #4299e1;
    }

    .btn-secondary:hover {
        background-color: #3182ce;
    }

    .btn-danger {
        background-color: #e53e3e;
        color: white;
        border: 1px solid #e53e3e;
    }

    .btn-danger:hover {
        background-color: #c53030;
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete confirmations with SweetAlert
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.delete-form');
                const productName = this.dataset.productName;
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar "${productName}"? Esta acción no se puede deshacer.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e53e3e',
                    cancelButtonColor: '#718096',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection
