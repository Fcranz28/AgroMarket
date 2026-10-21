<div
    x-data="{ open: false }"
    x-on:open-cart.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-effect="document.body.classList.toggle('overflow-hidden', open)"
>
    <div x-cloak x-show="open" x-transition.opacity x-on:click="open = false" class="fixed inset-0 z-[2050] bg-black/40"></div>

    <aside
        x-cloak
        x-show="open"
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="translate-x-full"
        x-transition:leave="transition duration-200 ease-in"
        x-transition:leave-end="translate-x-full"
        data-test="cart-panel"
        class="fixed inset-y-0 right-0 z-[2100] flex w-full max-w-sm flex-col bg-surface text-ink shadow-2xl"
        role="dialog"
        aria-modal="true"
        aria-labelledby="cart-title"
    >
        <header class="flex items-center justify-between border-b border-line px-5 py-4">
            <h2 id="cart-title" class="text-lg font-semibold">Tu carrito</h2>
            <button type="button" x-on:click="open = false" class="rounded-full p-1.5 text-muted transition hover:bg-brand-soft hover:text-brand" aria-label="Cerrar carrito">
                <svg class="size-5 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </button>
        </header>

        <div class="flex-1 overflow-y-auto px-5 py-4" wire:loading.class="opacity-60">
            @forelse ($lines as $line)
                <div wire:key="cart-line-{{ $line['key'] }}" class="flex gap-3 border-b border-line py-4 last:border-b-0">
                    <a href="{{ route('products.show', $line['product']->slug) }}" class="shrink-0">
                        <x-catalog.product-image :product="$line['product']" class="size-16 rounded-lg" />
                    </a>

                    <div class="flex min-w-0 flex-1 flex-col gap-1">
                        <a href="{{ route('products.show', $line['product']->slug) }}" class="truncate font-medium hover:text-brand">{{ $line['product']->name }}</a>
                        <p class="text-sm text-muted">{{ $line['price']->format() }} / {{ $line['unit'] }}</p>

                        <div class="mt-1 flex items-center justify-between">
                            <div class="flex items-center rounded-pill border border-line">
                                <button type="button" wire:click="decrement(@js($line['key']), {{ $line['quantity'] }})" class="px-2.5 py-1 text-muted transition hover:text-brand" aria-label="Quitar uno">−</button>
                                <span class="min-w-6 text-center text-sm font-semibold">{{ $line['quantity'] }}</span>
                                <button type="button" wire:click="increment(@js($line['key']), {{ $line['quantity'] }})" @disabled($line['quantity'] >= $line['stock']) class="px-2.5 py-1 text-muted transition hover:text-brand disabled:opacity-40" aria-label="Agregar uno" data-test="cart-increment">+</button>
                            </div>
                            <span class="text-sm font-semibold">{{ $line['subtotal']->format() }}</span>
                        </div>
                    </div>

                    <button type="button" wire:click="remove(@js($line['key']))" wire:confirm="¿Quitar {{ $line['product']->name }} del carrito?" class="self-start rounded-full p-1.5 text-muted transition hover:bg-danger/10 hover:text-danger" aria-label="Quitar {{ $line['product']->name }}">
                        <svg class="size-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                    </button>
                </div>
            @empty
                <div class="flex h-full flex-col items-center justify-center gap-3 text-center text-muted">
                    <svg class="size-12 fill-current opacity-40" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                    <p>Tu carrito está vacío.</p>
                    <x-ui.button variant="secondary" size="sm" :href="route('products.index')">Ver productos</x-ui.button>
                </div>
            @endforelse
        </div>

        <footer class="border-t border-line px-5 py-4">
            <div class="mb-3 flex items-center justify-between text-lg font-semibold">
                <span>Total</span>
                <span>{{ $total->format() }}</span>
            </div>
            <p class="mb-3 text-xs text-muted">Los precios incluyen IGV.</p>
            @if ($lines->isEmpty())
                <x-ui.button class="w-full" disabled>Proceder al pago</x-ui.button>
            @else
                <x-ui.button class="w-full" :href="route('checkout.index')">Proceder al pago</x-ui.button>
            @endif
        </footer>
    </aside>
</div>
