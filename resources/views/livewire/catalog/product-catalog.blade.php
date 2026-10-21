@php
    $categoryButtonClasses = 'w-full rounded-lg px-3 py-2 text-left text-sm transition hover:bg-brand-soft hover:text-brand';
@endphp

<div class="mx-auto flex max-w-7xl flex-col gap-6 pt-6 pb-12 lg:flex-row lg:gap-8">
    {{-- Categorías: lista lateral en escritorio, pastillas desplazables en móvil --}}
    <aside class="lg:w-60 lg:shrink-0">
        <x-ui.card class="hidden lg:sticky lg:top-28 lg:block" :padding="false">
            <h2 class="border-b border-line px-5 py-4 font-semibold text-ink">Categorías</h2>
            <ul class="flex flex-col gap-0.5 p-2">
                <li>
                    <button type="button" wire:click="selectCategory('all')" @class([$categoryButtonClasses, 'bg-brand-soft font-semibold text-brand' => ! $this->currentCategory, 'text-ink' => (bool) $this->currentCategory])>Todos los productos</button>
                </li>
                @foreach ($this->categories as $categoryOption)
                    <li wire:key="category-{{ $categoryOption->id }}">
                        <button type="button" wire:click="selectCategory(@js($categoryOption->slug))" @class([$categoryButtonClasses, 'bg-brand-soft font-semibold text-brand' => $this->currentCategory?->is($categoryOption), 'text-ink' => ! $this->currentCategory?->is($categoryOption)])>{{ $categoryOption->name }}</button>
                    </li>
                @endforeach
            </ul>
        </x-ui.card>

        <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] lg:hidden">
            <button type="button" wire:click="selectCategory('all')" @class(['shrink-0 rounded-pill border px-4 py-1.5 text-sm whitespace-nowrap transition', 'border-brand bg-brand text-white' => ! $this->currentCategory, 'border-line bg-surface text-ink' => (bool) $this->currentCategory])>Todos</button>
            @foreach ($this->categories as $categoryOption)
                <button type="button" wire:key="category-pill-{{ $categoryOption->id }}" wire:click="selectCategory(@js($categoryOption->slug))" @class(['shrink-0 rounded-pill border px-4 py-1.5 text-sm whitespace-nowrap transition', 'border-brand bg-brand text-white' => $this->currentCategory?->is($categoryOption), 'border-line bg-surface text-ink' => ! $this->currentCategory?->is($categoryOption)])>{{ $categoryOption->name }}</button>
            @endforeach
        </div>
    </aside>

    <section class="min-w-0 flex-1">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-ink sm:text-3xl">
                    @if (trim($q) !== '')
                        Resultados para “{{ $q }}”
                    @else
                        {{ $this->currentCategory->name ?? 'Todos los productos' }}
                    @endif
                </h1>
                <p class="mt-1 text-sm text-muted">
                    {{ trans_choice('{0} Ningún producto|{1} 1 producto|[2,*] :count productos', $this->products->total()) }}
                    @if (trim($q) !== '' && $this->currentCategory)
                        en {{ $this->currentCategory->name }}
                    @endif
                    @if (trim($q) !== '')
                        · <button type="button" wire:click="clearSearch" class="font-medium text-brand hover:underline">Quitar búsqueda</button>
                    @endif
                </p>
            </div>

            <label class="flex items-center gap-2 text-sm text-muted">
                Ordenar por
                <select wire:model.live="sort" class="rounded-pill border border-line bg-surface px-3 py-1.5 text-sm text-ink outline-none focus:border-brand">
                    @foreach (\App\Modules\Catalog\Livewire\ProductCatalog::SORT_OPTIONS as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <div class="relative">
            <div wire:loading.delay class="absolute inset-0 z-10 rounded-card bg-canvas/60"></div>

            @if ($this->products->isEmpty())
                <x-ui.card class="flex flex-col items-center gap-3 py-16 text-center">
                    <p class="text-lg font-semibold text-ink">No encontramos productos</p>
                    <p class="text-sm text-muted">Prueba con otra búsqueda o categoría.</p>
                    <x-ui.button variant="secondary" size="sm" wire:click="resetFilters">Ver todos los productos</x-ui.button>
                </x-ui.card>
            @else
                <div class="grid grid-cols-2 gap-3 sm:gap-5 md:grid-cols-3 xl:grid-cols-4">
                    @foreach ($this->products as $product)
                        <x-catalog.product-card :product="$product" wire:key="product-{{ $product->id }}" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $this->products->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
