@php
    $columns = [
        'Acerca de' => [
            ['Nuestra Historia', route('about')],
            ['Misión y Visión', route('about').'#mission'],
            ['Equipo', route('about').'#team'],
            ['Trabaja con Nosotros', route('about').'#careers'],
        ],
        'Ayuda' => [
            ['Centro de Ayuda', route('contact.show')],
            ['Envíos', route('contact.show')],
            ['Devoluciones', route('contact.show')],
            ['Estado del Pedido', route('orders.index')],
            ['Formas de Pago', route('contact.show')],
        ],
        'Contacto' => [
            ['WhatsApp', route('contact.show')],
            ['Email', route('contact.show')],
            ['Teléfono', route('contact.show')],
            ['Sucursales', route('contact.show').'#locations'],
        ],
    ];

    $socialLinks = [
        'Facebook' => 'M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.989C18.343 21.129 22 16.99 22 12c0-5.523-4.477-10-10-10z',
        'Twitter' => 'M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z',
        'Instagram' => 'M12 2c2.717 0 3.056.01 4.122.06 1.065.05 1.79.217 2.428.465.66.254 1.216.598 1.772 1.153a4.908 4.908 0 0 1 1.153 1.772c.247.637.415 1.363.465 2.428.047 1.066.06 1.405.06 4.122 0 2.717-.01 3.056-.06 4.122-.05 1.065-.218 1.79-.465 2.428a4.883 4.883 0 0 1-1.153 1.772 4.915 4.915 0 0 1-1.772 1.153c-.637.247-1.363.415-2.428.465-1.066.047-1.405.06-4.122.06-2.717 0-3.056-.01-4.122-.06-1.065-.05-1.79-.218-2.428-.465a4.89 4.89 0 0 1-1.772-1.153 4.904 4.904 0 0 1-1.153-1.772c-.248-.637-.415-1.363-.465-2.428C2.013 15.056 2 14.717 2 12c0-2.717.01-3.056.06-4.122.05-1.066.217-1.79.465-2.428a4.88 4.88 0 0 1 1.153-1.772A4.897 4.897 0 0 1 5.45 2.525c.638-.248 1.362-.415 2.428-.465C8.944 2.013 9.283 2 12 2zm0 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm6.5-.25a1.25 1.25 0 0 0-2.5 0 1.25 1.25 0 0 0 2.5 0zM12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6z',
        'LinkedIn' => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
    ];
@endphp

<footer class="relative z-10 mt-auto w-full bg-linear-135 from-[#6fba2c] via-[#aed581] to-[#7cb342] px-5 pt-14 pb-24 text-center text-[#1f2e16] md:pb-6 md:text-left dark:from-[#5a8025] dark:via-[#95c356] dark:to-[#6b9230]">
    <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4 lg:gap-10">
        @foreach ($columns as $title => $links)
            <div>
                <h4 class="mb-5 text-lg font-semibold">{{ $title }}</h4>
                <ul class="flex flex-col gap-2.5">
                    @foreach ($links as [$label, $url])
                        <li><a href="{{ $url }}" class="opacity-80 transition hover:opacity-100 hover:underline">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        <div>
            <h4 class="mb-5 text-lg font-semibold">Síguenos</h4>
            <div class="flex justify-center gap-4 md:justify-start">
                @foreach ($socialLinks as $network => $path)
                    <a href="#" aria-label="{{ $network }}" class="opacity-80 transition hover:-translate-y-0.5 hover:opacity-100">
                        <svg class="size-6 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $path }}"/></svg>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mx-auto mt-10 max-w-6xl border-t border-black/10 pt-5 text-center text-sm opacity-80">
        &copy; {{ date('Y') }} {{ config('app.name', 'AgroMarket') }}. Todos los derechos reservados.
    </div>
</footer>

@auth
    @if (auth()->user()->isAdmin() || (auth()->user()->isFarmer() && auth()->user()->isVerified()))
        <a href="{{ route('dashboard.productos.create') }}" title="Agregar nuevo producto" aria-label="Agregar nuevo producto" class="fixed right-5 bottom-[90px] z-[1000] flex size-15 items-center justify-center rounded-full border-2 border-white/20 bg-linear-135 from-brand to-brand-strong text-white shadow-[0_4px_15px_rgb(122_165_55/0.4)] transition hover:-translate-y-1 hover:scale-110">
            <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        </a>
    @endif
@endauth
