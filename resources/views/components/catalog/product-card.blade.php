@props(['product'])

{{-- El botón avisa al carrito con el evento "add-to-cart" de Livewire --}}
<article {{ $attributes->class('group flex flex-col overflow-hidden rounded-card border border-line bg-surface shadow-soft transition hover:-translate-y-1 hover:shadow-lifted') }}>
    <a href="{{ route('products.show', $product->slug) }}" class="block aspect-square overflow-hidden">
        <x-catalog.product-image :product="$product" class="size-full transition duration-300 group-hover:scale-105" />
    </a>

    <div class="flex flex-1 flex-col gap-1 p-4">
        @if ($product->relationLoaded('category') && $product->category)
            <span class="text-xs font-medium tracking-wide text-muted uppercase">{{ $product->category->name }}</span>
        @endif

        <a href="{{ route('products.show', $product->slug) }}" class="line-clamp-2 font-semibold text-ink hover:text-brand">{{ $product->name }}</a>

        <p class="mt-auto pt-2 text-lg font-bold text-brand">
            {{ \App\Modules\Shared\Money::fromDecimal($product->price)->format() }}
            <span class="text-sm font-normal text-muted">/ {{ $product->unit }}</span>
        </p>

        @if ($product->stock > 0)
            <x-ui.button size="sm" class="mt-2 w-full" x-data x-on:click="Livewire.dispatch('add-to-cart', { productId: {{ $product->id }} })">
                <svg class="size-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M11 9h2V6h3V4h-3V1h-2v3H8v2h3v3zm-4 9c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-9.83-3.25.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L19.42 4h-.01l-1.1 2-2.76 5H8.53l-.13-.27L6.16 6l-.95-2-.94-2H1v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.13 0-.25-.11-.25-.25z"/></svg>
                Agregar al carrito
            </x-ui.button>
        @else
            <x-ui.button size="sm" variant="secondary" class="mt-2 w-full" disabled>Sin stock</x-ui.button>
        @endif
    </div>
</article>
