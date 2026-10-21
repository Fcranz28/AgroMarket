@extends('layouts.app')

@section('content')
    <div class="mx-auto flex max-w-3xl flex-col gap-6 pt-10 pb-16">
        <div class="h-2 overflow-hidden rounded-full bg-line" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
            <div class="h-full w-1/2 rounded-full bg-brand"></div>
        </div>

        <x-ui.card class="p-8 sm:p-10">
            <header class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-ink">¡Personaliza tu experiencia!</h1>
                <p class="mt-2 text-muted">Selecciona las categorías que más te interesan.</p>
            </header>

            <form action="{{ route('onboarding.preferences') }}" method="POST" class="flex flex-col gap-8">
                @csrf

                <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
                    @foreach ($categories as $category)
                        <label class="relative flex cursor-pointer items-center justify-center rounded-xl border-2 border-line bg-canvas px-3 py-4 text-center font-medium text-ink transition hover:border-brand has-checked:border-brand has-checked:bg-brand-soft has-checked:text-brand-strong">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="peer sr-only">
                            {{ $category->name }}
                            <span class="absolute top-1.5 right-2 hidden text-xs peer-checked:block"><i class="fas fa-check"></i></span>
                        </label>
                    @endforeach
                </div>

                <div class="flex flex-col items-center gap-3">
                    <x-ui.button type="submit" size="lg" class="w-full sm:w-auto">Finalizar y explorar</x-ui.button>
                    <a href="{{ route('home') }}" class="text-sm text-muted hover:text-brand">Omitir por ahora</a>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection
