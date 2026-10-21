@php
    $navLinks = [
        ['label' => 'Inicio', 'route' => 'home', 'active' => 'home'],
        ['label' => 'Catálogo', 'route' => 'products.index', 'active' => 'products.*'],
        ['label' => 'Nosotros', 'route' => 'about', 'active' => 'about'],
        ['label' => 'Contacto', 'route' => 'contact.show', 'active' => 'contact.*'],
    ];
@endphp

<header class="sticky top-0 z-[1000] bg-navbar text-on-night">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-x-6 gap-y-3 px-4 py-3 sm:px-6 lg:py-4">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5 hover:text-on-night" aria-label="AgroMarket, inicio">
            <span class="flex size-9 items-center justify-center rounded-[10px] bg-lime">
                <svg class="size-5 text-night" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 19c0-8 5-13 14-14-1 9-6 14-14 14z"/><path d="M5 19l7-7"/></svg>
            </span>
            <span class="text-xl font-extrabold tracking-tight">AgroMarket</span>
        </a>

        <nav class="order-last -mx-4 flex w-[calc(100%+2rem)] gap-1 overflow-x-auto px-4 [scrollbar-width:none] xl:order-none xl:mx-0 xl:w-auto xl:shrink-0 xl:px-0" aria-label="Principal">
            @foreach ($navLinks as $link)
                <a
                    href="{{ route($link['route']) }}"
                    @if (request()->routeIs($link['active'])) aria-current="page" @endif
                    @class([
                        'shrink-0 rounded-pill px-4 py-2 text-sm font-semibold whitespace-nowrap transition',
                        'bg-lime text-night' => request()->routeIs($link['active']),
                        'text-on-night-muted hover:bg-white/10 hover:text-on-night' => ! request()->routeIs($link['active']),
                    ])
                >{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="ml-auto flex min-w-0 flex-1 items-center justify-end gap-2 xl:ml-0">
            <livewire:catalog.search-box />

            @guest
                <a href="{{ route('login') }}" class="hidden h-11 shrink-0 items-center gap-2 rounded-control border border-white/15 px-4 text-sm font-semibold transition hover:border-lime hover:text-lime lg:flex">
                    <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c1-4 4.5-6 8-6s7 2 8 6"/></svg>
                    Ingresar
                </a>
                <a href="{{ route('register') }}" class="hidden h-11 shrink-0 items-center rounded-control bg-white/10 px-4 text-sm font-semibold transition hover:bg-white/15 xl:flex">Registrarse</a>
            @else
                <x-ui.dropdown class="hidden md:block">
                    <x-slot:trigger>
                        <button type="button" class="flex h-11 items-center gap-2 rounded-control border border-white/15 px-3 text-sm font-semibold whitespace-nowrap transition hover:border-lime">
                            <span class="flex size-7 items-center justify-center rounded-full bg-lime text-xs font-extrabold text-night">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                            <span class="max-w-32 truncate">Hola, {{ Auth::user()->name }}</span>
                            <svg class="size-3 fill-current" viewBox="0 0 16 16" aria-hidden="true"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/></svg>
                        </button>
                    </x-slot:trigger>

                    @include('partials.user-menu-links')
                </x-ui.dropdown>
            @endguest

            <span class="hidden sm:block">@include('partials.theme-toggle')</span>

            <button type="button" data-test="open-cart" x-data x-on:click="$dispatch('open-cart')" class="relative hidden size-11 shrink-0 items-center justify-center rounded-control bg-lime text-night transition hover:bg-lime-strong md:flex" aria-label="Abrir carrito">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6.2"/><circle cx="10" cy="20" r="1.2"/><circle cx="17" cy="20" r="1.2"/></svg>
                <livewire:orders.cart-count />
            </button>
        </div>
    </div>
</header>
