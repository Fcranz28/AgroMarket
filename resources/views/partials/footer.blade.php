@php
    $columns = [
        'Tienda' => [
            ['Catálogo', route('products.index')],
            ['Mis pedidos', route('orders.index')],
            ['Mis direcciones', route('orders.index', ['tab' => 'addresses'])],
        ],
        'Agricultores' => auth()->user()?->isFarmer()
            ? [['Panel del agricultor', route('farmer.dashboard')], ['Mis productos', route('dashboard.productos.index')]]
            : [['Vender en AgroMarket', route('register')], ['Cómo funciona', route('home').'#vende']],
        'AgroMarket' => [
            ['Nosotros', route('about')],
            ['Preguntas frecuentes', route('home').'#preguntas'],
            ['Contacto', route('contact.show')],
        ],
    ];
@endphp

<footer class="relative z-10 mt-auto w-full overflow-hidden bg-navbar pb-24 text-on-night-muted md:pb-0">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 pt-16 sm:px-6 md:grid-cols-[minmax(0,1.4fr)_repeat(3,minmax(0,1fr))]">
        <div class="flex flex-col gap-4">
            <span class="flex items-center gap-2.5 text-on-night">
                <span class="flex size-9 items-center justify-center rounded-[10px] bg-lime">
                    <svg class="size-5 text-night" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 19c0-8 5-13 14-14-1 9-6 14-14 14z"/><path d="M5 19l7-7"/></svg>
                </span>
                <span class="text-xl font-extrabold tracking-tight">AgroMarket</span>
            </span>
            <p class="max-w-xs leading-relaxed">Productos del campo peruano, directo de quien los cultiva.</p>
        </div>

        @foreach ($columns as $title => $links)
            <nav class="flex flex-col gap-3 text-sm" aria-label="{{ $title }}">
                <h2 class="font-bold text-on-night">{{ $title }}</h2>
                @foreach ($links as [$label, $url])
                    <a href="{{ $url }}" class="w-fit transition hover:text-lime">{{ $label }}</a>
                @endforeach
            </nav>
        @endforeach
    </div>

    <p aria-hidden="true" class="mx-auto mt-12 max-w-7xl px-4 text-[clamp(4.5rem,15.5vw,14rem)] leading-[0.8] font-extrabold tracking-[-0.06em] whitespace-nowrap text-night-soft select-none sm:px-6 dark:text-white/5">AgroMarket</p>

    <div class="mx-auto flex max-w-7xl flex-wrap justify-between gap-3 border-t border-white/10 px-4 py-6 text-sm sm:px-6">
        <span>&copy; {{ date('Y') }} AgroMarket. Todos los derechos reservados.</span>
        <span>Hecho en el Perú</span>
    </div>
</footer>

@auth
    @if (auth()->user()->isAdmin() || (auth()->user()->isFarmer() && auth()->user()->isVerified()))
        <a href="{{ route('dashboard.productos.create') }}" title="Agregar nuevo producto" aria-label="Agregar nuevo producto" class="fixed right-5 bottom-24 z-[1000] flex size-14 items-center justify-center rounded-2xl bg-lime text-night shadow-lifted transition hover:-translate-y-1 hover:bg-lime-strong md:bottom-6">
            <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        </a>
    @endif
@endauth
