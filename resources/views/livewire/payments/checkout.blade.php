<div class="mx-auto max-w-6xl pt-6 pb-12" x-data x-on:cart-updated.window="window.location.reload()">
    <h1 class="mb-6 text-2xl font-bold text-ink sm:text-3xl">Finalizar compra</h1>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
        <div class="flex flex-col gap-6">
            <x-ui.card>
                <h2 class="mb-5 text-lg font-semibold text-ink">Datos del comprador</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-ui.input name="guest_name" label="Nombres" wire:model="guest_name" autocomplete="given-name" required />
                    <x-ui.input name="guest_lastname" label="Apellidos" wire:model="guest_lastname" autocomplete="family-name" required />
                    <x-ui.input name="guest_email" type="email" label="Correo electrónico" wire:model="guest_email" autocomplete="email" required class="sm:col-span-2" />

                    <div class="flex flex-col gap-1.5">
                        <label for="document_type" class="text-sm font-medium text-ink">Tipo de documento</label>
                        <select id="document_type" wire:model="document_type" class="w-full rounded-xl border border-line bg-surface px-4 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/25 focus:outline-none">
                            @foreach (\App\Modules\Payments\Livewire\Checkout::DOCUMENT_TYPES as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('document_type') <p class="text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                    <x-ui.input name="document_number" label="Número de documento" wire:model="document_number" placeholder="Ej: 12345678" required />

                    <x-ui.input name="phone" type="tel" label="Teléfono" wire:model="phone" autocomplete="tel" placeholder="Ej: 987654321" required class="sm:col-span-2" />
                </div>
            </x-ui.card>

            <x-ui.card>
                <h2 class="mb-5 text-lg font-semibold text-ink">Dirección de envío</h2>

                <div class="flex flex-col gap-3">
                    @foreach ($this->addresses as $address)
                        <label wire:key="address-{{ $address->id }}" class="flex cursor-pointer items-start gap-3 rounded-xl border border-line p-4 transition has-checked:border-brand has-checked:bg-brand-soft">
                            <input type="radio" wire:model.live="addressChoice" value="{{ $address->id }}" class="mt-1 accent-brand">
                            <span class="text-sm text-ink">
                                {{ $address->address }}
                                @if ($address->is_default)
                                    <x-ui.badge class="ml-1">Principal</x-ui.badge>
                                @endif
                            </span>
                        </label>
                    @endforeach

                    @if ($this->addresses->isNotEmpty())
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-4 transition has-checked:border-brand has-checked:bg-brand-soft">
                            <input type="radio" wire:model.live="addressChoice" value="{{ \App\Modules\Payments\Livewire\Checkout::NEW_ADDRESS }}" class="accent-brand">
                            <span class="text-sm text-ink">Otra dirección</span>
                        </label>
                    @endif

                    @if ($addressChoice === \App\Modules\Payments\Livewire\Checkout::NEW_ADDRESS)
                        <div class="flex flex-col gap-1.5">
                            <label for="shipping_address" class="sr-only">Dirección</label>
                            <textarea id="shipping_address" wire:model="shipping_address" rows="2" placeholder="Calle, número, distrito, ciudad y una referencia" autocomplete="street-address" @class([
                                'w-full rounded-xl border bg-surface px-4 py-2.5 text-sm text-ink placeholder:text-muted/70 focus:ring-2 focus:outline-none',
                                'border-line focus:border-brand focus:ring-brand/25' => ! $errors->has('shipping_address'),
                                'border-danger focus:ring-danger/25' => $errors->has('shipping_address'),
                            ])></textarea>
                            @error('shipping_address') <p class="text-xs text-danger">{{ $message }}</p> @enderror
                        </div>

                        @auth
                            <label class="flex items-center gap-2 text-sm text-muted">
                                <input type="checkbox" wire:model="saveAddress" class="accent-brand">
                                Guardar en mis direcciones
                            </label>
                        @endauth
                    @endif
                </div>
            </x-ui.card>

            <x-ui.card>
                <div x-data="stripePayment({ key: @js($stripeKey), amount: {{ $total->cents() }}, currency: @js($currency) })">
                    <h2 class="mb-5 text-lg font-semibold text-ink">Pago con tarjeta</h2>

                    <div wire:ignore x-ref="paymentElement" class="min-h-24"></div>
                    <p x-show="! ready && ! error" class="text-sm text-muted">Cargando el formulario de pago…</p>

                    <x-ui.alert type="error" class="mt-4" x-cloak x-show="error"><span x-text="error"></span></x-ui.alert>
                    @error('payment')
                        <x-ui.alert type="error" class="mt-4">{{ $message }}</x-ui.alert>
                    @enderror

                    <x-ui.button size="lg" class="mt-5 w-full" x-on:click="pay()" x-bind:disabled="! ready || processing">
                        <svg x-show="processing" x-cloak class="size-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                        <span x-text="processing ? 'Procesando…' : 'Pagar {{ $total->format() }}'">Pagar {{ $total->format() }}</span>
                    </x-ui.button>

                    <p class="mt-3 flex items-center justify-center gap-1.5 text-xs text-muted">
                        <svg class="size-3.5 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                        Pagos seguros procesados por Stripe
                    </p>
                </div>
            </x-ui.card>
        </div>

        <x-ui.card class="lg:sticky lg:top-28">
            <h2 class="mb-2 text-lg font-semibold text-ink">Resumen del pedido</h2>

            <ul class="flex flex-col divide-y divide-line">
                @foreach ($lines as $line)
                    <li wire:key="summary-{{ $line['key'] }}" class="flex items-center gap-3 py-3">
                        <x-catalog.product-image :product="$line['product']" class="size-14 shrink-0 rounded-lg" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-ink">{{ $line['product']->name }}</p>
                            <p class="text-sm text-muted">{{ $line['quantity'] }} × {{ $line['price']->format() }} / {{ $line['unit'] }}</p>
                        </div>
                        <span class="font-semibold text-ink">{{ $line['subtotal']->format() }}</span>
                    </li>
                @endforeach
            </ul>

            <dl class="mt-2 flex flex-col gap-2 border-t border-line pt-4 text-sm">
                <div class="flex justify-between text-muted"><dt>Op. gravada</dt><dd>{{ $total->withoutIgv()->format() }}</dd></div>
                <div class="flex justify-between text-muted"><dt>IGV (18%, incluido)</dt><dd>{{ $total->igv()->format() }}</dd></div>
                <div class="flex justify-between border-t border-line pt-3 text-lg font-bold text-ink"><dt>Total</dt><dd>{{ $total->format() }}</dd></div>
            </dl>
        </x-ui.card>
    </div>
</div>
