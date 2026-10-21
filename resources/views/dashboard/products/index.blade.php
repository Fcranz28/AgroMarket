@extends('layouts.dashboard')

@section('title', 'Mis productos')
@section('header', 'Mis productos')

@section('content')
    <x-ui.card :padding="false">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-4">
            <p class="text-sm text-muted">{{ trans_choice('{0} Sin productos|{1} 1 producto|[2,*] :count productos', $products->total()) }}</p>
            <x-ui.button size="sm" :href="route('dashboard.productos.create')"><i class="fas fa-plus"></i> Agregar producto</x-ui.button>
        </div>

        @include('dashboard.products.partials.table', ['editRoute' => 'dashboard.productos.edit', 'showRoute' => 'dashboard.productos.show', 'destroyRoute' => 'dashboard.productos.destroy'])
    </x-ui.card>
@endsection
