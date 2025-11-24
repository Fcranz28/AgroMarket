@extends('layouts.app')

@section('content')
<div class="container">
    <div class="form-container">
        <h2>Editar Producto: {{ $product->name }}</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('dashboard.productos.update', $product) }}" method="POST" enctype="multipart/form-data">
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
                    <label for="unit">Unidad de Medida (Seleccione una o más)</label>
                    @php
                        $selectedUnits = explode(', ', $product->unit);
                        $stocks = explode(', ', $product->stock);
                        // Create a map of unit => stock
                        $stockMap = [];
                        foreach ($selectedUnits as $index => $unit) {
                            $stockMap[$unit] = $stocks[$index] ?? 0;
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
                    <label>Stock Disponible por Unidad</label>
                    <div id="stock-container" class="stock-container">
                        <!-- Pre-fill existing stocks -->
                        @foreach($selectedUnits as $unit)
                            <div class="stock-item">
                                <div class="stock-label">{{ $unit }}</div>
                                <div class="stock-input-group">
                                    <input type="number" class="form-control" name="stock[]" placeholder="Cantidad" value="{{ $stockMap[$unit] ?? '' }}" required data-unit="{{ $unit }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="form-group full-width">
                <label>Imágenes del Producto</label>
                
                <!-- Existing Images -->
                @if($product->images->count() > 0)
                    <div class="mb-3">
                        <label>Imágenes Actuales (Seleccionar para eliminar)</label>
                        <div class="existing-images-grid">
                            @foreach($product->images as $img)
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
    /* ... (Existing styles) ... */
    .container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    
    /* Stock Container Styles */
    .stock-container {
        background: #f8fafc;
        padding: 1rem;
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
        min-height: 150px;
        max-height: 300px;
        overflow-y: auto;
    }

    .stock-item {
        background: white;
        padding: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .stock-label {
        font-weight: 600;
        color: #2d3748;
        min-width: 80px;
        text-transform: capitalize;
    }

    .stock-input-group {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Existing Images Grid */
    .existing-images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .existing-image-item {
        position: relative;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }

    .existing-image-item img {
        width: 100%;
        height: 100px;
        object-fit: cover;
    }

    .delete-checkbox {
        padding: 0.5rem;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
    }

    /* Drag & Drop Styles (Updated) */
    .image-upload-container {
        border: 2px dashed #cbd5e0;
        border-radius: 0.75rem;
        padding: 2rem;
        text-align: center;
        position: relative;
        transition: all 0.3s ease;
        background: #f8fafc;
        cursor: pointer;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    .image-upload-container:hover, .image-upload-container.highlight {
        border-color: #48bb78;
        background: #f0fff4;
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
        color: #a0aec0;
        margin-bottom: 1rem;
        transition: color 0.3s;
    }

    .image-upload-container:hover .upload-placeholder svg {
        color: #48bb78;
    }

    .file-info {
        font-size: 0.875rem;
        color: #a0aec0;
    }

    .preview-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 1rem;
        width: 100%;
        margin-top: 1rem;
        z-index: 20;
    }

    .image-preview-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dynamic Stock Logic
        const unitSelect = document.getElementById('unit');
        const stockContainer = document.getElementById('stock-container');

        unitSelect.addEventListener('change', updateStockInputs);

        function updateStockInputs() {
            const selectedOptions = Array.from(unitSelect.selectedOptions);
            const currentInputs = {};
            
            // Save current values
            stockContainer.querySelectorAll('input').forEach(input => {
                currentInputs[input.dataset.unit] = input.value;
            });

            stockContainer.innerHTML = '';

            if (selectedOptions.length === 0) {
                stockContainer.innerHTML = '<p class="text-muted text-center" style="padding: 2rem; border: 1px dashed #cbd5e0; border-radius: 0.5rem;">Seleccione unidades para asignar stock</p>';
                return;
            }

            selectedOptions.forEach(option => {
                const unit = option.value;
                const unitName = option.text;
                const value = currentInputs[unit] || ''; // This might need to be pre-filled from PHP for initial load, which is handled by the blade loop above. 
                // Wait, the blade loop pre-fills it. But if user changes selection, we need to preserve or add new.
                // The current logic clears and rebuilds. We need to respect the initial values from PHP if they exist in the DOM.
                // Actually, the blade loop renders the initial state. This JS runs on change.
                // So we just need to make sure we don't lose values when toggling.
                
                const div = document.createElement('div');
                div.className = 'stock-item';
                div.innerHTML = `
                    <div class="stock-label">${unitName}</div>
                    <div class="stock-input-group">
                        <input type="number" class="form-control" name="stock[]" placeholder="Cantidad" value="${value}" required data-unit="${unit}">
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
