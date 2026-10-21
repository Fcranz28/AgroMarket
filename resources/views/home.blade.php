@extends('layouts.app')

@section('content')
    <section class="hero-banner" style="background-image: url('{{ asset('img/hero-market-hd.jpg') }}');">
        <div class="hero-overlay">
            <div class="hero-content">
                <h1 class="hero-title">Productos Frescos Directamente del Campo</h1>
                <p class="hero-subtitle">Conectamos agricultores locales con tu hogar</p>
                <a href="{{ route('products.index') }}" class="hero-btn">Explorar Productos</a>
            </div>
        </div>
    </section>

    <div class="mx-auto flex max-w-7xl flex-col gap-14 py-12">
        @if ($featuredProducts->isNotEmpty())
            <section aria-labelledby="featured-title">
                <div class="mb-6 flex items-end justify-between gap-4">
                    <h2 id="featured-title" class="text-2xl font-bold text-ink sm:text-3xl">Productos destacados</h2>
                    <a href="{{ route('products.index') }}" class="shrink-0 text-sm font-medium text-brand hover:underline">Ver todos</a>
                </div>

                <div class="-mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-4 [scrollbar-width:thin]">
                    @foreach ($featuredProducts as $product)
                        <x-catalog.product-card :product="$product" class="w-56 shrink-0 snap-start sm:w-64" />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($suggestedProducts->isNotEmpty())
            <section aria-labelledby="suggested-title">
                <h2 id="suggested-title" class="mb-6 text-2xl font-bold text-ink sm:text-3xl">Productos que te pueden interesar</h2>

                <div class="grid grid-cols-2 gap-3 sm:gap-5 md:grid-cols-3 xl:grid-cols-4">
                    @foreach ($suggestedProducts as $product)
                        <x-catalog.product-card :product="$product" />
                    @endforeach
                </div>

                <div class="mt-8 flex justify-center">
                    <x-ui.button variant="secondary" size="lg" :href="route('products.index')">Ver más productos</x-ui.button>
                </div>
            </section>
        @endif
    </div>
@endsection
