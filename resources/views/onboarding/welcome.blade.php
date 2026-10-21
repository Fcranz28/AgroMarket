@extends('layouts.app')

@php
    $roles = [
        [
            'value' => 'user',
            'title' => 'Quiero comprar',
            'text' => 'Busco productos frescos y de calidad directamente del campo a mi mesa.',
            'tone' => 'bg-sky-500/15 text-sky-600 dark:text-sky-300',
            'icon' => 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0z',
        ],
        [
            'value' => 'farmer',
            'title' => 'Quiero vender',
            'text' => 'Soy productor y quiero ofrecer mis cosechas a miles de clientes.',
            'tone' => 'bg-brand-soft text-brand',
            'icon' => 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z',
        ],
    ];
@endphp

@section('content')
    <div class="mx-auto flex max-w-4xl flex-col items-center gap-10 pt-10 pb-16">
        <header class="text-center">
            <h1 class="text-3xl font-bold text-ink sm:text-4xl">Bienvenido a AgroMarket</h1>
            <p class="mt-2 text-lg text-muted">Para comenzar, cuéntanos cómo deseas usar la plataforma.</p>
        </header>

        <div class="grid w-full gap-6 sm:grid-cols-2">
            @foreach ($roles as $role)
                <form action="{{ route('onboarding.role') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="{{ $role['value'] }}">
                    <button type="submit" class="group flex h-full w-full flex-col items-center gap-4 rounded-card border-2 border-line bg-surface p-8 text-center shadow-soft transition hover:-translate-y-2 hover:border-brand hover:shadow-lifted">
                        <span @class(['flex size-20 items-center justify-center rounded-full', $role['tone']])>
                            <svg class="size-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $role['icon'] }}"/></svg>
                        </span>
                        <span class="text-2xl font-semibold text-ink">{{ $role['title'] }}</span>
                        <span class="flex-1 text-muted">{{ $role['text'] }}</span>
                        <span class="rounded-pill bg-canvas px-6 py-2 font-semibold text-ink transition group-hover:bg-night group-hover:text-lime">Seleccionar →</span>
                    </button>
                </form>
            @endforeach
        </div>
    </div>
@endsection
