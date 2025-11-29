@extends('layouts.app')

@section('content')
    <!-- Hero Banner Section -->
    <section class="hero-banner" style="background-image: url('{{ asset('img/hero-market-hd.jpg') }}');">
        <div class="hero-overlay">
            <div class="hero-content">
                <h1 class="hero-title">Productos Frescos Directamente del Campo</h1>
                <p class="hero-subtitle">Conectamos agricultores locales con tu hogar</p>
                <a href="{{ route('products.index') }}" class="hero-btn">Explorar Productos</a>
            </div>
        </div>
    </section>

    <section class="products-section">
        <h2 class="section-title">Productos Destacados</h2>
        <div class="products-carousel">
            <div class="carousel-content">
                <!-- Las cards de productos se insertarán aquí mediante JavaScript -->
            </div>
        </div>
    </section>

    <section class="random-products">
        <h2 class="section-title">Productos que te pueden interesar</h2>
        <div class="products-grid">
            <!-- Las cards de productos aleatorios se insertarán aquí mediante JavaScript -->
        </div>
        <div class="load-more-container">
            <button class="load-more-btn">Ver Más...</button>
        </div>
    </section>
@endsection