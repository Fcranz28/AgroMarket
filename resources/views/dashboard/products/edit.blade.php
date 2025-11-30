@extends('layouts.dashboard')

@section('title', 'Editar Producto')
@section('header', 'Editar Producto')

@section('content')
<div class="dashboard-products-form-container">
    <div class="product-form-card">
        <div class="product-form-header">
            <h2>Editar Producto: {{ $producto->name }}</h2>

        </div>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('dashboard.productos.update', ['producto' => $producto->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="product-form-group">
                <label for="name">Nombre del Producto</label>
                <input type="text" class="product-form-control" id="name" name="name" value="{{ old('name', $producto->name) }}" required>
            </div>

            <div class="product-form-group">
                <label for="description">Descripción</label>
                <textarea class="product-form-control" id="description" name="description" rows="3" required>{{ old('description', $producto->description) }}</textarea>
            </div>

            <div class="product-form-group">
                <label for="category_id">Categoría</label>
                <select class="product-form-control" id="category_id" name="category_id" required>
                    <option value="">Seleccione una categoría</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $producto->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="product-form-row">
                <div class="product-form-group">
                    <label for="unit">Unidad de Medida (Seleccione una o más)</label>
                    @php
                        // Get actual units from product_units table
                        $productUnits = $producto->units;
                        $selectedUnits = $productUnits->pluck('unit')->toArray();
                        $units = [
                            'kg' => 'Kilogramo (kg)',
                            'g' => 'Gramo (g)',
                            'lb' => 'Libra (lb)',
                            'unidad' => 'Unidad',
                            'docena' => 'Docena',
                            'saco' => 'Saco',
                            'caja' => 'Caja',
                            'atado' => 'Atado',
                            'manojo' => 'Manojo',
                            'litro' => 'Litro',
                            'ml' => 'Mililitro (ml)'
                        ];
                    @endphp
                    <div class="unit-selection-grid">
                        @foreach($units as $value => $label)
                            @php $isSelected = in_array($value, $selectedUnits); @endphp
                            <div class="unit-option {{ $isSelected ? 'active' : '' }}" data-value="{{ $value }}">
                                <input type="checkbox" name="unit[]" value="{{ $value }}" id="unit_{{ $value }}" class="d-none" {{ $isSelected ? 'checked' : '' }}>
                                <span>{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                    <small class="text-muted">Haga clic en las unidades que desea vender.</small>
                </div>

                <div class="product-form-group">
                    <label>Stock y Precio por Unidad</label>
                    <div id="stock-container" class="stock-container">
                        <!-- Pre-fill existing units with stock and price -->
                        @foreach($productUnits as $pu)
                            <div class="stock-item">
                                <div class="stock-label">
                                    @switch($pu->unit)
                                        @case('kg') Kilogramo (kg) @break
                                        @case('g') Gramo (g) @break
                                        @case('lb') Libra (lb) @break
                                        @case('unidad') Unidad @break
                                        @case('docena') Docena @break
                                        @case('saco') Saco @break
                                        @case('caja') Caja @break
                                        @case('atado') Atado @break
                                        @case('manojo') Manojo @break
                                        @case('litro') Litro @break
                                        @case('ml') Mililitro (ml) @break
                                        @default {{ $pu->unit }}
                                    @endswitch
                                </div>
                                <div class="stock-input-group">
                                    <div style="flex: 1;">
                                        <label class="input-label">Stock</label>
                                        <input type="number" class="product-form-control" name="stock[]" placeholder="Cantidad" value="{{ $pu->stock }}" required data-unit="{{ $pu->unit }}">
                                    </div>
                                    <div style="flex: 1;">
                                        <label class="input-label">Precio (S/.)</label>
                                        <input type="number" step="0.01" class="product-form-control" name="price[]" placeholder="0.00" value="{{ $pu->price }}" required data-unit="{{ $pu->unit }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="product-form-group full-width">
                <label>Imágenes del Producto</label>
                
                <!-- Existing Images -->
                @if($producto->images->count() > 0)
                    <div class="mb-3">
                        <label>Imágenes Actuales (Seleccionar para eliminar)</label>
                        <div class="existing-images-grid">
                            @foreach($producto->images as $img)
                                <div class="existing-image-item">
                                    <img src="{{ Storage::url($img->image_path) }}" alt="Product Image">
                                    <div class="delete-checkbox">
                                        <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" id="img_{{ $img->id }}">
                                        <label for="img_{{ $img->id }}">Eliminar</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- New Uploads -->
                <div class="image-upload-container" id="dropZone">
                    <input type="file" name="image[]" id="imageInput" class="file-input" accept="image/*" multiple>
                    <div class="upload-placeholder" id="uploadPlaceholder">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <p>Agregar nuevas imágenes (Arrastrar o Clic)</p>
                        <span class="file-info">Soporta: JPG, PNG, WEBP (Máx 2MB)</span>
                    </div>
                    <div class="preview-container" id="previewContainer" style="display: none;">
                        <!-- Previews will be inserted here -->
                    </div>
                </div>
                @error('image')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                @error('image.*')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="product-form-actions">
                <a href="{{ route('dashboard.productos.index') }}" class="product-btn product-btn-secondary">Cancelar</a>
                <button type="submit" class="product-btn product-btn-primary">Actualizar Producto</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
{{-- Styles loaded via dashboard.css → dashboard/products/form.css --}}
@endpush

@push('scripts')
    @vite(['resources/js/dashboard/products/form.js'])
@endpush

@endsection


