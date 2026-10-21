@props(['name', 'title', 'maxWidth' => 'max-w-lg'])

{{--
    Se abre con $dispatch('open-modal', { name: '...', data: { ... } }); el contenido lee "data".
    Se cierra con Escape, el botón ×, el fondo o $dispatch('close-modal', { name: '...' }).
--}}
<div
    x-data="{ open: false, data: {} }"
    x-on:open-modal.window="if ($event.detail.name === @js($name)) { data = $event.detail.data ?? {}; open = true }"
    x-on:close-modal.window="if ($event.detail.name === @js($name)) open = false"
    x-on:keydown.escape.window="open = false"
    x-effect="document.body.classList.toggle('overflow-hidden', open)"
    x-cloak
    x-show="open"
    class="fixed inset-0 z-[2200] flex items-end justify-center p-4 sm:items-center"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-{{ $name }}-title"
>
    <div x-show="open" x-transition.opacity x-on:click="open = false" class="absolute inset-0 bg-black/50"></div>

    <div x-show="open" x-transition x-trap.noscroll="open" @class(['relative max-h-[90vh] w-full overflow-y-auto rounded-card bg-surface p-6 text-ink shadow-2xl', $maxWidth])>
        <header class="mb-5 flex items-start justify-between gap-4">
            <h2 id="modal-{{ $name }}-title" class="text-xl font-semibold">{{ $title }}</h2>
            <button type="button" x-on:click="open = false" class="-m-1 rounded-full p-1.5 text-muted transition hover:bg-brand-soft hover:text-brand" aria-label="Cerrar">
                <svg class="size-5 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </button>
        </header>

        {{ $slot }}
    </div>
</div>
