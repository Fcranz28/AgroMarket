@extends('layouts.app')

@section('content')
<div class="container">
    <div class="form-container">
        <h2>[Admin] Editar Producto: {{ $product->name }}</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.productos.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="name">Nombre del Producto</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
            </div>

            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="price">Precio (S/.)</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price', $product->price) }}" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="category_id">Categoría</label>
                    <select class="form-control" id="category_id" name="category_id" required>
                        <option value="">Seleccione una categoría</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="unit">Unidad de Medida</label>
                    <input type="text" class="form-control" id="unit" name="unit" placeholder="Ej: kg, unidad, atado" value="{{ old('unit', $product->unit) }}" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="stock">Stock Disponible</label>
                    <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="image">Imagen del Producto</label>
                @if($product->image_path)
                    <div class="current-image mb-2">
                        <img src="{{ Storage::url($product->image_path) }}" alt="Imagen actual" style="height: 100px; border-radius: 5px;">
                        <p class="text-muted small">Imagen actual</p>
                    </div>
                @endif
                <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                <small class="text-muted">Dejar en blanco para mantener la imagen actual</small>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar Producto</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    
    .form-container {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    h2 {
        color: #2c3e50;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-row {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .col-md-6 {
        flex: 1;
        min-width: 250px;
    }

    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #4a5568;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        transition: border-color 0.2s;
    }

    .form-control:focus {
        border-color: #48bb78;
        outline: none;
        box-shadow: 0 0 0 3px rgba(72, 187, 120, 0.1);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
    }

    .btn-primary {
        background-color: #48bb78;
        color: white;
    }

    .btn-primary:hover {
        background-color: #38a169;
    }

    .btn-secondary {
        background-color: #e2e8f0;
        color: #4a5568;
    }

    .btn-secondary:hover {
        background-color: #cbd5e0;
    }

    .alert {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 0.5rem;
    }

    .alert-danger {
        background-color: #fff5f5;
        color: #c53030;
        border: 1px solid #feb2b2;
    }
    
    .text-muted {
        color: #718096;
    }
    
    .small {
        font-size: 0.875rem;
    }
    
    .mb-2 {
        margin-bottom: 0.5rem;
    }
</style>
@endpush
@endsection
