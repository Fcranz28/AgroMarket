@extends('layouts.app')

@section('content')
<div class="container product-detail-container">
    <div class="product-detail-grid">
        <!-- Galería de Imágenes -->
        <div class="product-gallery">
            <div class="main-image-container">
                @if($product->image_path)
                    <img id="mainImage" src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}">
                @elseif($product->image_url)
                    <img id="mainImage" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                @else
                    <img id="mainImage" src="{{ asset('img/placeholder.png') }}" alt="Sin imagen">
                @endif
            </div>
            
            @if($product->images->count() > 0)
                <div class="thumbnail-strip">
                    <!-- Main image thumbnail -->
                    <div class="thumbnail active" data-src="{{ $product->image_path ? Storage::url($product->image_path) : ($product->image_url ?? asset('img/placeholder.png')) }}">
                        <img src="{{ $product->image_path ? Storage::url($product->image_path) : ($product->image_url ?? asset('img/placeholder.png')) }}" alt="Main">
                    </div>
                    
                    <!-- Additional images -->
                    @foreach($product->images as $image)
                        @if($image->image_path != $product->image_path)
                            <div class="thumbnail" data-src="{{ Storage::url($image->image_path) }}">
                                <img src="{{ Storage::url($image->image_path) }}" alt="Thumbnail">
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Información del Producto -->
        <div class="product-detail-info">
            <h1 class="product-title">{{ $product->name }}</h1>
            <p class="product-price">S/. {{ number_format($product->price, 2) }}</p>
            
            <div class="product-description">
                <p>{{ $product->description ?? 'Sin descripción disponible.' }}</p>
            </div>

            <div class="product-options">
                <!-- Selección de Unidad -->
                <div class="form-group">
                    <label>Unidad de Medida:</label>
                    <div id="unitButtonsContainer" class="unit-buttons-container">
                        <!-- Se llenará con JS -->
                    </div>
                    <input type="hidden" id="selectedUnit" value="">
                </div>

                <!-- Stock Disponible -->
                <div class="stock-info">
                    Stock disponible: <span id="stockDisplay">0</span>
                </div>

                <!-- Cantidad -->
                <div class="quantity-selector">
                    <button class="qty-btn" id="decreaseQty">-</button>
                    <input type="number" id="quantityInput" value="1" min="1" class="qty-input">
                    <button class="qty-btn" id="increaseQty">+</button>
                </div>

                <button id="addToCartBtn" class="btn btn-primary btn-lg btn-block">
                    Agregar al Carrito
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .product-detail-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .product-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    @media (max-width: 768px) {
        .product-detail-grid {
            grid-template-columns: 1fr;
        }
    }

    .product-detail-image img {
        width: 100%;
        height: auto;
        border-radius: 10px;
        object-fit: cover;
        max-height: 500px;
    }

    /* Gallery Styles */
    .product-gallery {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .main-image-container {
        width: 100%;
        height: 400px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
    }

    .main-image-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .thumbnail-strip {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }

    .thumbnail {
        width: 60px;
        height: 60px;
        border: 2px solid transparent;
        border-radius: 5px;
        overflow: hidden;
        cursor: pointer;
        opacity: 0.7;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .thumbnail.active {
        border-color: #48bb78;
        opacity: 1;
    }

    .thumbnail:hover {
        opacity: 1;
        border-color: #cbd5e0;
    }

    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    /* ... (rest of styles) */

    .product-title {
        font-size: 2rem;
        color: #2d3748;
        margin-bottom: 1rem;
    }
    
    /* ... */
</style>
@endpush

@push('scripts')
<script>
    // Gallery Logic
    function changeImage(thumbnail, src) {
        // Update main image
        document.getElementById('mainImage').src = src;
        
        // Update active class
        document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
        thumbnail.classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const product = @json($product);
        // ... (rest of existing script)

    .product-price {
        font-size: 1.5rem;
        color: #48bb78;
        font-weight: bold;
        margin-bottom: 1.5rem;
    }

    .product-description {
        color: #4a5568;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .product-options {
        background: #f7fafc;
        padding: 1.5rem;
        border-radius: 8px;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        background: white;
    }

    .stock-info {
        margin-bottom: 1.5rem;
        color: #718096;
        font-size: 0.9rem;
    }

    .unit-buttons-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .unit-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.9rem;
        color: #4a5568;
    }

    .unit-btn:hover {
        border-color: #cbd5e0;
        background: #f7fafc;
    }

    .unit-btn.active {
        border-color: #48bb78;
        background: #f0fff4;
        color: #2f855a;
        font-weight: 600;
        box-shadow: 0 0 0 1px #48bb78;
    }

    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .qty-btn {
        width: 40px;
        height: 40px;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qty-input {
        width: 60px;
        height: 40px;
        text-align: center;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
    }

    .btn-block {
        width: 100%;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const product = @json($product);
        const unitContainer = document.getElementById('unitButtonsContainer');
        const selectedUnitInput = document.getElementById('selectedUnit');
        const stockDisplay = document.getElementById('stockDisplay');
        const quantityInput = document.getElementById('quantityInput');
        const decreaseBtn = document.getElementById('decreaseQty');
        const increaseBtn = document.getElementById('increaseQty');
        const addToCartBtn = document.getElementById('addToCartBtn');

        // --- Gallery Logic ---
        const mainImage = document.getElementById('mainImage');
        const thumbnails = document.querySelectorAll('.thumbnail');

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                const src = this.dataset.src;
                mainImage.src = src;
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // --- Unit Logic ---
        const units = product.unit ? product.unit.split(',') : [];
        const stocks = product.stock ? product.stock.toString().split(',') : [];

        // Create Buttons
        units.forEach((unit, index) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'unit-btn';
            btn.textContent = unit.trim();
            btn.dataset.stock = stocks[index] ? stocks[index].trim() : 0;
            btn.dataset.unit = unit.trim();
            
            btn.addEventListener('click', function() {
                // Remove active class from all
                document.querySelectorAll('.unit-btn').forEach(b => b.classList.remove('active'));
                // Add active to clicked
                this.classList.add('active');
                
                // Update state
                selectedUnitInput.value = this.dataset.unit;
                updateStock(parseInt(this.dataset.stock));
            });

            unitContainer.appendChild(btn);

            // Select first by default
            if (index === 0) {
                btn.click();
            }
        });

        function updateStock(stock) {
            stockDisplay.textContent = stock;
            quantityInput.max = stock;
            quantityInput.value = 1; // Reset quantity
            
            if (stock === 0) {
                addToCartBtn.disabled = true;
                addToCartBtn.textContent = 'Sin Stock';
            } else {
                addToCartBtn.disabled = false;
                addToCartBtn.textContent = 'Agregar al Carrito';
            }
        }

        // --- Quantity Logic ---
        decreaseBtn.addEventListener('click', () => {
            let val = parseInt(quantityInput.value);
            if (val > 1) quantityInput.value = val - 1;
        });

        increaseBtn.addEventListener('click', () => {
            let val = parseInt(quantityInput.value);
            let max = parseInt(quantityInput.max);
            if (val < max) quantityInput.value = val + 1;
        });

        // --- Add to Cart Logic ---
        addToCartBtn.addEventListener('click', () => {
            const selectedUnit = selectedUnitInput.value;
            const quantity = parseInt(quantityInput.value);
            
            if (!selectedUnit) {
                alert('Por favor seleccione una unidad');
                return;
            }

            const productToAdd = {
                ...product,
                unit: selectedUnit,
                cantidad: quantity
            };

            const event = new CustomEvent('add-to-cart-detail', {
                detail: { product: productToAdd, quantity: quantity }
            });
            document.dispatchEvent(event);
        });
    });
</script>
@endpush
@endsection
