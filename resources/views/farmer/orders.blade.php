@extends('layouts.dashboard')

@php
    use App\Modules\Orders\Enums\OrderStatus;
    use App\Modules\Shared\Money;

    $farmerId = auth()->id();
    $filters = ['all' => 'Todos', 'pending' => 'Pendientes', 'processing' => 'En proceso', 'shipped' => 'Enviados', 'delivered' => 'Entregados'];

    // Lo que corresponde a este agricultor en cada pedido (puede haber productos de otros)
    $farmerTotals = $orders->mapWithKeys(fn ($order) => [
        $order->id => $order->items
            ->filter(fn ($item) => $item->product?->user_id === $farmerId)
            ->reduce(fn (Money $total, $item) => $total->add(Money::fromDecimal($item->price)->multiply($item->quantity)), Money::zero()),
    ]);

    $statusOptions = collect(OrderStatus::cases())
        ->mapWithKeys(fn (OrderStatus $status) => [$status->value => $status === OrderStatus::Cancelled ? 'Cancelado (devuelve stock y reembolsa)' : $status->label()]);
@endphp

@section('title', 'Mis pedidos')
@section('header', 'Pedidos')

@section('content')
    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.stat-card label="Por atender" :value="$pendingOrders->count()" icon="fa-clock" />
        <x-ui.stat-card label="Pedidos" :value="$orders->count()" icon="fa-shopping-bag" />
        <x-ui.stat-card label="Tus ventas" :value="$farmerTotals->reduce(fn ($total, $amount) => $total->add($amount), Money::zero())->format()" icon="fa-wallet" hint="Solo tus productos, IGV incluido" />
    </div>

    <div class="flex flex-col gap-4" x-data="{ filter: 'all' }">
        <div class="-mx-4 flex gap-2 overflow-x-auto px-4 [scrollbar-width:none] sm:mx-0 sm:px-0" role="group" aria-label="Filtrar por estado">
            @foreach ($filters as $value => $label)
                <button type="button" x-on:click="filter = @js($value)" x-bind:aria-pressed="filter === @js($value)" x-bind:class="filter === @js($value) ? 'border-brand bg-brand text-white' : 'border-line bg-surface text-ink hover:border-brand'" class="shrink-0 rounded-pill border px-4 py-1.5 text-sm font-medium whitespace-nowrap transition">{{ $label }}</button>
            @endforeach
        </div>

        @forelse ($orders as $order)
            <article x-show="filter === 'all' || filter === @js($order->status->value)" class="rounded-card border border-line bg-surface shadow-soft">
                <header class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-4">
                    <div>
                        <p class="font-semibold text-ink">Pedido #{{ $order->id }}</p>
                        <p class="text-sm text-muted">{{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->user?->name ?? trim($order->guest_name.' '.$order->guest_lastname) }} <span class="text-xs">({{ $order->user ? 'cliente' : 'invitado' }})</span></p>
                    </div>
                    <x-ui.badge :color="$order->status->badgeColor()">{{ $order->status->label() }}</x-ui.badge>
                </header>

                <ul class="divide-y divide-line px-5">
                    @foreach ($order->items as $item)
                        @continue($item->product?->user_id !== $farmerId)
                        @php($price = Money::fromDecimal($item->price))
                        <li class="flex items-center gap-3 py-3">
                            <x-catalog.product-image :product="$item->product" class="size-12 shrink-0 rounded-lg" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-ink">{{ $item->product->name }}</p>
                                <p class="text-sm text-muted">{{ $item->quantity }} × {{ $price->format() }}{{ $item->unit ? ' / '.$item->unit : '' }}</p>
                            </div>
                            <span class="text-sm font-semibold text-ink">{{ $price->multiply($item->quantity)->format() }}</span>
                        </li>
                    @endforeach
                </ul>

                <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-line px-5 py-4">
                    <p class="text-ink">Tu parte: <strong>{{ $farmerTotals[$order->id]->format() }}</strong></p>
                    <div class="flex flex-wrap gap-2">
                        <x-ui.button variant="secondary" size="sm" :href="route('farmer.orders.show', $order)"><i class="fas fa-eye"></i> Ver detalles</x-ui.button>
                        @unless ($order->isFinal())
                            <x-ui.button size="sm" x-on:click="$dispatch('open-modal', { name: 'order-status', data: { id: {{ $order->id }}, uuid: {{ Js::from($order->uuid) }}, status: {{ Js::from($order->status->value) }} } })"><i class="fas fa-truck"></i> Actualizar estado</x-ui.button>
                        @endunless
                    </div>
                </footer>
            </article>
        @empty
            <x-ui.card class="flex flex-col items-center gap-3 py-14 text-center">
                <i class="fas fa-shopping-bag text-4xl text-brand"></i>
                <h2 class="text-lg font-semibold text-ink">No tienes pedidos aún</h2>
                <p class="text-sm text-muted">Cuando los clientes compren tus productos, aparecerán aquí.</p>
            </x-ui.card>
        @endforelse
    </div>

    <x-ui.modal name="order-status" title="Actualizar estado" max-width="max-w-md">
        <form
            class="flex flex-col gap-4"
            x-data="{ status: '', saving: false, error: null }"
            x-effect="status = data.status ?? ''"
            x-on:submit.prevent="
                saving = true; error = null;
                fetch(@js(url('/agricultor/pedidos')) + '/' + data.uuid + '/status', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({ status }),
                })
                    .then(async (response) => ({ ok: response.ok, body: await response.json() }))
                    .then(({ ok, body }) => {
                        if (! ok || ! body.success) { error = body.message ?? 'No se pudo actualizar el estado.'; return; }
                        $dispatch('notify', { type: 'success', message: body.message });
                        window.location.reload();
                    })
                    .catch(() => error = 'Ocurrió un error al procesar la solicitud.')
                    .finally(() => saving = false)
            "
        >
            <p class="text-sm text-muted">Pedido #<span x-text="data.id"></span></p>
            <label for="order-status-select" class="sr-only">Nuevo estado</label>
            <select id="order-status-select" x-model="status" class="w-full rounded-xl border border-line bg-surface px-4 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/25 focus:outline-none">
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <x-ui.alert type="warning" x-cloak x-show="status === 'cancelled'">Se devolverá el stock y se reembolsará el pago al cliente.</x-ui.alert>
            <p class="text-xs text-danger" x-show="error" x-text="error"></p>
            <div class="flex justify-end gap-2">
                <x-ui.button variant="secondary" x-on:click="$dispatch('close-modal', { name: 'order-status' })">Cancelar</x-ui.button>
                <x-ui.button type="submit" x-bind:disabled="saving || status === data.status">Guardar</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
@endsection
