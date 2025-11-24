@extends('layouts.app')

@section('content')
<div class="container">
    <div class="form-container">
        <h2>Agregar Nuevo Producto</h2>
        
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
            
            <div class="form-group">
                <label for="name">Nombre del Producto</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="category_id">Categoría</label>
                    <select class="form-control" id="category_id" name="category_id" required>
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
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="unit">Unidad de Medida (Seleccione una o más)</label>
                    <select class="form-control" id="unit" name="unit[]" multiple required style="height: 150px;">
                        <option value="kg">Kilogramo (kg)</option>
                        <option value="g">Gramo (g)</option>
                        <option value="lb">Libra (lb)</option>
                        <option value="unidad">Unidad</option>
                        <option value="docena">Docena</option>
                        <option value="saco">Saco</option>
                        <option value="caja">Caja</option>
                        <option value="atado">Atado</option>
                        <option value="manojo">Manojo</option>
                        <option value="litro">Litro</option>
                        <option value="ml">Mililitro (ml)</option>
                    </select>
                    <small class="text-muted">Mantenga presionado Ctrl (Windows) o Cmd (Mac) para seleccionar múltiples opciones.</small>
                </div>

                <div class="form-group col-md-6">
                    <label>Stock y Precio por Unidad</label>
                    <div id="stock-container" class="stock-container">
                        <p class="text-muted text-center" style="padding: 2rem; border: 1px dashed #cbd5e0; border-radius: 0.5rem;">Seleccione unidades para asignar stock y precio</p>
                    </div>
                </div>
            </div>

            <div class="form-group full-width">
                <label>Imágenes del Producto</label>
                <div class="image-upload-container" id="dropZone">
                    <input type="file" name="image[]" id="imageInput" class="file-input" accept="image/*" multiple>
                    <div class="upload-placeholder" id="uploadPlaceholder">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <p>Arrastra y suelta tus imágenes aquí o haz clic para seleccionar</p>
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
                <button type="submit" class="btn btn-primary">Guardar Producto</button>
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
    }

    .stock-input-group {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Drag & Drop Styles */
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

    .upload-placeholder p {
        font-size: 1.1rem;
        color: #4a5568;
        margin-bottom: 0.5rem;
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
    
    .error-message {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
    }
</style>
@endpush
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
