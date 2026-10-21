@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button'])

@php
    $variants = [
        'primary' => 'bg-brand text-white shadow-soft hover:bg-brand-strong hover:shadow-card',
        'secondary' => 'border border-line text-ink hover:border-brand hover:text-brand',
        'dark' => 'bg-ink text-canvas hover:bg-brand hover:text-white',
        'ghost' => 'text-ink hover:bg-brand-soft hover:text-brand',
        'danger' => 'bg-danger text-white hover:opacity-90',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-5 py-2 text-sm',
        'lg' => 'px-7 py-3 text-base',
    ];

    $classes = 'inline-flex items-center justify-center gap-2 rounded-pill font-medium transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:pointer-events-none disabled:opacity-50 '
        .$variants[$variant].' '.$sizes[$size];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
