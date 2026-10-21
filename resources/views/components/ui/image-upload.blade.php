@props(['name', 'label' => null, 'hint' => 'JPG, PNG o WebP', 'current' => null])

@php
    $id = $attributes->get('id', $name);
@endphp

{{-- Selector de imagen con vista previa; también acepta arrastrar y soltar --}}
<div
    {{ $attributes->only('class')->class('flex flex-col gap-1.5') }}
    x-data="{
        preview: @js($current),
        dragging: false,
        show(file) {
            if (! file || ! file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = (event) => this.preview = event.target.result;
            reader.readAsDataURL(file);
        },
        drop(event) {
            this.dragging = false;
            if (! event.dataTransfer.files.length) return;
            this.$refs.input.files = event.dataTransfer.files;
            this.show(this.$refs.input.files[0]);
        },
        clear() {
            this.$refs.input.value = '';
            this.preview = null;
        },
    }"
>
    @if ($label)
        <label for="{{ $id }}" class="text-sm font-medium text-ink">{{ $label }}</label>
    @endif

    <div
        x-on:click="$refs.input.click()"
        x-on:dragover.prevent="dragging = true"
        x-on:dragleave.prevent="dragging = false"
        x-on:drop.prevent="drop($event)"
        x-bind:class="dragging ? 'border-brand bg-brand-soft' : 'border-line'"
        class="relative flex min-h-36 cursor-pointer flex-col items-center justify-center gap-2 overflow-hidden rounded-xl border-2 border-dashed p-4 text-center text-sm text-muted transition hover:border-brand"
    >
        <input
            type="file"
            name="{{ $name }}"
            id="{{ $id }}"
            accept="image/*"
            class="sr-only"
            x-ref="input"
            x-on:change="show($event.target.files[0])"
            {{ $attributes->except(['class', 'id']) }}
        >

        <template x-if="preview">
            <div class="relative">
                <img x-bind:src="preview" alt="Vista previa" class="max-h-48 rounded-lg object-contain">
                <button type="button" x-on:click.stop="clear()" class="absolute -top-2 -right-2 flex size-7 items-center justify-center rounded-full bg-danger text-white shadow" aria-label="Quitar imagen">×</button>
            </div>
        </template>

        <template x-if="! preview">
            <div class="flex flex-col items-center gap-2">
                <svg class="size-8 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                <span>Haz clic o arrastra una imagen</span>
                @if ($hint)
                    <span class="text-xs">{{ $hint }}</span>
                @endif
            </div>
        </template>
    </div>

    @error($name)
        <p class="text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
