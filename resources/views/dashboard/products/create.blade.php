@extends('layouts.dashboard')

@section('content')
<div class="dashboard-products-form-container">
    <div class="product-form-card">
        <div class="product-form-header">
            <h2>Agregar Nuevo Producto</h2>
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

        <form action="{{ route('dashboard.productos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="product-form-group">
                <label for="name">Nombre del Producto</label>
                <input type="text" class="product-form-control" id="name" name="name" value="{{ old('name') }}" required placeholder="Ej. Manzanas Orgánicas">
            </div>

            <div class="product-form-group">
                <label for="description">Descripción</label>
                <textarea class="product-form-control" id="description" name="description" rows="3" required placeholder="Describe tu producto...">{{ old('description') }}</textarea>
            </div>

            <div class="product-form-group">
                <label for="category_id">Categoría</label>
                <select class="product-form-control" id="category_id" name="category_id" required>
                    <option value="">Seleccione una categoría</option>
                    @if(isset($categories) && count($categories) > 0)
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    @else
                        <option value="" disabled>No hay categorías disponibles</option>
                    @endif
                </select>
            </div>

            <div class="product-form-row">
                <div class="product-form-group col-md-6">
                    <label for="unit">Unidad de Medida (Seleccione una o más)</label>
                    <div class="unit-selection-grid">
                        @php
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
                        @foreach($units as $value => $label)
                            <div class="unit-option" data-value="{{ $value }}">
                                <input type="checkbox" name="unit[]" value="{{ $value }}" id="unit_{{ $value }}" class="d-none">
                                <span>{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                    <small class="text-muted">Haga clic en las unidades que desea vender.</small>
                </div>

                <div class="product-form-group col-md-6">
                    <label>Stock y Precio por Unidad</label>
                    <div id="stock-container" class="stock-container">
                        <div class="empty-stock-state">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-package"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            <p>Seleccione unidades para asignar stock y precio</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="product-form-group full-width">
                <label>Imágenes del Producto</label>
                <div class="image-upload-container" id="dropZone">
                    <input type="file" name="image[]" id="imageInput" class="file-input" accept="image/*" multiple>
                    <div class="upload-placeholder" id="uploadPlaceholder">
                        <div class="upload-icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        </div>
                        <p>Arrastra y suelta tus imágenes aquí</p>
                        <span class="file-info">o haz clic para seleccionar (Máx 2MB)</span>
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
                <button type="submit" class="product-btn product-btn-primary">Guardar Producto</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
{{-- Styles loaded via dashboard.css → dashboard/products/form.css --}}
@endpush

@push('styles')
{{-- Styles loaded via dashboard.css → dashboard/products/form.css --}}
@endpush

@push('scripts')
    @vite(['resources/js/dashboard/products/form.js'])
@endpush
@endsection
