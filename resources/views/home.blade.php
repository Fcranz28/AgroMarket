@extends('layouts.app')

@php
    use App\Modules\Shared\Money;

    $spotlight = $featuredProducts->first();

    // Cinta animada: los productos recientes con su categoría
    $ticker = $featuredProducts->merge($suggestedProducts)->take(10);

    $faqs = [
        ['¿Necesito una cuenta para comprar?', 'No. Puedes comprar como invitado y te enviamos un enlace para descargar tu comprobante. Con una cuenta ves todos tus pedidos y guardas tus direcciones.'],
        ['¿Cómo sé que el agricultor es real?', 'Antes de publicar, cada agricultor sube su DNI y una foto de su rostro, y nuestro equipo valida su identidad. Solo los aprobados pueden vender.'],
        ['¿Qué pasa si un producto se agota mientras pago?', 'El pedido no se crea y te reembolsamos el pago automáticamente a tu tarjeta.'],
        ['¿Los precios incluyen IGV?', 'Sí. El precio que ves es el que pagas; en tu comprobante se detallan la base imponible y el IGV.'],
        ['¿Puedo comprar por mayor?', 'Sí. Muchos productos se venden por saco, caja o arroba, cada unidad con su propio precio y stock.'],
    ];

    $sectionTitle = 'text-3xl font-extrabold tracking-[-0.03em] text-ink sm:text-4xl';
@endphp

@section('fullbleed')
    <section class="bg-navbar bg-[radial-gradient(rgb(167_219_76/0.13)_1px,transparent_1px)] bg-[size:22px_22px] text-on-night" aria-labelledby="hero-title">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 pt-10 pb-16 sm:px-6 lg:grid-cols-2 lg:pt-14 lg:pb-20">
            <div class="flex flex-col gap-6">
                <a href="{{ route('products.index') }}" class="flex w-fit items-center gap-2.5 rounded-pill border border-lime/35 bg-lime/10 py-1.5 pr-4 pl-1.5 text-sm text-on-night transition hover:border-lime hover:text-on-night">
                    <span class="rounded-pill bg-lime px-2.5 py-1 text-xs font-extrabold text-night">Nuevo</span>
                    Compra sin cuenta y recibe tu comprobante
                    <svg class="size-3.5 text-lime" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                </a>

                <h1 id="hero-title" class="text-[clamp(2.75rem,6.2vw,5rem)] leading-[0.98] font-extrabold tracking-[-0.04em]">Del campo peruano,<br><span class="text-lime">directo a tu mesa.</span></h1>

                <p class="max-w-lg text-lg leading-relaxed text-on-night-muted">Compra a agricultores verificados, al precio que ellos fijan, por kilo, saco o arroba. Sin intermediarios.</p>

                <div class="flex flex-wrap gap-3">
                    <x-ui.button size="lg" :href="route('products.index')" class="ring-4 ring-lime/20">
                        Ver productos
                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </x-ui.button>
                    <a href="#vende" class="inline-flex items-center rounded-control border-[1.5px] border-white/35 px-6 py-3.5 font-bold transition hover:border-lime hover:text-lime">Soy agricultor</a>
                </div>
            </div>

            <div class="relative mx-auto h-[380px] w-full max-w-[560px] sm:h-[460px]" aria-hidden="true">
                <div class="absolute inset-0 bg-[radial-gradient(closest-side,rgb(167_219_76/0.28),transparent_70%)]"></div>
                <div class="absolute inset-[8%_12%] rounded-full border border-dashed border-lime/35"></div>
                <div class="absolute inset-[18%_22%] rounded-full bg-night-soft"></div>
                <x-catalog.illustration kind="palta" class="absolute top-[12%] left-[26%] w-[48%]" />
                <x-catalog.illustration kind="papa" class="absolute top-[48%] left-[2%] w-[34%]" />
                <x-catalog.illustration kind="mango" class="absolute top-[4%] right-[0%] w-[32%]" />
                <x-catalog.illustration kind="aji" class="absolute right-[8%] bottom-0 w-[30%]" />

                @if ($spotlight)
                    <div class="absolute top-[2%] left-0 flex flex-col gap-1 rounded-2xl bg-surface px-4 py-3 text-ink shadow-lifted">
                        <span class="text-xs font-semibold text-muted">{{ $spotlight->name }}@if ($spotlight->category) · {{ $spotlight->category->name }}@endif</span>
                        <span class="text-[22px] font-extrabold tracking-tight">{{ Money::fromDecimal($spotlight->price)->format() }} <span class="text-sm font-medium text-muted">/ {{ $spotlight->unit }}</span></span>
                        <span class="flex items-center gap-1.5 text-xs font-semibold text-brand">
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5 9-10"/></svg>
                            Agricultor verificado
                        </span>
                    </div>
                @endif
            </div>
        </div>

        @if ($ticker->isNotEmpty())
            <div class="overflow-hidden border-t border-lime-strong bg-lime text-night">
                <div class="flex w-max gap-10 py-4 text-lg font-extrabold whitespace-nowrap motion-safe:animate-marquee" aria-hidden="true">
                    @foreach ([1, 2] as $copy)
                        @foreach ($ticker as $item)
                            <span class="flex items-center gap-10">
                                {{ $item->name }}
                                @if ($item->category)
                                    <span class="font-medium text-night/70">{{ $item->category->name }}</span>
                                @endif
                                <svg class="size-4 fill-current" viewBox="0 0 24 24"><path d="M12 2l2.5 7.5L22 12l-7.5 2.5L12 22l-2.5-7.5L2 12l7.5-2.5z"/></svg>
                            </span>
                        @endforeach
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection

