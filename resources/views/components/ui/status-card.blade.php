@props(['title', 'icon' => 'clock', 'tone' => 'warning'])

@php
    $icons = [
        'clock' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z',
        'check' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z',
    ];

    $tones = [
        'warning' => 'bg-amber-500/15 text-amber-600 dark:text-amber-300',
        'success' => 'bg-success/15 text-success',
    ];
@endphp

{{-- Página de estado: un ícono, un título y el detalle debajo --}}
<div class="mx-auto flex max-w-xl items-center justify-center pt-10 pb-16">
    <x-ui.card class="flex w-full flex-col items-center gap-5 px-8 py-12 text-center">
        <span @class(['flex size-20 items-center justify-center rounded-full', $tones[$tone]])>
            <svg class="size-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$icon] }}"/></svg>
        </span>

        <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ $title }}</h1>

        {{ $slot }}
    </x-ui.card>
</div>
