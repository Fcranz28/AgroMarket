@php
    $itemClasses = 'relative flex flex-1 flex-col items-center justify-center gap-1 text-[0.7rem] font-semibold transition';
    $inactive = 'text-on-night-muted hover:text-on-night';
@endphp

<nav class="fixed inset-x-3 bottom-3 z-[2000] flex h-[68px] items-stretch rounded-[20px] bg-navbar pb-[env(safe-area-inset-bottom)] shadow-lifted md:hidden" aria-label="Navegación móvil">
    <a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif @class([$itemClasses, 'text-lime' => request()->routeIs('home'), $inactive => ! request()->routeIs('home')])>
        <svg class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11l9-7 9 7v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></svg>
        <span>Inicio</span>
    </a>

    <a href="{{ route('products.index') }}" @if (request()->routeIs('products.*')) aria-current="page" @endif @class([$itemClasses, 'text-lime' => request()->routeIs('products.*'), $inactive => ! request()->routeIs('products.*')])>
        <svg class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>
        <span>Catálogo</span>
    </a>

    <button type="button" data-test="open-cart-mobile" x-data x-on:click="$dispatch('open-cart')" class="{{ $itemClasses }} {{ $inactive }}">
        <svg class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6.2"/><circle cx="10" cy="20" r="1.2"/><circle cx="17" cy="20" r="1.2"/></svg>
        <span>Carrito</span>
        <livewire:orders.cart-count variant="mobile" />
    </button>

    <x-ui.dropdown class="flex flex-1" width="w-52">
        <x-slot:trigger>
            <button type="button" data-test="mobile-user-menu" class="{{ $itemClasses }} {{ $inactive }} h-full w-full" x-bind:class="open && '!text-lime'">
                <svg class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c1-4 4.5-6 8-6s7 2 8 6"/></svg>
                <span>Mi cuenta</span>
            </button>
        </x-slot:trigger>

        {{-- El menú se abre hacia arriba, sobre la barra --}}
        <x-slot:position>bottom-full right-0 mb-3</x-slot:position>

        @include('partials.user-menu-links')
    </x-ui.dropdown>
</nav>
