@props([
    'title',
    'subtitle' => null,
    'sideTitle' => '¡Hola, Amigo!',
    'sideText' => null,
    'sideLink' => null,
    'sideLinkLabel' => null,
    'sideFirst' => false,
])

@php
    $statusMessages = [
        'verification-link-sent' => 'Te enviamos un nuevo enlace de verificación.',
        'password-updated' => 'Tu contraseña se actualizó.',
    ];
@endphp

<div class="mx-auto flex max-w-4xl items-center justify-center pt-6 pb-12">
    <div @class(['grid w-full overflow-hidden rounded-[2rem] border border-line bg-surface shadow-lifted md:grid-cols-2', 'md:[&>*:first-child]:order-2' => ! $sideFirst])>
        <aside class="flex flex-col items-center justify-center gap-4 bg-linear-135 from-brand-strong to-brand px-8 py-10 text-center text-white md:py-16">
            <h2 class="text-2xl font-bold sm:text-3xl">{{ $sideTitle }}</h2>
            @if ($sideText)
                <p class="max-w-xs text-sm text-white/90">{{ $sideText }}</p>
            @endif
            @if ($sideLink)
                <a href="{{ $sideLink }}" class="mt-2 rounded-pill border-2 border-white px-8 py-2 text-sm font-semibold transition hover:bg-white hover:text-brand-strong">{{ $sideLinkLabel }}</a>
            @endif
        </aside>

        <div class="flex flex-col gap-6 px-6 py-10 sm:px-10">
            <header class="text-center">
                <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="mt-2 text-sm text-muted">{{ $subtitle }}</p>
                @endif
            </header>

            @if (session('status') && ! str_starts_with((string) session('status'), 'two-factor') && session('status') !== 'recovery-codes-generated')
                <x-ui.alert type="success">{{ $statusMessages[session('status')] ?? session('status') }}</x-ui.alert>
            @endif

            {{ $slot }}
        </div>
    </div>
</div>
