@extends('layouts.app')

@php
    use App\Modules\Orders\Enums\OrderStatus;
    use App\Modules\Orders\Enums\PaymentStatus;
    use App\Modules\Shared\Money;

    $total = Money::fromDecimal($order->total);
    $steps = OrderStatus::trackingSteps();
    $currentStep = array_search($order->status, $steps, true);
@endphp

@section('content')
    <div class="mx-auto flex max-w-6xl flex-col gap-6 pt-6 pb-12">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <a href="{{ auth()->user()->isFarmer() ? route('farmer.orders') : route('orders.index') }}" class="text-sm font-medium text-brand hover:underline">← Volver a mis pedidos</a>
                <h1 class="mt-2 text-3xl font-bold text-ink">Pedido #{{ $order->id }}</h1>
                <p class="text-muted">Realizado el {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <x-ui.badge :color="$order->status->badgeColor()" class="px-4 py-1 text-sm">{{ $order->status->label() }}</x-ui.badge>
        </header>

        @if (session('status'))
            <x-ui.alert type="success">{{ session('status') }}</x-ui.alert>
        @endif

        @if ($currentStep !== false)
            <x-ui.card>
                <ol class="flex items-start" aria-label="Seguimiento">
                    @foreach ($steps as $index => $step)
                        <li class="flex flex-1 flex-col items-center gap-2 text-center">
                            <div class="flex w-full items-center">
                                <span @class(['h-0.5 flex-1', 'invisible' => $loop->first, 'bg-brand' => $index <= $currentStep, 'bg-line' => $index > $currentStep])></span>
                                <span @class([
                                    'flex size-9 shrink-0 items-center justify-center rounded-full border-2 text-sm font-semibold',
                                    'border-brand bg-brand text-white' => $index <= $currentStep,
                                    'ring-4 ring-brand/25' => $index === $currentStep,
                                    'border-line text-muted' => $index > $currentStep,
                                ])>
                                    @if ($index < $currentStep)
                                        <i class="fas fa-check"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </span>
                                <span @class(['h-0.5 flex-1', 'invisible' => $loop->last, 'bg-brand' => $index < $currentStep, 'bg-line' => $index >= $currentStep])></span>
                            </div>
                            <span @class(['text-xs font-medium sm:text-sm', 'text-ink' => $index <= $currentStep, 'text-muted' => $index > $currentStep])>{{ $step->label() }}</span>
                        </li>
                    @endforeach
                </ol>
            </x-ui.card>
        @endif

        <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            <x-ui.card>
                <h2 class="mb-2 text-lg font-semibold text-ink">Productos</h2>
                <ul class="divide-y divide-line">
                    @foreach ($order->items as $item)
                        @php($price = Money::fromDecimal($item->price))
                        <li class="flex items-center gap-4 py-4">
                            <x-catalog.product-image :product="$item->product" class="size-16 shrink-0 rounded-lg" />
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('products.show', $item->product->slug) }}" class="font-medium text-ink hover:text-brand">{{ $item->product->name }}</a>
                                <p class="text-sm text-muted">{{ $item->quantity }} × {{ $price->format() }}{{ $item->unit ? ' / '.$item->unit : '' }}</p>
                            </div>
                            <span class="font-semibold text-ink">{{ $price->multiply($item->quantity)->format() }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-ui.card>

            <div class="flex flex-col gap-4">
                <x-ui.card>
                    <h2 class="mb-3 text-lg font-semibold text-ink">Resumen</h2>
                    <dl class="flex flex-col gap-2 text-sm">
                        <div class="flex justify-between text-muted"><dt>Op. gravada</dt><dd>{{ $total->withoutIgv()->format() }}</dd></div>
                        <div class="flex justify-between text-muted"><dt>IGV (18%, incluido)</dt><dd>{{ $total->igv()->format() }}</dd></div>
                        <div class="flex justify-between text-muted"><dt>Envío</dt><dd>Gratis</dd></div>
                        <div class="flex justify-between border-t border-line pt-3 text-lg font-bold text-ink"><dt>Total</dt><dd>{{ $total->format() }}</dd></div>
                    </dl>
                    @if ($order->invoice)
                        <x-ui.button class="mt-4 w-full" :href="route('invoice.download', $order->invoice)"><i class="fas fa-file-pdf"></i> Descargar comprobante</x-ui.button>
                    @endif
                </x-ui.card>

                <x-ui.card>
                    <h2 class="mb-3 text-lg font-semibold text-ink">Envío</h2>
                    <p class="text-sm text-ink">{{ $order->shipping_address }}</p>
                    @if ($order->phone)
                        <p class="mt-2 text-sm text-muted"><i class="fas fa-phone mr-1"></i> {{ $order->phone }}</p>
                    @endif
                </x-ui.card>

                <x-ui.card>
                    <h2 class="mb-3 text-lg font-semibold text-ink">Pago</h2>
                    <p class="flex items-center gap-2 text-sm">
                        <x-ui.badge :color="$order->payment_status === PaymentStatus::Paid ? 'success' : 'warning'">{{ $order->payment_status->label() }}</x-ui.badge>
                    </p>
                    @if ($order->stripe_payment_intent_id)
                        <p class="mt-2 truncate font-mono text-xs text-muted">{{ $order->stripe_payment_intent_id }}</p>
                    @endif
                </x-ui.card>
            </div>
        </div>
    </div>
@endsection
