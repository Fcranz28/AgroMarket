@props(['product'])

@if ($product->image_path)
    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" loading="lazy" {{ $attributes->class('object-cover') }}>
@else
    {{-- Sin foto: una ilustración según el nombre o la categoría --}}
    <div role="img" aria-label="{{ $product->name }}" {{ $attributes->class('flex items-center justify-center bg-brand-soft p-[12%]') }}>
        <x-catalog.illustration :name="$product->name" :category="$product->relationLoaded('category') ? $product->category?->slug : null" class="size-full max-h-full" />
    </div>
@endif
