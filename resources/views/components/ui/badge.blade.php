@props(['color' => 'brand'])

@php
    $colors = [
        'brand' => 'bg-brand-soft text-brand-strong',
        'neutral' => 'bg-canvas text-muted',
        'success' => 'bg-success/15 text-success',
        'warning' => 'bg-amber-500/15 text-amber-700 dark:text-amber-300',
        'danger' => 'bg-danger/15 text-danger',
        'info' => 'bg-sky-500/15 text-sky-700 dark:text-sky-300',
    ];
@endphp

<span {{ $attributes->class('inline-flex items-center gap-1 rounded-pill px-2.5 py-0.5 text-xs font-semibold '.$colors[$color]) }}>{{ $slot }}</span>
