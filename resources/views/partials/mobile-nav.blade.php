@php
    $itemClasses = 'flex flex-1 flex-col items-center justify-center gap-1 text-[0.7rem] transition hover:text-brand';
@endphp

<nav class="fixed inset-x-0 bottom-0 z-[2000] flex h-[70px] items-center justify-around border-t border-line bg-surface pb-[env(safe-area-inset-bottom)] shadow-[0_-4px_20px_rgb(0_0_0/0.1)] md:hidden" aria-label="Navegación móvil">
    <a href="{{ route('home') }}" @class([$itemClasses, 'text-brand' => request()->routeIs('home'), 'text-muted' => ! request()->routeIs('home')])>
        <svg class="size-[22px] fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        <span>Inicio</span>
    </a>

    <x-ui.dropdown class="flex flex-1 justify-center" width="w-52">
        <x-slot:trigger>
            <button type="button" data-test="mobile-user-menu" class="{{ $itemClasses }} text-muted" x-bind:class="open && 'text-brand'">
                <svg class="size-[22px] fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                <span class="flex items-center gap-0.5">
                    Yo
                    <svg class="size-2.5 transition" x-bind:class="open && 'rotate-180'" viewBox="0 0 12 12" aria-hidden="true"><path d="m2 4 4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                </span>
            </button>
        </x-slot:trigger>

        {{-- El menú se abre hacia arriba, sobre la barra --}}
        <x-slot:position>bottom-full left-1/2 mb-3 -translate-x-1/2</x-slot:position>

        @include('partials.user-menu-links')
    </x-ui.dropdown>

    <button type="button" data-test="open-cart-mobile" x-data x-on:click="$dispatch('open-cart')" class="relative {{ $itemClasses }} text-muted">
        <svg class="size-[22px] fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
        <span>Carrito</span>
        <livewire:orders.cart-count variant="mobile" />
    </button>
</nav>
