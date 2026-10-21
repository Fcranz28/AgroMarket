@props(['label', 'value', 'icon', 'hint' => null])

<div {{ $attributes->class('flex items-start justify-between gap-4 rounded-card border border-line bg-surface p-5 shadow-soft') }}>
    <div class="min-w-0">
        <p class="text-xs font-semibold tracking-wide text-muted uppercase">{{ $label }}</p>
        <p class="mt-2 truncate text-2xl font-bold text-ink sm:text-3xl">{{ $value }}</p>
        @if ($hint)
            <p class="mt-1 text-xs text-muted">{{ $hint }}</p>
        @endif
    </div>
    <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-brand-soft text-lg text-brand"><i class="fas {{ $icon }}"></i></span>
</div>
