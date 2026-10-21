@extends('layouts.app')

@php
    $heroStats = [['count' => 1500, 'label' => 'Agricultores'], ['count' => 25, 'label' => 'Comunidades'], ['count' => 10000, 'label' => 'Clientes']];

    $values = [
        ['icon' => 'fa-handshake', 'title' => 'Compromiso social', 'text' => 'Trabajamos por el desarrollo sostenible de las comunidades agrícolas.'],
        ['icon' => 'fa-seedling', 'title' => 'Sostenibilidad', 'text' => 'Promovemos prácticas agrícolas responsables con el medio ambiente.'],
        ['icon' => 'fa-lightbulb', 'title' => 'Innovación', 'text' => 'Integramos tecnología para mejorar la eficiencia y productividad.'],
        ['icon' => 'fa-balance-scale', 'title' => 'Comercio justo', 'text' => 'Garantizamos precios justos y condiciones equitativas.'],
    ];

    $impact = [
        ['count' => 1500, 'suffix' => '', 'label' => 'Agricultores beneficiados'],
        ['count' => 25, 'suffix' => '', 'label' => 'Comunidades alcanzadas'],
        ['count' => 40, 'suffix' => '%', 'label' => 'Incremento en ingresos'],
        ['count' => 10000, 'suffix' => '', 'label' => 'Clientes satisfechos'],
    ];

    $team = [
        ['name' => 'María Rodríguez', 'role' => 'Directora Ejecutiva', 'photo' => null, 'text' => 'Experta en desarrollo agrícola sostenible con 15 años de experiencia trabajando con comunidades rurales.', 'links' => []],
        ['name' => 'Franz Aguilar', 'role' => 'Director de Tecnología', 'photo' => 'img/team/franz.jpg', 'text' => 'Desarrollador Full Stack y creador de AgroMarket. Apasionado por conectar la tecnología con el campo para impulsar el desarrollo agrícola.', 'links' => [
            ['icon' => 'fab fa-linkedin-in', 'label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/in/franz-kennedy-aguilar-cerna-5ab72226a'],
            ['icon' => 'fab fa-github', 'label' => 'GitHub', 'url' => 'https://github.com/Fcranz28'],
            ['icon' => 'fab fa-facebook-f', 'label' => 'Facebook', 'url' => 'https://web.facebook.com/franz.aguilarcerna/'],
        ]],
        ['name' => 'Ana Torres', 'role' => 'Gerente de Desarrollo Comunitario', 'photo' => null, 'text' => 'Dedicada a fortalecer vínculos con comunidades agrícolas y promover el desarrollo sostenible.', 'links' => []],
    ];

@endphp

