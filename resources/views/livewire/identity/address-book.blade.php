<div class="flex flex-col gap-4">
    @forelse ($this->addresses as $address)
        <div wire:key="address-{{ $address->id }}" class="flex flex-wrap items-center gap-3 rounded-xl border border-line bg-surface p-4">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-soft text-brand"><i class="fas fa-map-marker-alt"></i></span>
            <p class="min-w-0 flex-1 text-sm text-ink">
                {{ $address->address }}
                @if ($address->is_default)
                    <x-ui.badge class="ml-1">Principal</x-ui.badge>
                @endif
            </p>
            <div class="flex gap-2">
                @unless ($address->is_default)
                    <x-ui.button variant="ghost" size="sm" wire:click="makeDefault({{ $address->id }})">Usar como principal</x-ui.button>
                @endunless
                <x-ui.button variant="ghost" size="sm" class="text-danger hover:bg-danger/10 hover:text-danger" wire:click="remove({{ $address->id }})" wire:confirm="¿Eliminar esta dirección?">Eliminar</x-ui.button>
            </div>
        </div>
    @empty
        <p class="rounded-xl border border-dashed border-line p-6 text-center text-sm text-muted">Aún no tienes direcciones guardadas.</p>
    @endforelse

    @if ($this->addresses->count() < $maxAddresses)
        <form wire:submit="add" class="flex flex-col gap-3 rounded-xl border border-line bg-canvas p-4">
            <label for="newAddress" class="text-sm font-medium text-ink">Nueva dirección</label>
            <textarea id="newAddress" wire:model="newAddress" rows="2" placeholder="Calle, número, distrito, ciudad y una referencia" @class([
                'w-full rounded-xl border bg-surface px-4 py-2.5 text-sm text-ink placeholder:text-muted/70 focus:ring-2 focus:outline-none',
                'border-line focus:border-brand focus:ring-brand/25' => ! $errors->has('newAddress'),
                'border-danger focus:ring-danger/25' => $errors->has('newAddress'),
            ])></textarea>
            @error('newAddress') <p class="text-xs text-danger">{{ $message }}</p> @enderror
            <x-ui.button type="submit" class="self-start" wire:loading.attr="disabled">Guardar dirección</x-ui.button>
        </form>
    @else
        <p class="text-sm text-muted">Puedes guardar hasta {{ $maxAddresses }} direcciones.</p>
    @endif
</div>
