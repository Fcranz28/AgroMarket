@props(['padding' => true])

<div {{ $attributes->class(['rounded-card border border-line bg-surface shadow-soft', 'p-6' => $padding]) }}>
    @isset($header)
        <div class="mb-4 flex items-center justify-between gap-4 border-b border-line pb-4">{{ $header }}</div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div class="mt-4 border-t border-line pt-4">{{ $footer }}</div>
    @endisset
</div>
