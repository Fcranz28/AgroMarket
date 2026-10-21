@props(['href' => null])

@php
    $classes = 'flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm text-ink transition hover:bg-brand-soft hover:text-brand';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="submit" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
