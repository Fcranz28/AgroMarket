@extends('layouts.app')

@section('content')
<div class="container product-detail-container" id="productDetailContainer" data-product="{{ json_encode($product) }}">
    <div class="product-detail-grid">
        <!-- Galería de Imágenes -->
        <div class="product-detail-gallery">
            <div class="product-detail-main-image">
                @if($product->image_path)
                    <img id="mainImage" src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}">
                @elseif($product->image_url)
                    <img id="mainImage" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                @else
                    <img id="mainImage" src="{{ asset('img/placeholder.png') }}" alt="Sin imagen">
                @endif
            </div>
            
            @if($product->images->count() > 0)
                <div class="product-detail-thumbnails">
                    <!-- Main image thumbnail -->
                    <div class="product-detail-thumbnail active" data-src="{{ $product->image_path ? Storage::url($product->image_path) : ($product->image_url ?? asset('img/placeholder.png')) }}">
                        <img src="{{ $product->image_path ? Storage::url($product->image_path) : ($product->image_url ?? asset('img/placeholder.png')) }}" alt="Main">
                    </div>
                    
                    <!-- Additional images -->
                    @foreach($product->images as $image)
                        @if($image->image_path != $product->image_path)
                            <div class="product-detail-thumbnail" data-src="{{ Storage::url($image->image_path) }}">
                                <img src="{{ Storage::url($image->image_path) }}" alt="Thumbnail">
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Información del Producto -->
        <div class="product-detail-info">
            <h1 class="product-detail-title">{{ $product->name }}</h1>
            <p class="product-detail-price">S/. {{ number_format($product->price, 2) }}</p>
            
            <div class="product-detail-description">
                <p>{{ $product->description ?? 'Sin descripción disponible.' }}</p>
            </div>

            <div class="product-detail-options">
                <!-- Selección de Unidad -->
                <div class="form-group">
                    <label class="product-detail-option-label">Unidad de Medida:</label>
                    <div id="unitButtonsContainer" class="product-detail-units">
                        @if($product->units->count() > 0)
                            @foreach($product->units as $unit)
                                <button type="button" 
                                        class="product-detail-unit-btn {{ $loop->first ? 'active' : '' }}" 
                                        data-unit="{{ $unit->unit }}" 
                                        data-price="{{ $unit->price }}" 
                                        data-stock="{{ $unit->stock }}">
                                    {{ $unit->unit }}
                                </button>
                            @endforeach
                        @else
                            <!-- Fallback for legacy products -->
                            <button type="button" 
                                    class="product-detail-unit-btn active" 
                                    data-unit="{{ $product->unit }}" 
                                    data-price="{{ $product->price }}" 
                                    data-stock="{{ $product->stock }}">
                                {{ $product->unit }}
                            </button>
                        @endif
                    </div>
                    <input type="hidden" id="selectedUnit" value="">
                    <input type="hidden" id="selectedPrice" value="">
                </div>

                <!-- Stock Disponible -->
                <div class="product-detail-stock">
                    Stock disponible: <span id="stockDisplay">0</span>
                </div>

                <!-- Cantidad -->
                <div class="product-detail-qty-selector">
                    <button class="product-detail-qty-btn" id="decreaseQty">-</button>
                    <input type="number" id="quantityInput" value="1" min="1" class="product-detail-qty-input">
                    <button class="product-detail-qty-btn" id="increaseQty">+</button>
                </div>

                <button id="addToCartBtn" class="btn btn-primary btn-lg btn-block">
                    Agregar al Carrito
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Productos Relacionados -->
    <div class="related-products-section">
        <h2 class="product-detail-section-title">Productos que te pueden interesar</h2>
        @if($relatedProducts->count() > 0)
            <div class="product-detail-related-marquee">
                <div class="marquee-track">
                    {{-- Original Loop --}}
                    @foreach($relatedProducts as $related)
                        <div class="product-detail-related-card">
                            <a href="{{ route('products.show', $related->slug) }}" class="product-detail-related-link">
                                <div class="product-detail-related-image">
                                    @if($related->image_path)
                                        <img src="{{ Storage::url($related->image_path) }}" alt="{{ $related->name }}">
                                    @elseif($related->image_url)
                                        <img src="{{ $related->image_url }}" alt="{{ $related->name }}">
                                    @else
                                        <img src="{{ asset('img/placeholder.png') }}" alt="Sin imagen">
                                    @endif
                                </div>
                                <div class="product-detail-related-info">
                                    <h3>{{ $related->name }}</h3>
                                    <p class="price">S/. {{ number_format($related->price, 2) }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach


                </div>
            </div>
        @else
            <p class="no-related">No hay productos relacionados disponibles.</p>
        @endif
    </div>

    <!-- Sección de Reseñas -->
    <div class="product-detail-reviews">
        <h2 class="product-detail-section-title">Reseñas y Comentarios</h2>
        
        <!-- Formulario de Reseña (Solo logueados) -->
        @auth
            <div class="product-detail-review-form">
                <h3>Deja tu comentario</h3>
                <form action="{{ route('reviews.store', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Calificación:</label>
                        <div class="product-detail-rating-input">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="star{{$i}}" name="rating" value="{{$i}}" required>
                                <label for="star{{$i}}" title="{{$i}} estrellas">★</label>
                            @endfor
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="comment">Comentario:</label>
                        <textarea name="comment" id="comment" rows="3" class="form-control" placeholder="Comparte tu experiencia..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="d-block mb-2">Foto (opcional):</label>
                        <div class="review-image-upload" id="reviewDropZone">
                            <input type="file" name="image" id="reviewImageInput" class="d-none" accept="image/*">
                            <div class="upload-content" id="reviewUploadPlaceholder">
                                <div class="upload-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                </div>
                                <p>Click para agregar foto</p>
                            </div>
                            <div class="review-preview-container" id="reviewPreviewContainer" style="display: none;">
                                <img id="reviewImagePreview" src="" alt="Vista previa">
                                <button type="button" id="removeReviewImage" class="remove-preview-btn">×</button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Publicar Reseña</button>
                </form>
            </div>
        @else
            <div class="login-prompt">
                <p>Por favor <a href="{{ route('login') }}">inicia sesión</a> para dejar un comentario.</p>
            </div>
        @endauth

        <!-- Lista de Reseñas -->
        <div class="reviews-list">
            @forelse($product->reviews->sortByDesc('created_at') as $review)
                <div class="product-detail-review-card">
                    <div class="product-detail-review-header">
                        <div class="review-avatar">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </div>
                        <div class="review-meta">
                            <span class="product-detail-review-user">{{ $review->user->name }}</span>
                            <span class="product-detail-review-date">{{ $review->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="product-detail-review-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="product-detail-star {{ $i <= $review->rating ? 'filled' : '' }}">★</span>
                            @endfor
                        </div>
                    </div>
                    <div class="product-detail-review-content">
                        <p>{{ $review->comment }}</p>
                        @if($review->image_path)
                            <div class="product-detail-review-image">
                                <img src="{{ Storage::url($review->image_path) }}" alt="Foto de reseña" onclick="window.open(this.src)">
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="no-reviews">Aún no hay reseñas para este producto.</p>
            @endforelse
        </div>
    </div>
</div>

@push('styles')
{{-- Styles loaded via app.css → products/show.css --}}
@endpush

@push('scripts')
@push('scripts')
    @vite(['resources/js/products/show.js'])
@endpush
@endsection
