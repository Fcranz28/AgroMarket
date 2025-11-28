@extends('layouts.dashboard')

@section('title', 'Editar Producto')
@section('header', 'Editar Producto')

@section('content')
<div class="container">
    <div class="form-container">
        <div class="form-header">
            <h2>Editar Producto: {{ $producto->name }}</h2>
            <button type="button" id="themeToggle" class="theme-toggle" aria-label="Cambiar tema">
                <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
                <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>
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
            
            <div class="form-group">
                <label for="name">Nombre del Producto</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $producto->name) }}" required>
            </div>

            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $producto->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="category_id">Categoría</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <option value="">Seleccione una categoría</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $producto->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="unit">Unidad de Medida (Seleccione una o más)</label>
                    @php
                        // Get actual units from product_units table
                        $productUnits = $producto->units;
                        $selectedUnits = $productUnits->pluck('unit')->toArray();
                        $unitDataMap = [];
                        foreach ($productUnits as $pu) {
                            $unitDataMap[$pu->unit] = [
                                'stock' => $pu->stock,
                                'price' => $pu->price
                            ];
                        }
                    @endphp
                    <select class="form-control" id="unit" name="unit[]" multiple required style="height: 150px;">
                        <option value="kg" {{ in_array('kg', $selectedUnits) ? 'selected' : '' }}>Kilogramo (kg)</option>
                        <option value="g" {{ in_array('g', $selectedUnits) ? 'selected' : '' }}>Gramo (g)</option>
                        <option value="lb" {{ in_array('lb', $selectedUnits) ? 'selected' : '' }}>Libra (lb)</option>
                        <option value="unidad" {{ in_array('unidad', $selectedUnits) ? 'selected' : '' }}>Unidad</option>
                        <option value="docena" {{ in_array('docena', $selectedUnits) ? 'selected' : '' }}>Docena</option>
                        <option value="saco" {{ in_array('saco', $selectedUnits) ? 'selected' : '' }}>Saco</option>
                        <option value="caja" {{ in_array('caja', $selectedUnits) ? 'selected' : '' }}>Caja</option>
                        <option value="atado" {{ in_array('atado', $selectedUnits) ? 'selected' : '' }}>Atado</option>
                        <option value="manojo" {{ in_array('manojo', $selectedUnits) ? 'selected' : '' }}>Manojo</option>
                        <option value="litro" {{ in_array('litro', $selectedUnits) ? 'selected' : '' }}>Litro</option>
                        <option value="ml" {{ in_array('ml', $selectedUnits) ? 'selected' : '' }}>Mililitro (ml)</option>
                    </select>
                    <small class="text-muted">Mantenga presionado Ctrl (Windows) o Cmd (Mac) para seleccionar múltiples opciones.</small>
                </div>

                <div class="form-group col-md-6">
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
                                        <input type="number" class="form-control" name="stock[]" placeholder="Cantidad" value="{{ $pu->stock }}" required data-unit="{{ $pu->unit }}">
                                    </div>
                                    <div style="flex: 1;">
                                        <label class="input-label">Precio (S/.)</label>
                                        <input type="number" step="0.01" class="form-control" name="price[]" placeholder="0.00" value="{{ $pu->price }}" required data-unit="{{ $pu->unit }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="form-group full-width">
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

            <div class="form-actions">
                <a href="{{ route('dashboard.productos.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar Producto</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    :root {
        /* Light Mode - Pastel Colors */
        --bg-primary: #fef6f9;
        --bg-secondary: #ffffff;
        --bg-card: #ffffff;
        --bg-input: #f8f9fa;
        --bg-input-focus: #ffffff;
        
        --text-primary: #2d3748;
        --text-secondary: #718096;
        --text-muted: #a0aec0;
        
        --border-color: #e9d5e0;
        --border-focus: #f0abdd;
        
        --accent-primary: #f0abdd;
        --accent-secondary: #b8e0d2;
        --accent-tertiary: #d4a5a5;
        
        --shadow-sm: 0 2px 4px rgba(240, 171, 221, 0.1);
        --shadow-md: 0 4px 12px rgba(240, 171, 221, 0.15);
        --shadow-lg: 0 8px 24px rgba(240, 171, 221, 0.2);
        
        --success: #b8e0d2;
        --warning: #ffd5a5;
        --danger: #ffb3c1;
    }

    @media (prefers-color-scheme: dark) {
        :root:not([data-theme="light"]) {
            /* Dark Mode - Muted Pastels */
            --bg-primary: #1a1a2e;
            --bg-secondary: #25274d;
            --bg-card: #2e3047;
            --bg-input: #363853;
            --bg-input-focus: #3d405f;
            
            --text-primary: #e8e9f3;
            --text-secondary: #b4b5c5;
            --text-muted: #8486a0;
            
            --border-color: #464866;
            --border-focus: #8b7fa8;
            
            --accent-primary: #9d8ba8;
            --accent-secondary: #7fa89d;
            --accent-tertiary: #a88b8b;
            
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.5);
            
            --success: #7fa89d;
            --warning: #c4a57b;
            --danger: #c47b8d;
        }
    }

    :root[data-theme="dark"] {
        /* Dark Mode - Muted Pastels (Manual override) */
        --bg-primary: #1a1a2e;
        --bg-secondary: #25274d;
        --bg-card: #2e3047;
        --bg-input: #363853;
        --bg-input-focus: #3d405f;
        
        --text-primary: #e8e9f3;
        --text-secondary: #b4b5c5;
        --text-muted: #8486a0;
        
        --border-color: #464866;
        --border-focus: #8b7fa8;
        
        --accent-primary: #9d8ba8;
        --accent-secondary: #7fa89d;
        --accent-tertiary: #a88b8b;
        
        --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.3);
        --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.4);
        --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.5);
        
        --success: #7fa89d;
        --warning: #c4a57b;
        --danger: #c47b8d;
    }

    .container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .form-container {
        background: var(--bg-card);
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .form-container:hover {
        box-shadow: var(--shadow-lg);
    }

    .form-container h2 {
        color: var(--text-primary);
        margin-bottom: 2rem;
        font-size: 1.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-container h2::before {
        content: '';
        width: 4px;
        height: 2rem;
        background: linear-gradient(180deg, var(--accent-primary), var(--accent-secondary));
        border-radius: 2px;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .theme-toggle {
        position: relative;
        width: 50px;
        height: 50px;
        border-radius: 12px;
        border: 2px solid var(--border-color);
        background: var(--bg-input);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .theme-toggle:hover {
        border-color: var(--accent-primary);
        background: var(--bg-card);
        transform: scale(1.05);
    }

    .theme-toggle svg {
        position: absolute;
        transition: all 0.3s ease;
        color: var(--accent-primary);
    }

    .sun-icon {
        opacity: 1;
        transform: rotate(0deg) scale(1);
    }

    .moon-icon {
        opacity: 0;
        transform: rotate(-90deg) scale(0.5);
    }

    [data-theme="dark"] .sun-icon {
        opacity: 0;
        transform: rotate(90deg) scale(0.5);
    }

    [data-theme="dark"] .moon-icon {
        opacity: 1;
        transform: rotate(0deg) scale(1);
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border: 1px solid;
        background: var(--bg-input);
    }

    .alert-danger {
        border-color: var(--danger);
        background: linear-gradient(135deg, rgba(255, 179, 193, 0.1), rgba(255, 179, 193, 0.05));
        color: var(--danger);
    }

    .alert ul {
        margin: 0;
        padding-left: 1.25rem;
    }

    .form-group {
        margin-bottom: 1.75rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--text-secondary);
        font-size: 0.925rem;
        letter-spacing: 0.01em;
    }

    .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: var(--bg-input);
        color: var(--text-primary);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--border-focus);
        background: var(--bg-input-focus);
        box-shadow: 0 0 0 4px rgba(240, 171, 221, 0.1);
        transform: translateY(-1px);
    }

    .form-control::placeholder {
        color: var(--text-muted);
    }

    select.form-control {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23a0aec0' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.75rem;
    }

    .text-muted {
        font-size: 0.825rem;
        color: var(--text-muted);
        margin-top: 0.375rem;
        display: block;
    }

    /* Stock Container */
    .stock-container {
        background: linear-gradient(135deg, rgba(240, 171, 221, 0.05), rgba(184, 224, 210, 0.05));
        padding: 1.25rem;
        border-radius: 16px;
        border: 2px dashed var(--border-color);
        min-height: 180px;
        max-height: 400px;
        overflow-y: auto;
        transition: all 0.3s ease;
    }

    .stock-container::-webkit-scrollbar {
        width: 8px;
    }

    .stock-container::-webkit-scrollbar-track {
        background: var(--bg-input);
        border-radius: 4px;
    }

    .stock-container::-webkit-scrollbar-thumb {
        background: var(--accent-primary);
        border-radius: 4px;
    }

    .stock-item {
        background: var(--bg-card);
        padding: 1rem;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        margin-bottom: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        animation: slideIn 0.3s ease-out;
        transition: all 0.2s ease;
    }

    .stock-item:hover {
        border-color: var(--accent-primary);
        box-shadow: var(--shadow-sm);
        transform: translateX(4px);
    }

    .stock-item:last-child {
        margin-bottom: 0;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stock-label {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.925rem;
        padding: 0.375rem 0.75rem;
        background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-transform: capitalize;
    }

    .stock-input-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    .input-label {
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-bottom: 0.375rem;
        display: block;
        font-weight: 500;
    }

    /* Image Upload Styles */
    .existing-images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .existing-image-item {
        position: relative;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .existing-image-item:hover {
        border-color: var(--accent-primary);
        box-shadow: var(--shadow-md);
        transform: scale(1.05);
    }

    .existing-image-item img {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }

    .delete-checkbox {
        padding: 0.625rem;
        background: var(--bg-card);
        border-top: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.825rem;
        color: var(--text-secondary);
    }

    .delete-checkbox input[type="checkbox"] {
        cursor: pointer;
        width: 16px;
        height: 16px;
        accent-color: var(--danger);
    }

    .image-upload-container {
        border: 3px dashed var(--border-color);
        border-radius: 16px;
        padding: 2.5rem;
        text-align: center;
        position: relative;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, rgba(240, 171, 221, 0.03), rgba(184, 224, 210, 0.03));
        cursor: pointer;
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    .image-upload-container:hover,
    .image-upload-container.highlight {
        border-color: var(--accent-primary);
        background: linear-gradient(135deg, rgba(240, 171, 221, 0.08), rgba(184, 224, 210, 0.08));
        transform: scale(1.01);
    }

    .file-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
    }

    .upload-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        pointer-events: none;
    }

    .upload-placeholder svg {
        color: var(--accent-primary);
        margin-bottom: 1rem;
        transition: all 0.3s;
    }

    .image-upload-container:hover .upload-placeholder svg {
        transform: translateY(-4px) scale(1.1);
    }

    .upload-placeholder p {
        color: var(--text-primary);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .file-info {
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .preview-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 1rem;
        width: 100%;
        margin-top: 1.5rem;
        z-index: 20;
    }

    .image-preview-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .image-preview-item:hover {
        border-color: var(--accent-primary);
        box-shadow: var(--shadow-sm);
        transform: scale(1.05);
    }

    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 2px solid var(--border-color);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.75rem;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1rem;
        letter-spacing: 0.01em;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
        color: white;
        box-shadow: var(--shadow-sm);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--bg-input);
        color: var(--text-secondary);
        border-color: var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--bg-card);
        border-color: var(--border-focus);
        color: var(--text-primary);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-container {
            padding: 1.5rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Theme Toggle Functionality
        const themeToggle = document.getElementById('themeToggle');
        const rootElement = document.documentElement;
        
        // Check for saved theme preference or default to system preference
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        // Set initial theme
        if (savedTheme) {
            rootElement.setAttribute('data-theme', savedTheme);
        } else if (systemPrefersDark) {
            rootElement.setAttribute('data-theme', 'dark');
        } else {
            rootElement.setAttribute('data-theme', 'light');
        }
        
        // Toggle theme on button click
        themeToggle.addEventListener('click', function() {
            const currentTheme = rootElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            rootElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Add a subtle animation
            this.style.transform = 'rotate(360deg)';
            setTimeout(() => {
                this.style.transform = '';
            }, 300);
        });
        
        // Dynamic Stock Logic
        const unitSelect = document.getElementById('unit');
        const stockContainer = document.getElementById('stock-container');

        unitSelect.addEventListener('change', updateStockInputs);

        function updateStockInputs() {
            const selectedOptions = Array.from(unitSelect.selectedOptions);
            const currentInputs = {};
            const currentPrices = {};
            
            // Save current values
            stockContainer.querySelectorAll('input[name="stock[]"]').forEach(input => {
                currentInputs[input.dataset.unit] = input.value;
            });
            stockContainer.querySelectorAll('input[name="price[]"]').forEach(input => {
                currentPrices[input.dataset.unit] = input.value;
            });

            stockContainer.innerHTML = '';

            if (selectedOptions.length === 0) {
                stockContainer.innerHTML = '<p class="text-muted text-center" style="padding: 2rem; border: 1px dashed #cbd5e0; border-radius: 0.5rem;">Seleccione unidades para asignar stock y precio</p>';
                return;
            }

            selectedOptions.forEach(option => {
                const unit = option.value;
                const unitName = option.text;
                const stockValue = currentInputs[unit] || '';
                const priceValue = currentPrices[unit] || '';

                const div = document.createElement('div');
                div.className = 'stock-item';
                div.innerHTML = `
                    <div class="stock-label">${unitName}</div>
                    <div class="stock-input-group">
                        <div style="flex: 1;">
                            <label style="font-size: 0.75rem; color: #718096; margin-bottom: 0.25rem; display: block">Stock</label>
                            <input type="number" class="form-control" name="stock[]" placeholder="Cantidad" value="${stockValue}" required data-unit="${unit}">
                        </div>
                        <div style="flex: 1;">
                            <label style="font-size: 0.75rem; color: #718096; margin-bottom: 0.25rem; display: block;">Precio (S/.)</label>
                            <input type="number" step="0.01" class="form-control" name="price[]" placeholder="0.00" value="${priceValue}" required data-unit="${unit}">
                        </div>
                    </div>
                `;
                stockContainer.appendChild(div);
            });
        }

        // --- Image Upload Logic ---
        const dropZone = document.getElementById('dropZone');
        const imageInput = document.getElementById('imageInput');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const previewContainer = document.getElementById('previewContainer');

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop zone when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('highlight');
        }

        function unhighlight(e) {
            dropZone.classList.remove('highlight');
        }

        // Handle dropped files
        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            imageInput.files = files; // Update input files
            handleFiles(files);
        }

        imageInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            if (files.length > 0) {
                uploadPlaceholder.style.display = 'none';
                previewContainer.style.display = 'grid'; // Use grid for multiple images
                previewContainer.innerHTML = ''; // Clear previous previews

                Array.from(files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const previewDiv = document.createElement('div');
                            previewDiv.className = 'image-preview-item';
                            previewDiv.innerHTML = `
                                <img src="${e.target.result}" alt="Preview">
                            `;
                            previewContainer.appendChild(previewDiv);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                uploadPlaceholder.style.display = 'flex';
                previewContainer.style.display = 'none';
            }
        }
    });
</script>
@endpush
@endsection
