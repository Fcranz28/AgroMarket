@props(['product'])

@php
    use App\Modules\Shared\Money;

    $units = $product->relationLoaded('units') ? $product->units->sortBy(fn ($unit) => (float) $unit->price)->values() : collect();

    $tag = match (true) {
        $product->stock > 0 && $product->stock <= 10 => ['Pocas unidades', 'bg-[#F2C14E] text-[#2A1E00]'],
        $units->count() > 1 => ['Por mayor', 'bg-surface text-ink'],
        default => null,
    };
@endphp

{{-- El botón avisa al carrito con el evento "add-to-cart" de Livewire --}}
<article {{ $attributes->class('group flex flex-col gap-3 rounded-card border border-line bg-surface p-3 shadow-soft transition hover:-translate-y-1 hover:shadow-card') }}>
    <a href="{{ route('products.show', $product->slug) }}" class="relative block aspect-[4/3] overflow-hidden rounded-xl">
        <x-catalog.product-image :product="$product" class="size-full transition duration-300 group-hover:scale-105" />
        @if ($tag)
            <span class="absolute top-2.5 left-2.5 rounded-pill px-2.5 py-1 text-[11px] font-bold shadow-soft {{ $tag[1] }}">{{ $tag[0] }}</span>
        @endif
    </a>

    <div class="flex flex-col gap-0.5 px-1.5">
        @if ($product->relationLoaded('category') && $product->category)
            <span class="text-[11px] font-bold tracking-[0.08em] text-brand uppercase">{{ $product->category->name }}</span>
        @endif
        <a href="{{ route('products.show', $product->slug) }}" class="line-clamp-2 text-lg font-extrabold tracking-tight text-ink hover:text-brand">{{ $product->name }}</a>
    </div>

    @if ($units->count() > 1)
        <div class="flex flex-wrap gap-1.5 px-1.5">
            @foreach ($units as $unit)
                <span @class(['rounded-lg px-2 py-1 text-xs font-bold', 'bg-night text-lime' => $loop->first, 'bg-canvas text-muted' => ! $loop->first])>{{ $unit->unit }}</span>
            @endforeach
        </div>
    @endif

    <div class="mt-auto flex items-center justify-between gap-2 px-1.5 pb-1">
        <p class="min-w-0 text-ink">
            <span class="block text-xl leading-tight font-extrabold tracking-tight whitespace-nowrap">{{ Money::fromDecimal($product->price)->format() }}</span>
            <span class="block truncate text-sm text-muted">por {{ $product->unit }}</span>
        </p>

        @if ($product->stock > 0)
            <x-ui.button variant="dark" size="sm" class="h-11 shrink-0" data-test="add-to-cart-{{ $product->id }}" aria-label="Agregar {{ $product->name }} al carrito" x-data x-on:click="Livewire.dispatch('add-to-cart', { productId: {{ $product->id }} })">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Agregar
            </x-ui.button>
        @else
            <span class="rounded-lg bg-canvas px-3 py-2 text-sm font-semibold text-muted">Sin stock</span>
        @endif
    </div>
</article>
