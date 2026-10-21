@extends('layouts.dashboard')

@php
    use App\Modules\Shared\Money;
@endphp

@section('title', 'Panel de agricultor')
@section('header', 'Mi resumen')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="Mis productos" :value="$products" icon="fa-box-open" />
        <x-ui.stat-card label="Ventas" :value="$sales" icon="fa-shopping-cart" hint="Pedidos pagados con tus productos" />
        <x-ui.stat-card label="Ingresos" :value="Money::fromDecimal($revenue)->format()" icon="fa-wallet" hint="IGV incluido" />
        <x-ui.stat-card label="Valoración" :value="$averageRating ? number_format($averageRating, 1).' ★' : '—'" icon="fa-star" :hint="$averageRating ? 'Promedio de tus reseñas' : 'Aún sin reseñas'" />
    </div>

    <x-ui.card>
        <h2 class="text-lg font-semibold text-ink">Ingresos por mes</h2>
        <p class="mb-5 text-sm text-muted">Últimos 12 meses, pedidos pagados y no cancelados</p>
        <x-ui.bar-chart :data="$monthlyRevenue" label="Ingresos por mes de los últimos 12 meses" />
    </x-ui.card>

    <x-ui.card :padding="false">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-4">
            <h2 class="text-lg font-semibold text-ink">Productos recientes</h2>
            <x-ui.button size="sm" :href="route('dashboard.productos.create')"><i class="fas fa-plus"></i> Nuevo producto</x-ui.button>
        </div>

        @include('dashboard.products.partials.table', ['products' => $recentProducts, 'editRoute' => 'dashboard.productos.edit', 'showRoute' => 'dashboard.productos.show', 'destroyRoute' => null])
    </x-ui.card>
@endsection
