@props(['name', 'label' => null, 'options' => [], 'placeholder' => null, 'selected' => null, 'bag' => 'default'])

@php
    $id = $attributes->get('id', $name);
    $errorBag = $errors->getBag($bag);
    $hasError = $errorBag->has($name);
    $current = (string) old($name, $selected);
@endphp

<div {{ $attributes->only('class')->class('flex flex-col gap-1.5') }}>
    @if ($label)
        <label for="{{ $id }}" class="text-sm font-medium text-ink">{{ $label }}</label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $id }}"
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->except(['id', 'class'])->class([
            'w-full rounded-xl border bg-surface px-4 py-2.5 text-sm text-ink transition focus:outline-none focus:ring-2',
            'border-line focus:border-brand focus:ring-brand/25' => ! $hasError,
            'border-danger focus:ring-danger/25' => $hasError,
        ]) }}
    >
        @if ($placeholder)
            <option value="" disabled @selected($current === '')>{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $optionLabel)
            <option value="{{ $value }}" @selected($current === (string) $value)>{{ $optionLabel }}</option>
        @endforeach
        {{ $slot }}
    </select>

    @if ($hasError)
        <p id="{{ $id }}-error" class="text-xs text-danger">{{ $errorBag->first($name) }}</p>
    @endif
</div>
