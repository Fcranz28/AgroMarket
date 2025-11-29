@extends('layouts.app')

@section('content')
<div class="container">
    <div class="form-container">
        <div class="form-header">
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
            
            <div class="form-group">
                <label for="name">Nombre del Producto</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required placeholder="Ej. Manzanas Orgánicas">
            </div>

            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Describe tu producto...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
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

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="unit">Unidad de Medida (Seleccione una o más)</label>
                    <select class="form-control" id="unit" name="unit[]" multiple required style="height: 180px;">
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
                        <div class="empty-stock-state">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-package"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            <p>Seleccione unidades para asignar stock y precio</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group full-width">
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

            <div class="form-actions">
                <a href="{{ route('dashboard.productos.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Producto</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
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

    .form-header {
        margin-bottom: 2rem;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 1rem;
    }

    .form-container h2 {
        color: var(--text-primary);
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-container h2::before {
        content: '';
        width: 6px;
        height: 2rem;
        background: linear-gradient(180deg, var(--accent-primary), var(--accent-secondary));
        border-radius: 3px;
    }

    .form-group {
        margin-bottom: 1.75rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 0.95rem;
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
        border-color: var(--accent-primary);
        background: var(--bg-card);
        box-shadow: 0 0 0 4px rgba(122, 165, 55, 0.1);
    }

    .form-control::placeholder {
        color: var(--text-muted);
    }

    select.form-control {
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23a0aec0' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
        appearance: none;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .text-muted {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 0.5rem;
        display: block;
    }

    /* Stock Container */
    .stock-container {
        background: var(--bg-input);
        padding: 1.25rem;
        border-radius: 16px;
        border: 2px dashed var(--border-color);
        min-height: 180px;
        max-height: 400px;
        overflow-y: auto;
        transition: all 0.3s ease;
    }

    .empty-stock-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: 140px;
        color: var(--text-muted);
        text-align: center;
        gap: 1rem;
    }

    .empty-stock-state svg {
        width: 48px;
        height: 48px;
        opacity: 0.5;
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
        box-shadow: var(--shadow-sm);
    }

    .stock-item:hover {
        border-color: var(--accent-primary);
        transform: translateY(-2px);
    }

    .stock-label {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 0.95rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 0.5rem;
    }

    .stock-input-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    /* Image Upload */
    .image-upload-container {
        border: 3px dashed var(--border-color);
        border-radius: 16px;
        padding: 3rem 2rem;
        text-align: center;
        position: relative;
        transition: all 0.3s ease;
        background: var(--bg-input);
        cursor: pointer;
        min-height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    .image-upload-container:hover, .image-upload-container.highlight {
        border-color: var(--accent-primary);
        background: rgba(122, 165, 55, 0.05);
        transform: scale(1.01);
    }

    .upload-icon-wrapper {
        width: 64px;
        height: 64px;
        background: var(--bg-card);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        color: var(--accent-primary);
        transition: all 0.3s ease;
    }

    .image-upload-container:hover .upload-icon-wrapper {
        transform: scale(1.1) rotate(10deg);
        color: var(--accent-hover);
    }

    .upload-placeholder p {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .file-info {
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .preview-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 1.5rem;
        width: 100%;
        margin-top: 2rem;
    }

    .image-preview-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }

    .image-preview-item:hover {
        transform: scale(1.05);
        border-color: var(--accent-primary);
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
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border-color);
    }

    .btn {
        padding: 0.875rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
        color: white;
        box-shadow: 0 4px 15px rgba(122, 165, 55, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(122, 165, 55, 0.4);
    }

    .btn-secondary {
        background: var(--bg-input);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--bg-card);
        color: var(--text-primary);
        border-color: var(--text-muted);
    }

    .alert {
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid transparent;
    }

    .alert-danger {
        background: rgba(255, 184, 184, 0.15);
        border-color: var(--danger);
        color: #c53030;
    }

    .error-message {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        .form-container {
            padding: 1.5rem;
        }
        
        .form-row {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn {
            width: 100%;
        }
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