@section('content')
    <section class="mx-auto mt-6 max-w-6xl overflow-hidden rounded-[2rem] bg-linear-135 from-[#3f6a1f] via-brand-strong to-brand px-5 py-16 text-center text-white sm:py-20">
        <div class="mx-auto max-w-3xl">
            <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">Conectando el campo con el futuro</h1>
            <p class="mt-4 text-lg text-white/90">Transformando la agricultura peruana a través de la innovación tecnológica y el comercio justo.</p>

            <dl class="mt-10 grid grid-cols-3 gap-4">
                @foreach ($heroStats as $stat)
                    <div class="flex flex-col-reverse rounded-card bg-white/10 px-2 py-5 backdrop-blur">
                        <dt class="text-sm text-white/80">{{ $stat['label'] }}</dt>
                        <dd class="text-3xl font-bold sm:text-4xl" x-data="counter({{ $stat['count'] }})" x-intersect.once="start()" x-text="display">0</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    <div class="mx-auto flex max-w-6xl flex-col gap-20 py-16">
        <section id="mission" class="flex flex-col gap-10">
            <header class="text-center">
                <h2 class="text-3xl font-bold text-ink">Nuestra razón de ser</h2>
                <p class="mt-2 text-muted">Comprometidos con el desarrollo sostenible del sector agrícola peruano</p>
            </header>

            <div class="grid gap-6 md:grid-cols-2">
                @foreach ([['fa-bullseye', 'Misión', 'Empoderar a los agricultores peruanos conectándolos directamente con los consumidores, promoviendo el comercio justo y sostenible mientras facilitamos el acceso a tecnología y recursos que mejoren su productividad y calidad de vida.'], ['fa-eye', 'Visión', 'Ser la plataforma líder en la transformación digital del sector agrícola peruano, creando un ecosistema donde la tecnología y la tradición se unen para construir un futuro más próspero para nuestras comunidades agrícolas.']] as [$icon, $title, $text])
                    <x-ui.card x-reveal>
                        <span class="mb-4 flex size-14 items-center justify-center rounded-2xl bg-brand-soft text-2xl text-brand"><i class="fas {{ $icon }}"></i></span>
                        <h3 class="text-xl font-semibold text-ink">{{ $title }}</h3>
                        <p class="mt-2 leading-relaxed text-muted">{{ $text }}</p>
                    </x-ui.card>
                @endforeach
            </div>
        </section>

        <section class="flex flex-col gap-10">
            <header class="text-center">
                <h2 class="text-3xl font-bold text-ink">Nuestros valores</h2>
                <p class="mt-2 text-muted">Los principios que guían nuestro trabajo diario</p>
            </header>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($values as $value)
                    <div class="rounded-card border border-line bg-surface p-6 text-center shadow-soft transition hover:-translate-y-1 hover:shadow-card" x-reveal>
                        <span class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-brand text-xl text-white"><i class="fas {{ $value['icon'] }}"></i></span>
                        <h3 class="font-semibold text-ink">{{ $value['title'] }}</h3>
                        <p class="mt-2 text-sm text-muted">{{ $value['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="flex flex-col gap-10 rounded-[2rem] bg-brand-soft px-6 py-12">
            <header class="text-center">
                <h2 class="text-3xl font-bold text-ink">Nuestro impacto</h2>
                <p class="mt-2 text-muted">Resultados que transforman vidas</p>
            </header>

            <dl class="grid grid-cols-2 gap-6 lg:grid-cols-4">
                @foreach ($impact as $item)
                    <div class="flex flex-col-reverse text-center">
                        <dt class="mt-1 text-sm text-muted">{{ $item['label'] }}</dt>
                        <dd class="text-4xl font-extrabold text-brand-strong">
                            <span x-data="counter({{ $item['count'] }})" x-intersect.once="start()" x-text="display">0</span>{{ $item['suffix'] }}
                        </dd>
                    </div>
                @endforeach
            </dl>
        </section>

        <section id="team" class="flex flex-col gap-10">
            <header class="text-center">
                <h2 class="text-3xl font-bold text-ink">Nuestro equipo</h2>
                <p class="mt-2 text-muted">Las personas detrás de la transformación agrícola</p>
            </header>

            <div class="grid gap-6 md:grid-cols-3">
                @foreach ($team as $member)
                    <x-ui.card class="text-center" x-reveal>
                        <div class="mx-auto mb-4 flex size-28 items-center justify-center overflow-hidden rounded-full bg-brand-soft text-4xl text-brand">
                            @if ($member['photo'])
                                <img src="{{ asset($member['photo']) }}" alt="{{ $member['name'] }}" class="size-full object-cover">
                            @else
                                <i class="fas fa-user-tie"></i>
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold text-ink">{{ $member['name'] }}</h3>
                        <p class="text-sm font-medium text-brand">{{ $member['role'] }}</p>
                        <p class="mt-3 text-sm text-muted">{{ $member['text'] }}</p>

                        @if ($member['links'])
                            <div class="mt-4 flex justify-center gap-3">
                                @foreach ($member['links'] as $link)
                                    <a href="{{ $link['url'] }}" target="_blank" rel="noopener" aria-label="{{ $link['label'] }}" class="flex size-9 items-center justify-center rounded-full border border-line text-muted transition hover:border-brand hover:bg-brand hover:text-white"><i class="{{ $link['icon'] }}"></i></a>
                                @endforeach
                            </div>
                        @endif
                    </x-ui.card>
                @endforeach
            </div>
        </section>

        <section id="careers" class="rounded-[2rem] bg-linear-135 from-brand-strong to-brand px-6 py-14 text-center text-white">
            <h2 class="text-3xl font-bold">Sé parte del cambio</h2>
            <p class="mx-auto mt-3 max-w-xl text-white/90">Únete a nuestra comunidad y ayuda a transformar el sector agrícola peruano.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-pill bg-white px-6 py-3 font-semibold text-brand-strong transition hover:shadow-lifted"><i class="fas fa-user-plus"></i> Únete como agricultor</a>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-pill border-2 border-white px-6 py-3 font-semibold transition hover:bg-white/10"><i class="fas fa-shopping-bag"></i> Explora productos</a>
            </div>
        </section>
    </div>
@endsection
