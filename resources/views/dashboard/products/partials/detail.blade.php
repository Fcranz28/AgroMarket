{{-- Detalle de un producto en los paneles. Variables: $product, $editUrl, $backUrl --}}
@php
    use App\Modules\Shared\Money;

    $gallery = collect([$product->image_path])->merge($product->images->pluck('image_path'))->filter()->unique()->values();
@endphp

<div class="flex flex-wrap items-center justify-between gap-3">
    <a href="{{ $backUrl }}" class="text-sm font-medium text-brand hover:underline">← Volver a productos</a>
    <div class="flex gap-2">
        <x-ui.button variant="secondary" size="sm" :href="route('products.show', $product->slug)" target="_blank"><i class="fas fa-store"></i> Ver en la tienda</x-ui.button>
        <x-ui.button size="sm" :href="$editUrl"><i class="fas fa-pen"></i> Editar</x-ui.button>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">
    <x-ui.card class="flex flex-col gap-3">
        @if ($gallery->isNotEmpty())
            <img src="{{ Storage::url($gallery->first()) }}" alt="{{ $product->name }}" class="aspect-square w-full rounded-xl object-cover">
            @if ($gallery->count() > 1)
                <div class="flex flex-wrap gap-2">
                    @foreach ($gallery->slice(1) as $path)
                        <img src="{{ Storage::url($path) }}" alt="" class="size-16 rounded-lg object-cover">
                    @endforeach
                </div>
            @endif
        @else
            <x-catalog.product-image :product="$product" class="aspect-square w-full rounded-xl" />
        @endif
    </x-ui.card>

    <div class="flex flex-col gap-6">
        <x-ui.card>
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-bold text-ink">{{ $product->name }}</h2>
                    <p class="text-sm text-muted">{{ $product->category?->name }}@if ($product->user) · {{ $product->user->name }}@endif</p>
                </div>
                <x-ui.badge :color="$product->stock > 0 ? 'success' : 'danger'">{{ $product->stock > 0 ? 'Disponible' : 'Agotado' }}</x-ui.badge>
            </div>
            <p class="mt-4 leading-relaxed text-muted">{{ $product->description ?: 'Sin descripción.' }}</p>
        </x-ui.card>

        <x-ui.card :padding="false">
            <h3 class="border-b border-line px-5 py-4 font-semibold text-ink">Unidades de venta</h3>
            <table class="w-full text-left text-sm">
                <thead class="text-xs tracking-wide text-muted uppercase">
                    <tr><th scope="col" class="px-5 py-2 font-semibold">Unidad</th><th scope="col" class="px-5 py-2 font-semibold">Precio</th><th scope="col" class="px-5 py-2 font-semibold">Stock</th></tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($product->units as $unit)
                        <tr><td class="px-5 py-3 text-ink">{{ $unit->unit }}</td><td class="px-5 py-3 text-ink">{{ Money::fromDecimal($unit->price)->format() }}</td><td class="px-5 py-3 text-ink">{{ $unit->stock }}</td></tr>
                    @empty
                        <tr><td class="px-5 py-3 text-ink">{{ $product->unit }}</td><td class="px-5 py-3 text-ink">{{ Money::fromDecimal($product->price)->format() }}</td><td class="px-5 py-3 text-ink">{{ $product->stock }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-ui.card>
    </div>
</div>
