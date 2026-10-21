@props(['name', 'label' => null, 'hint' => null, 'bag' => 'default'])

@php
    $id = $attributes->get('id', $name);
    $errorBag = $errors->getBag($bag);
    $hasError = $errorBag->has($name);
@endphp

<div {{ $attributes->only('class')->class('flex flex-col gap-1.5') }}>
    @if ($label)
        <label for="{{ $id }}" class="text-sm font-medium text-ink">{{ $label }}</label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $id }}"
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->except(['id', 'class', 'value'])->class([
            'w-full rounded-xl border bg-surface px-4 py-2.5 text-sm text-ink placeholder:text-muted/70 transition focus:outline-none focus:ring-2',
            'border-line focus:border-brand focus:ring-brand/25' => ! $hasError,
            'border-danger focus:ring-danger/25' => $hasError,
        ]) }}
    >{{ old($name, $attributes->get('value', (string) $slot)) }}</textarea>

    @if ($hasError)
        <p id="{{ $id }}-error" class="text-xs text-danger">{{ $errorBag->first($name) }}</p>
    @elseif ($hint)
        <p class="text-xs text-muted">{{ $hint }}</p>
    @endif
</div>
