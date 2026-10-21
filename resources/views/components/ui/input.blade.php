@props(['name', 'label' => null, 'type' => 'text', 'hint' => null])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
@endphp

<div class="flex flex-col gap-1.5">
    @if ($label)
        <label for="{{ $id }}" class="text-sm font-medium text-ink">{{ $label }}</label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        @if ($type !== 'password') value="{{ old($name, $attributes->get('value')) }}" @endif
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->except(['id', 'value'])->class([
            'w-full rounded-xl border bg-surface px-4 py-2.5 text-sm text-ink placeholder:text-muted/70 transition focus:outline-none focus:ring-2',
            'border-line focus:border-brand focus:ring-brand/25' => ! $hasError,
            'border-danger focus:ring-danger/25' => $hasError,
        ]) }}
    >

    @if ($hasError)
        <p id="{{ $id }}-error" class="text-xs text-danger">{{ $errors->first($name) }}</p>
    @elseif ($hint)
        <p class="text-xs text-muted">{{ $hint }}</p>
    @endif
</div>
