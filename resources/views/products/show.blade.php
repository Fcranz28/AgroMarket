@extends('layouts.app')

@php
    use App\Modules\Shared\Money;

    $gallery = collect([$product->image_path])
        ->merge($product->images->pluck('image_path'))
        ->filter()
        ->unique()
        ->map(fn (string $path) => Storage::url($path))
        ->values();

    // Unidades a la venta; los productos antiguos sin unidades se venden en su unidad base
    $units = $product->units->isNotEmpty()
        ? $product->units->sortBy(fn ($unit) => (float) $unit->price)->values()->map(fn ($unit) => [
            'id' => $unit->id,
            'name' => $unit->unit,
            'price' => Money::fromDecimal($unit->price)->format(),
            'stock' => $unit->stock,
        ])
        : collect([['id' => null, 'name' => $product->unit, 'price' => Money::fromDecimal($product->price)->format(), 'stock' => $product->stock]]);

    $reviews = $product->reviews->sortByDesc('created_at');
    $averageRating = $reviews->avg('rating');
@endphp

@section('content')
    <div class="mx-auto flex max-w-6xl flex-col gap-12 pt-6 pb-12">
        <nav class="text-sm text-muted" aria-label="Ruta">
            <a href="{{ route('products.index') }}" class="hover:text-brand">Productos</a>
            @if ($product->category)
                <span class="mx-1">/</span>
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-brand">{{ $product->category->name }}</a>
            @endif
        </nav>

        <div class="grid gap-8 lg:grid-cols-2">
            {{-- Galería --}}
            <div x-data="{ current: 0, images: @js($gallery) }" class="flex flex-col gap-3">
                <div class="aspect-square overflow-hidden rounded-card border border-line bg-surface">
                    @if ($gallery->isNotEmpty())
                        <img x-bind:src="images[current]" src="{{ $gallery->first() }}" alt="{{ $product->name }}" class="size-full object-cover">
                    @else
                        <x-catalog.product-image :product="$product" class="size-full" />
                    @endif
                </div>

                @if ($gallery->count() > 1)
                    <div class="flex gap-2 overflow-x-auto">
                        @foreach ($gallery as $index => $url)
                            <button type="button" x-on:click="current = {{ $index }}" x-bind:class="current === {{ $index }} ? 'ring-2 ring-brand' : 'opacity-70 hover:opacity-100'" class="size-20 shrink-0 overflow-hidden rounded-lg transition" aria-label="Ver imagen {{ $index + 1 }}">
                                <img src="{{ $url }}" alt="" class="size-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Compra: el carrito (Livewire) escucha el evento "add-to-cart" --}}
            <div
                x-data="{
                    units: @js($units),
                    selected: 0,
                    quantity: 1,
                    get unit() { return this.units[this.selected]; },
                    select(index) { this.selected = index; this.quantity = 1; },
                    add() { Livewire.dispatch('add-to-cart', { productId: {{ $product->id }}, unitId: this.unit.id, quantity: this.quantity }); },
                }"
                class="flex flex-col gap-5"
            >
                <div>
                    <h1 class="text-3xl font-bold text-ink sm:text-4xl">{{ $product->name }}</h1>
                    @if ($averageRating)
                        <p class="mt-2 flex items-center gap-2 text-sm text-muted">
                            <span class="text-amber-500">{{ str_repeat('★', (int) round($averageRating)) }}{{ str_repeat('☆', 5 - (int) round($averageRating)) }}</span>
                            {{ number_format($averageRating, 1) }} · {{ trans_choice('{1} 1 reseña|[2,*] :count reseñas', $reviews->count()) }}
                        </p>
                    @endif
                </div>

                <p class="text-3xl font-bold text-brand">
                    <span x-text="unit.price">{{ $units->first()['price'] }}</span>
                    <span class="text-base font-normal text-muted">/ <span x-text="unit.name">{{ $units->first()['name'] }}</span></span>
                </p>

                <p class="leading-relaxed text-muted">{{ $product->description ?: 'Sin descripción disponible.' }}</p>

                <div class="flex flex-col gap-2">
                    <span class="text-sm font-medium text-ink">Unidad de medida</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($units as $index => $unit)
                            <button type="button" x-on:click="select({{ $index }})" x-bind:class="selected === {{ $index }} ? 'border-brand bg-brand text-white' : 'border-line bg-surface text-ink hover:border-brand'" class="rounded-pill border px-4 py-1.5 text-sm font-medium transition">{{ $unit['name'] }}</button>
                        @endforeach
                    </div>
                    <p class="text-sm text-muted">Stock disponible: <span class="font-semibold text-ink" x-text="unit.stock">{{ $units->first()['stock'] }}</span></p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center rounded-pill border border-line bg-surface">
                        <button type="button" x-on:click="quantity = Math.max(1, quantity - 1)" class="px-4 py-2 text-lg text-muted hover:text-brand" aria-label="Quitar uno">−</button>
                        <input type="number" x-model.number="quantity" min="1" x-bind:max="unit.stock" class="w-14 bg-transparent text-center font-semibold text-ink outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none" aria-label="Cantidad">
                        <button type="button" data-test="quantity-increase" x-on:click="quantity = Math.min(unit.stock, quantity + 1)" class="px-4 py-2 text-lg text-muted hover:text-brand" aria-label="Agregar uno">+</button>
                    </div>

                    <x-ui.button size="lg" class="flex-1" data-test="product-add-to-cart" x-on:click="add()" x-bind:disabled="unit.stock < 1">
                        <span x-text="unit.stock < 1 ? 'Sin stock' : 'Agregar al carrito'">Agregar al carrito</span>
                    </x-ui.button>
                </div>

                @if ($product->user)
                    <p class="text-sm text-muted">Vendido por <span class="font-medium text-ink">{{ $product->user->name }}</span></p>
                @endif
            </div>
        </div>

        {{-- Reseñas --}}
        <section class="grid gap-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]" aria-labelledby="reviews-title">
            <div>
                <h2 id="reviews-title" class="mb-4 text-2xl font-bold text-ink">Reseñas</h2>

                @auth
                    <x-ui.card>
                        <form action="{{ route('reviews.store', $product->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4" x-data="{ rating: {{ (int) old('rating', 0) }}, hover: 0 }">
                            @csrf
                            <h3 class="font-semibold text-ink">Deja tu comentario</h3>

                            <fieldset>
                                <legend class="mb-1 text-sm font-medium text-ink">Calificación</legend>
                                <div class="flex gap-1 text-3xl" x-on:mouseleave="hover = 0">
                                    @for ($star = 1; $star <= 5; $star++)
                                        <label class="cursor-pointer" x-on:mouseenter="hover = {{ $star }}" x-bind:class="(hover || rating) >= {{ $star }} ? 'text-amber-500' : 'text-line'">
                                            <input type="radio" name="rating" value="{{ $star }}" x-model.number="rating" class="sr-only" required>
                                            <span aria-hidden="true">★</span>
                                            <span class="sr-only">{{ $star }} {{ $star === 1 ? 'estrella' : 'estrellas' }}</span>
                                        </label>
                                    @endfor
                                </div>
                                @error('rating') <p class="text-xs text-danger">{{ $message }}</p> @enderror
                            </fieldset>

                            <x-ui.textarea name="comment" label="Comentario" rows="3" placeholder="Comparte tu experiencia..." />
                            <x-ui.image-upload name="image" label="Foto (opcional)" hint="Máximo 2 MB" />

                            <x-ui.button type="submit">Publicar reseña</x-ui.button>
                        </form>
                    </x-ui.card>
                @else
                    <x-ui.card class="text-sm text-muted">
                        <a href="{{ route('login') }}" class="font-medium text-brand hover:underline">Inicia sesión</a> para dejar un comentario.
                    </x-ui.card>
                @endauth
            </div>

            <div class="flex flex-col gap-4 lg:pt-12">
                @forelse ($reviews as $review)
                    <article class="rounded-card border border-line bg-surface p-5">
                        <header class="flex items-center gap-3">
                            <span class="flex size-10 items-center justify-center rounded-full bg-brand font-semibold text-white">{{ mb_strtoupper(mb_substr($review->user->name, 0, 1)) }}</span>
                            <div class="flex-1">
                                <p class="font-medium text-ink">{{ $review->user->name }}</p>
                                <p class="text-xs text-muted">{{ $review->created_at->format('d/m/Y') }}</p>
                            </div>
                            <span class="text-amber-500" aria-label="{{ $review->rating }} de 5 estrellas">{{ str_repeat('★', $review->rating) }}<span class="text-line">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
                        </header>

                        @if ($review->comment)
                            <p class="mt-3 text-sm text-muted">{{ $review->comment }}</p>
                        @endif

                        @if ($review->image_path)
                            <a href="{{ Storage::url($review->image_path) }}" target="_blank" class="mt-3 block w-fit">
                                <img src="{{ Storage::url($review->image_path) }}" alt="Foto de la reseña" class="max-h-40 rounded-lg">
                            </a>
                        @endif
                    </article>
                @empty
                    <p class="rounded-card border border-dashed border-line p-8 text-center text-muted">Aún no hay reseñas para este producto.</p>
                @endforelse
            </div>
        </section>

        @if ($relatedProducts->isNotEmpty())
            <section aria-labelledby="related-title">
                <h2 id="related-title" class="mb-6 text-2xl font-bold text-ink">Productos que te pueden interesar</h2>
                <div class="grid grid-cols-2 gap-3 sm:gap-5 md:grid-cols-4">
                    @foreach ($relatedProducts as $related)
                        <x-catalog.product-card :product="$related" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