@section('content')
    <div class="flex flex-col gap-20 py-10">
        @if ($categories->isNotEmpty())
            <section aria-labelledby="cat-title">
                <div class="mb-6 flex flex-wrap items-baseline justify-between gap-3">
                    <h2 id="cat-title" class="{{ $sectionTitle }}">Compra por categoría</h2>
                    <a href="{{ route('products.index') }}" class="font-bold text-brand hover:underline">Ver todas →</a>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ($categories as $category)
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group flex flex-col gap-3 rounded-card border border-line bg-surface p-3 shadow-soft transition hover:-translate-y-1 hover:shadow-card">
                            <span class="block aspect-[4/3] rounded-xl bg-brand-soft p-3">
                                <x-catalog.illustration :category="$category->slug" class="size-full transition duration-300 group-hover:scale-105" />
                            </span>
                            <span class="flex items-center justify-between gap-2 px-1">
                                <span class="font-bold text-ink">{{ $category->name }}</span>
                                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-canvas text-ink transition group-hover:bg-night group-hover:text-lime">
                                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8"/></svg>
                                </span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($featuredProducts->isNotEmpty())
            <section aria-labelledby="featured-title">
                <div class="mb-6 flex flex-wrap items-baseline justify-between gap-3">
                    <h2 id="featured-title" class="{{ $sectionTitle }}">Recién cosechado</h2>
                    <a href="{{ route('products.index') }}" class="font-bold text-brand hover:underline">Ver todos los productos →</a>
                </div>
                <div class="grid grid-cols-1 gap-4 min-[480px]:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($featuredProducts as $product)
                        <x-catalog.product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

        <section aria-labelledby="bento-title">
            <p class="mb-2 text-xs font-bold tracking-[0.14em] text-brand uppercase">Por qué AgroMarket</p>
            <h2 id="bento-title" class="{{ $sectionTitle }} mb-7 max-w-2xl leading-[1.05]">Todo lo que pasa entre la chacra y tu mesa, a la vista.</h2>

            <div class="grid gap-4 lg:grid-cols-3">
                <article class="flex flex-wrap items-center gap-7 rounded-3xl bg-navbar p-7 text-on-night lg:col-span-2">
                    <div class="flex min-w-56 flex-1 flex-col gap-2.5">
                        <h3 class="text-2xl font-extrabold tracking-tight">Cada agricultor, verificado</h3>
                        <p class="leading-relaxed text-on-night-muted">Antes de vender, sube su DNI y una selfie. Nuestro equipo valida su identidad con RENIEC.</p>
                    </div>
                    <div class="flex min-w-64 flex-1 flex-col gap-3 rounded-2xl bg-surface p-5 text-ink shadow-lifted" aria-hidden="true">
                        <div class="flex items-center gap-3">
                            <span class="flex size-11 items-center justify-center rounded-full bg-night font-extrabold text-lime">RM</span>
                            <span class="flex-1"><span class="block font-bold">Rosa Mamani</span><span class="text-sm text-muted">Agricultora</span></span>
                            <x-ui.badge color="success">Aprobada</x-ui.badge>
                        </div>
                        @foreach (['DNI frontal y reverso' => 'Revisado', 'Foto de rostro' => 'Revisada', 'Consulta RENIEC' => 'Coincide'] as $check => $result)
                            <div @class(['flex justify-between text-sm', 'border-t border-line pt-3' => $loop->first])>
                                <span class="text-muted">{{ $check }}</span>
                                <span class="font-bold text-brand">✓ {{ $result }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="flex flex-col gap-5 rounded-3xl border border-line bg-surface p-7">
                    <div>
                        <h3 class="mb-1.5 text-xl font-extrabold tracking-tight">Por kilo, saco o arroba</h3>
                        <p class="leading-relaxed text-muted">Cada unidad con su propio precio y stock.</p>
                    </div>
                    <div class="mt-auto flex flex-col gap-2" aria-hidden="true">
                        <span class="flex justify-between rounded-xl border-2 border-ink px-3.5 py-3 font-bold">Kilo <span>S/. 12.00</span></span>
                        <span class="flex justify-between rounded-xl border border-line px-3.5 py-3 font-semibold text-muted">Arroba <span>S/. 140.00</span></span>
                    </div>
                </article>

                <article class="flex flex-col gap-5 rounded-3xl bg-lime p-7 text-night">
                    <div>
                        <h3 class="mb-1.5 text-xl font-extrabold tracking-tight">Pago seguro</h3>
                        <p class="leading-relaxed text-night/80">Con tarjeta. Si un producto se agota al pagar, te devolvemos el dinero.</p>
                    </div>
                    <div class="mt-auto flex flex-col gap-4 rounded-2xl bg-night p-4 text-on-night" aria-hidden="true">
                        <div class="flex items-center justify-between"><span class="h-6 w-9 rounded-md bg-lime"></span><span class="text-xs text-on-night-muted">Débito o crédito</span></div>
                        <span class="font-mono text-[17px] tracking-[0.12em]">•••• •••• •••• 4242</span>
                    </div>
                </article>

                <article class="flex flex-col gap-5 rounded-3xl border border-line bg-surface p-7">
                    <div>
                        <h3 class="mb-1.5 text-xl font-extrabold tracking-tight">Comprobante electrónico</h3>
                        <p class="leading-relaxed text-muted">El IGV ya está en el precio. Lo verás detallado.</p>
                    </div>
                    <dl class="mt-auto flex flex-col gap-2 rounded-2xl bg-canvas p-4 text-sm" aria-hidden="true">
                        <div class="flex justify-between"><dt class="text-muted">Op. gravada</dt><dd>S/. 7.63</dd></div>
                        <div class="flex justify-between"><dt class="text-muted">IGV (18 %)</dt><dd>S/. 1.37</dd></div>
                        <div class="flex justify-between border-t border-dashed border-line pt-2 font-extrabold"><dt>Total</dt><dd>S/. 9.00</dd></div>
                    </dl>
                </article>

                <article class="flex flex-col gap-6 rounded-3xl border border-line bg-surface p-7">
                    <div>
                        <h3 class="mb-1.5 text-xl font-extrabold tracking-tight">Sigue tu pedido</h3>
                        <p class="leading-relaxed text-muted">El agricultor actualiza el estado y tú lo ves en tu cuenta.</p>
                    </div>
                    <ol class="mt-auto grid grid-cols-4 gap-2" aria-hidden="true">
                        @foreach (['Pendiente' => 'done', 'En proceso' => 'done', 'Enviado' => 'current', 'Entregado' => 'next'] as $step => $state)
                            <li class="flex flex-col gap-2.5">
                                <span @class(['h-1.5 rounded-full', 'bg-ink' => $state === 'done', 'bg-lime-strong' => $state === 'current', 'bg-line' => $state === 'next'])></span>
                                <span @class(['text-xs font-bold', 'text-brand' => $state === 'current', 'text-muted' => $state === 'next'])>{{ $step }}</span>
                            </li>
                        @endforeach
                    </ol>
                </article>
            </div>
        </section>

        @if ($suggestedProducts->isNotEmpty())
            <section aria-labelledby="suggested-title">
                <h2 id="suggested-title" class="{{ $sectionTitle }} mb-6">Te puede interesar</h2>
                <div class="grid grid-cols-1 gap-4 min-[480px]:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($suggestedProducts as $product)
                        <x-catalog.product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

        <section class="grid items-start gap-6 lg:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
            <div id="preguntas" class="scroll-mt-28" x-data="{ open: 0 }">
                <h2 class="{{ $sectionTitle }} mb-5">Preguntas frecuentes</h2>
                <div class="flex flex-col gap-2.5">
                    @foreach ($faqs as $index => [$question, $answer])
                        <div class="rounded-2xl border bg-surface transition" x-bind:class="open === {{ $index }} ? 'border-ink' : 'border-line'">
                            <h3>
                                <button
                                    type="button"
                                    id="faq-{{ $index }}"
                                    x-on:click="open = open === {{ $index }} ? null : {{ $index }}"
                                    x-bind:aria-expanded="(open === {{ $index }}).toString()"
                                    aria-controls="faq-{{ $index }}-answer"
                                    class="flex min-h-15 w-full items-center justify-between gap-4 px-5 py-4 text-left font-bold text-ink"
                                >
                                    {{ $question }}
                                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full transition" x-bind:class="open === {{ $index }} ? 'bg-night text-lime rotate-45' : 'bg-canvas text-ink'">
                                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                                    </span>
                                </button>
                            </h3>
                            <div id="faq-{{ $index }}-answer" role="region" aria-labelledby="faq-{{ $index }}" x-cloak x-show="open === {{ $index }}" x-collapse>
                                <p class="px-5 pb-5 leading-relaxed text-muted">{{ $answer }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <aside id="vende" class="flex scroll-mt-28 flex-col gap-4 rounded-3xl bg-navbar bg-[radial-gradient(rgb(167_219_76/0.13)_1px,transparent_1px)] bg-[size:22px_22px] p-8 text-on-night" aria-labelledby="vende-title">
                <span class="flex size-14 items-center justify-center rounded-2xl bg-lime text-night">
                    <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V10l7-5 7 5v11"/><path d="M9 21v-6h6v6"/></svg>
                </span>
                <h2 id="vende-title" class="text-3xl leading-[1.05] font-extrabold tracking-[-0.03em]">¿Cultivas?<br><span class="text-lime">Vende en AgroMarket.</span></h2>
                <ol class="list-decimal space-y-1.5 pl-5 text-on-night-muted">
                    <li>Crea tu cuenta con tu correo o Google.</li>
                    <li>Sube tu DNI y una selfie.</li>
                    <li>Publica tus cosechas con su stock y precio.</li>
                </ol>
                <x-ui.button size="lg" :href="route('register')" class="w-fit">
                    Empezar a vender
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </x-ui.button>
            </aside>
        </section>
    </div>
@endsection
