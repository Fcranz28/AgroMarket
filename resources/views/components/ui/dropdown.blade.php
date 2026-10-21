@props(['align' => 'right', 'width' => 'w-56'])

@php
    $positionClasses = isset($position) && $position->isNotEmpty()
        ? (string) $position
        : \Illuminate\Support\Arr::toCssClasses(['top-full mt-2', 'right-0' => $align === 'right', 'left-0' => $align === 'left']);
@endphp

<div x-data="{ open: false }" x-on:click.outside="open = false" x-on:keydown.escape.window="open = false" {{ $attributes->class('relative') }}>
    <div x-on:click="open = ! open">{{ $trigger }}</div>

    <div
        x-cloak
        x-show="open"
        x-transition
        x-on:click="open = false"
        class="absolute z-50 overflow-hidden rounded-xl border border-line bg-surface py-1 shadow-lifted {{ $width }} {{ $positionClasses }}"
    >
        {{ $slot }}
    </div>
</div>
