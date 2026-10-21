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
            <livewire:catalog.search-box />

            <button type="button" x-data x-on:click="$dispatch('open-cart')" class="relative hidden p-1 text-ink transition hover:text-brand md:inline-flex" aria-label="Abrir carrito">
                <svg class="size-6 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                <livewire:orders.cart-count />
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

            @include('partials.theme-toggle')
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
