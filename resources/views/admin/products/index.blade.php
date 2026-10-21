@extends('layouts.dashboard')

@section('title', 'Productos')
@section('header', 'Productos')

@section('content')
    <x-ui.card :padding="false">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-4">
            <p class="text-sm text-muted">{{ trans_choice('{0} Sin productos|{1} 1 producto|[2,*] :count productos', $products->total()) }}</p>
            <x-ui.button size="sm" :href="route('admin.productos.create')"><i class="fas fa-plus"></i> Nuevo producto</x-ui.button>
        </div>

        @include('dashboard.products.partials.table', ['editRoute' => 'admin.productos.edit', 'showRoute' => 'admin.productos.show', 'destroyRoute' => 'admin.productos.destroy', 'showOwner' => true])
    </x-ui.card>
@endsection
