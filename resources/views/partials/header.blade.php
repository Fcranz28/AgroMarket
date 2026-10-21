@php
    $navLinks = [
        ['label' => 'Inicio', 'route' => 'home', 'active' => 'home'],
        ['label' => 'Categorías', 'route' => 'products.index', 'active' => 'products.*'],
        ['label' => 'Contacto', 'route' => 'contact.show', 'active' => 'contact.*'],
        ['label' => 'Nosotros', 'route' => 'about', 'active' => 'about'],
    ];
@endphp

<header class="pointer-events-none fixed inset-x-0 top-2.5 z-[1000] flex flex-col items-center md:top-5">
    <nav class="pointer-events-auto flex w-[95%] max-w-[1600px] items-center justify-between gap-2 rounded-2xl border border-line bg-navbar px-2 py-2 shadow-card backdrop-blur-md sm:px-4 md:w-[90%] md:gap-4 md:rounded-pill md:px-8 md:py-3" aria-label="Principal">
        <a href="{{ route('home') }}" class="shrink-0">
            <img src="{{ asset('img/Logo_Claro.png') }}" alt="AgroMarket" class="h-7 w-auto md:h-10 dark:hidden">
            <img src="{{ asset('img/Logo_Oscuro.png') }}" alt="AgroMarket" class="hidden h-7 w-auto md:h-10 dark:block">
        </a>

        <div class="hidden gap-8 xl:flex">
            @foreach ($navLinks as $link)
                <a
                    href="{{ route($link['route']) }}"
                    @class([
                        'relative text-[0.95rem] font-medium transition hover:text-brand',
                        'text-brand after:absolute after:inset-x-0 after:-bottom-1 after:h-0.5 after:rounded-full after:bg-brand' => request()->routeIs($link['active']),
                        'text-ink' => ! request()->routeIs($link['active']),
                    ])
                >{{ $link['label'] }}</a>
            @endforeach
        </div>

        <div class="flex min-w-0 flex-1 items-center justify-end gap-1 sm:gap-3 xl:flex-none">
            <div class="relative min-w-0 flex-1 md:max-w-md xl:w-96 xl:flex-none">
                <form action="{{ route('search') }}" method="GET" id="searchForm" role="search" class="flex items-center rounded-pill border border-line bg-canvas py-1 pr-1 pl-1 transition focus-within:border-brand">
                    <label for="searchCategory" class="sr-only">Categoría</label>
                    <select name="category" id="searchCategory" class="hidden max-w-24 cursor-pointer truncate border-r border-line bg-transparent px-2 py-1.5 text-xs text-ink outline-none min-[400px]:block sm:max-w-40 sm:text-sm">
                        <option value="all">Todas las Categorías</option>
                        @foreach ($navCategories as $category)
                            <option value="{{ $category->slug }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <label for="searchInput" class="sr-only">Buscar productos</label>
                    <input type="text" name="q" id="searchInput" placeholder="Buscar productos..." autocomplete="off" class="min-w-0 flex-1 bg-transparent px-3 py-1.5 text-base text-ink outline-none placeholder:text-muted sm:text-sm">
                    <button type="submit" class="flex size-8 shrink-0 items-center justify-center rounded-full bg-brand text-white transition hover:bg-brand-strong" aria-label="Buscar">
                        <svg class="size-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                    </button>
                </form>

                {{-- autocomplete.js rellena .suggestions-list y alterna display --}}
                <div id="searchSuggestions" class="absolute inset-x-0 top-[calc(100%+6px)] z-[1000] max-h-96 overflow-y-auto rounded-xl border border-line bg-surface shadow-lifted" style="display: none;">
                    <div class="suggestions-list py-2"></div>
                </div>
            </div>

            {{-- carrito.js usa #openCart y .cart-count --}}
            <button type="button" id="openCart" class="relative hidden p-1 text-ink transition hover:text-brand md:inline-flex" aria-label="Abrir carrito">
                <svg class="size-6 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                <span class="cart-count absolute -top-1 -right-1 flex size-[18px] items-center justify-center rounded-full bg-brand text-[0.7rem] font-bold text-white">0</span>
            </button>

            @guest
                <div class="hidden shrink-0 gap-3 lg:flex">
                    <x-ui.button variant="secondary" :href="route('login')" class="whitespace-nowrap">Iniciar sesión</x-ui.button>
                    <x-ui.button variant="dark" :href="route('register')">Registrarse</x-ui.button>
                </div>
            @else
                <x-ui.dropdown class="hidden md:block">
                    <x-slot:trigger>
                        <button type="button" class="flex items-center gap-2 rounded-pill border border-line bg-canvas px-3 py-2 text-sm whitespace-nowrap text-ink transition hover:border-brand">
                            Hola, {{ Auth::user()->name }}
                            <svg class="size-3 fill-current" viewBox="0 0 16 16" aria-hidden="true"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/></svg>
                        </button>
                    </x-slot:trigger>

                    @include('partials.user-menu-links')
                </x-ui.dropdown>
            @endguest

            <button
                type="button"
                x-data="{ dark: document.documentElement.dataset.theme === 'dark' }"
                x-on:click="dark = ! dark; document.documentElement.dataset.theme = dark ? 'dark' : 'light'; localStorage.setItem('theme', dark ? 'dark' : 'light')"
                class="flex shrink-0 items-center rounded-full p-1.5 text-ink transition hover:text-brand"
                aria-label="Cambiar tema"
            >
                <svg x-show="! dark" class="size-6 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9c1.65 0 3 1.35 3 3s-1.35 3-3 3-3-1.35-3-3 1.35-3 3-3m0-2c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58a.996.996 0 0 0-1.41 0 .996.996 0 0 0 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37a.996.996 0 0 0-1.41 0 .996.996 0 0 0 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0a.996.996 0 0 0 0-1.41l-1.06-1.06zm1.06-10.96a.996.996 0 0 0 0-1.41.996.996 0 0 0-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36a.996.996 0 0 0 0-1.41.996.996 0 0 0-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z"/></svg>
                <svg x-show="dark" x-cloak class="size-6 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a9 9 0 1 0 9 9c0-.46-.04-.92-.1-1.36a5.389 5.389 0 0 1-4.4 2.26 5.403 5.403 0 0 1-3.14-9.8c-.44-.06-.9-.1-1.36-.1z"/></svg>
            </button>
        </div>
    </nav>

    {{-- Pestañas que cuelgan del navbar cuando los enlaces no caben en él --}}
    <nav class="pointer-events-auto -mt-px flex max-w-[95%] overflow-x-auto rounded-b-2xl border border-t-0 border-line bg-navbar px-1 shadow-soft backdrop-blur-md [scrollbar-width:none] xl:hidden" aria-label="Secciones">
        @foreach ($navLinks as $link)
            <a
                href="{{ route($link['route']) }}"
                @class([
                    'my-1 shrink-0 rounded-lg px-4 py-2 text-sm font-medium whitespace-nowrap transition hover:bg-brand/10 hover:text-brand',
                    'bg-brand/10 font-semibold text-brand' => request()->routeIs($link['active']),
                    'text-muted' => ! request()->routeIs($link['active']),
                ])
            >{{ $link['label'] }}</a>
        @endforeach
    </nav>
</header>
