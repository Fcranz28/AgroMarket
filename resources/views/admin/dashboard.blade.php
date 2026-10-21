@extends('layouts.dashboard')

@section('title', 'Panel de administración')
@section('header', 'Resumen general')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="Ingresos" :value="$totalRevenue->format()" icon="fa-wallet" :hint="trans_choice('{0} Sin pedidos pagados|{1} 1 pedido pagado|[2,*] :count pedidos pagados', $totalOrders)" />
        <x-ui.stat-card label="Productos" :value="number_format($totalProducts)" icon="fa-box" />
        <x-ui.stat-card label="Agricultores" :value="number_format($totalFarmers)" icon="fa-tractor" :hint="$pendingFarmersCount ? $pendingFarmersCount.' por verificar' : 'Todos verificados'" />
        <x-ui.stat-card label="Compradores" :value="number_format($totalUsers)" icon="fa-users" />
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
        <x-ui.card>
            <h2 class="text-lg font-semibold text-ink">Ingresos por mes</h2>
            <p class="mb-5 text-sm text-muted">Últimos 12 meses, pedidos pagados y no cancelados</p>
            <x-ui.bar-chart :data="$monthlyRevenue" label="Ingresos por mes de los últimos 12 meses" />
        </x-ui.card>

        <x-ui.card class="flex flex-col gap-4">
            <h2 class="text-lg font-semibold text-ink">Por atender</h2>

            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 rounded-xl border border-line p-4 transition hover:border-brand">
                <span class="flex size-10 items-center justify-center rounded-full bg-danger/10 text-danger"><i class="fas fa-exclamation-circle"></i></span>
                <span class="flex-1 text-sm text-ink">Reportes pendientes</span>
                <x-ui.badge :color="$pendingReportsCount ? 'danger' : 'neutral'">{{ $pendingReportsCount }}</x-ui.badge>
            </a>

            <div class="flex flex-col gap-2">
                <p class="text-sm font-medium text-ink">Agricultores por verificar</p>
                @forelse ($pendingFarmers as $farmer)
                    <a href="{{ route('admin.verify.view', $farmer) }}" class="flex items-center gap-3 rounded-xl p-2 transition hover:bg-brand-soft">
                        <span class="flex size-9 items-center justify-center rounded-full bg-night font-semibold text-lime">{{ mb_strtoupper(mb_substr($farmer->name, 0, 1)) }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium text-ink">{{ $farmer->name }}</span>
                            <span class="block truncate text-xs text-muted">{{ $farmer->created_at->diffForHumans() }}</span>
                        </span>
                        <i class="fas fa-chevron-right text-xs text-muted"></i>
                    </a>
                @empty
                    <p class="text-sm text-muted">No hay solicitudes pendientes.</p>
                @endforelse
            </div>
        </x-ui.card>
    </div>
@endsection
