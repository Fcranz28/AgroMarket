@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button'])

@php
    $variants = [
        'primary' => 'bg-lime text-night shadow-soft hover:bg-lime-strong',
        'secondary' => 'border border-line bg-surface text-ink hover:border-ink',
        'dark' => 'bg-night text-lime hover:bg-night-soft',
        'ghost' => 'text-ink hover:bg-brand-soft hover:text-brand',
        'danger' => 'bg-danger text-white hover:opacity-90',
    ];

    $sizes = [
        'sm' => 'px-3.5 py-2 text-sm',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-6 py-3.5 text-base',
    ];

    $classes = 'inline-flex items-center justify-center gap-2 rounded-control font-bold transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:pointer-events-none disabled:opacity-50 '
        .$variants[$variant].' '.$sizes[$size];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
