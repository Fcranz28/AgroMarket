@props(['type' => 'info', 'dismissible' => false])

@php
    $types = [
        'info' => 'border-sky-500/30 bg-sky-500/10 text-sky-800 dark:text-sky-200',
        'success' => 'border-success/30 bg-success/10 text-success',
        'warning' => 'border-amber-500/30 bg-amber-500/10 text-amber-800 dark:text-amber-200',
        'error' => 'border-danger/30 bg-danger/10 text-danger',
    ];
@endphp

<div
    role="{{ $type === 'error' ? 'alert' : 'status' }}"
    @if ($dismissible) x-data="{ open: true }" x-show="open" x-transition.opacity @endif
    {{ $attributes->class('flex items-start gap-3 rounded-xl border px-4 py-3 text-sm '.$types[$type]) }}
>
    <div class="flex-1">{{ $slot }}</div>

    @if ($dismissible)
        <button type="button" x-on:click="open = false" class="-m-1 rounded-md p-1 opacity-70 transition hover:opacity-100" aria-label="Cerrar">
            <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
        </button>
    @endif
</div>
