<div class="relative min-w-0 flex-1 md:max-w-md xl:w-96 xl:flex-none" x-data="{ open: false }" x-on:click.outside="open = false" x-on:keydown.escape="open = false">
    <form action="{{ route('search') }}" method="GET" role="search" class="flex items-center rounded-pill border border-line bg-canvas py-1 pr-1 pl-1 transition focus-within:border-brand">
        <label for="searchCategory" class="sr-only">Categoría</label>
        <select name="category" id="searchCategory" wire:model="category" class="hidden max-w-24 cursor-pointer truncate border-r border-line bg-transparent px-2 py-1.5 text-xs text-ink outline-none min-[400px]:block sm:max-w-40 sm:text-sm">
            <option value="all">Todas las Categorías</option>
            @foreach ($this->categories as $categoryOption)
                <option value="{{ $categoryOption->slug }}" @selected($categoryOption->slug === $category)>{{ $categoryOption->name }}</option>
            @endforeach
        </select>

        <label for="searchInput" class="sr-only">Buscar productos</label>
        <input
            type="search"
            name="q"
            id="searchInput"
            value="{{ $q }}"
            wire:model.live.debounce.300ms="q"
            x-on:focus="open = true"
            x-on:input="open = true"
            placeholder="Buscar productos..."
            autocomplete="off"
            class="min-w-0 flex-1 bg-transparent px-3 py-1.5 text-base text-ink outline-none placeholder:text-muted sm:text-sm [&::-webkit-search-cancel-button]:hidden"
        >

        <button type="submit" class="flex size-8 shrink-0 items-center justify-center rounded-full bg-brand text-white transition hover:bg-brand-strong" aria-label="Buscar">
            <svg class="size-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        </button>
    </form>

    @if ($this->suggestions->isNotEmpty())
        <ul x-cloak x-show="open" x-transition.opacity class="absolute inset-x-0 top-[calc(100%+6px)] z-[1000] max-h-96 overflow-y-auto rounded-xl border border-line bg-surface py-2 shadow-lifted">
            @foreach ($this->suggestions as $suggestion)
                <li wire:key="suggestion-{{ $suggestion->id }}">
                    <a href="{{ route('products.show', $suggestion->slug) }}" class="flex items-center gap-3 px-4 py-2 transition hover:bg-brand-soft">
                        <x-catalog.product-image :product="$suggestion" class="size-11 shrink-0 rounded-md" />
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium text-ink">{{ $suggestion->name }}</span>
                            <span class="text-xs font-semibold text-brand">{{ \App\Modules\Shared\Money::fromDecimal($suggestion->price)->format() }} / {{ $suggestion->unit }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
            <li class="border-t border-line">
                <a href="{{ route('search', ['q' => $q, 'category' => $category]) }}" class="block px-4 py-2 text-sm font-medium text-brand hover:underline">Ver todos los resultados</a>
            </li>
        </ul>
    @endif
</div>
