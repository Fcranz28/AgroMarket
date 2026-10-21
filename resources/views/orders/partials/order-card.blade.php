@php
    use App\Modules\Orders\Enums\OrderStatus;
    use App\Modules\Shared\Money;
@endphp

<article class="rounded-card border border-line bg-surface shadow-soft">
    <header class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-4">
        <div>
            <p class="font-semibold text-ink">Pedido #{{ $order->id }}</p>
            <p class="text-sm text-muted">{{ $order->created_at->translatedFormat('d M Y') }}</p>
        </div>
        <x-ui.badge :color="$order->status->badgeColor()">{{ $order->status->label() }}</x-ui.badge>
    </header>

    <ul class="divide-y divide-line px-5">
        @foreach ($order->items->take(3) as $item)
            <li class="flex items-center gap-3 py-3">
                <x-catalog.product-image :product="$item->product" class="size-14 shrink-0 rounded-lg" />
                <div class="min-w-0 flex-1">
                    <p class="truncate font-medium text-ink">{{ $item->product->name }}</p>
                    <p class="text-sm text-muted">Cantidad: {{ $item->quantity }}{{ $item->unit ? ' '.$item->unit : '' }}</p>
                    @if ($order->status === OrderStatus::Delivered)
                        <button
                            type="button"
                            x-on:click="$dispatch('open-modal', { name: 'report', data: { orderId: {{ $order->id }}, productId: {{ $item->product->id }}, productName: @js($item->product->name) } })"
                            class="mt-1 inline-flex items-center gap-1 text-xs font-medium text-danger hover:underline"
                        ><i class="fas fa-exclamation-triangle"></i> Reportar un problema</button>
                    @endif
                </div>
                <span class="text-sm font-semibold text-ink">{{ Money::fromDecimal($item->price)->format() }}</span>
            </li>
        @endforeach
    </ul>

    @if ($order->items->count() > 3)
        <p class="px-5 pb-2 text-sm text-muted">+ {{ $order->items->count() - 3 }} producto(s) más</p>
    @endif

    <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-line px-5 py-4">
        <p class="text-ink">Total: <strong>{{ Money::fromDecimal($order->total)->format() }}</strong></p>
        <div class="flex flex-wrap gap-2">
            <x-ui.button variant="secondary" size="sm" :href="route('orders.show', $order)">Ver detalles</x-ui.button>
            @if ($order->invoice)
                <x-ui.button variant="secondary" size="sm" :href="route('invoice.download', $order->invoice)"><i class="fas fa-file-invoice"></i> Comprobante</x-ui.button>
            @endif
            @unless ($order->status === OrderStatus::Cancelled)
                <x-ui.button variant="secondary" size="sm" x-on:click="$dispatch('open-modal', { name: 'tracking', data: { status: {{ Js::from($order->status->value) }}, orderId: {{ $order->id }} } })"><i class="fas fa-map-marker-alt"></i> Rastrear</x-ui.button>
            @endunless
        </div>
    </footer>
</article>
