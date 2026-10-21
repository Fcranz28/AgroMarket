@props(['product'])

@if ($product->image_path)
    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" loading="lazy" {{ $attributes->class('object-cover') }}>
@else
    <div role="img" aria-label="{{ $product->name }}" {{ $attributes->class('flex items-center justify-center bg-brand-soft text-brand') }}>
        <svg class="size-1/2 max-h-16 max-w-16 fill-current opacity-60" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg>
    </div>
@endif
