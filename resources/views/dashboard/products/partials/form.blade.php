{{--
    Formulario de producto (agricultor y admin).
    Variables: $action, $product (null al crear), $categories, $cancelUrl, $multipleImages.
--}}
@php
    $product ??= null;

    $suggestedUnits = ['kg', 'g', 'lb', 'unidad', 'docena', 'saco', 'caja', 'atado', 'manojo', 'arroba', 'litro', 'ml'];

    // Filas unidad/stock/precio: lo enviado (si falló la validación), las del producto o una vacía
    $rows = old('unit') !== null
        ? collect(old('unit'))->values()->map(fn ($unit, $index) => ['unit' => $unit, 'stock' => old('stock')[$index] ?? '', 'price' => old('price')[$index] ?? ''])
        : ($product?->units->isNotEmpty()
            ? $product->units->map(fn ($unit) => ['unit' => $unit->unit, 'stock' => $unit->stock, 'price' => $unit->price])
            : collect([['unit' => 'kg', 'stock' => '', 'price' => '']]));

    $unitErrors = collect($errors->getMessages())->filter(fn ($messages, $key) => preg_match('/^(unit|stock|price)(\.|$)/', $key))->flatten()->unique();
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
    @csrf
    @if ($product)
        @method('PUT')
    @endif

    <x-ui.card class="flex flex-col gap-4">
        <h2 class="text-lg font-semibold text-ink">Información</h2>
        <x-ui.input name="name" label="Nombre del producto" :value="$product?->name" placeholder="Ej. Papa amarilla" required />
        <x-ui.textarea name="description" label="Descripción" rows="3" placeholder="Origen, variedad, cómo se cultiva..." :value="$product?->description" />
        <x-ui.select name="category_id" label="Categoría" :options="$categories->pluck('name', 'id')->all()" :selected="$product?->category_id" placeholder="Selecciona una categoría" required />
    </x-ui.card>

    <x-ui.card class="flex flex-col gap-4" x-data="{ rows: {{ Js::from($rows->values()) }}, add() { this.rows.push({ unit: '', stock: '', price: '' }) } }">
        <div>
            <h2 class="text-lg font-semibold text-ink">Unidades de venta</h2>
            <p class="text-sm text-muted">Una fila por cada forma de vender el producto, con su stock y su precio (IGV incluido).</p>
        </div>

        <datalist id="unit-suggestions">
            @foreach ($suggestedUnits as $unit)
                <option value="{{ $unit }}"></option>
            @endforeach
        </datalist>

        <div class="hidden grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,1fr)_2.5rem] gap-3 text-xs font-semibold tracking-wide text-muted uppercase sm:grid">
            <span>Unidad</span><span>Stock</span><span>Precio (S/.)</span><span></span>
        </div>

        <template x-for="(row, index) in rows" x-bind:key="index">
            <div class="grid grid-cols-2 gap-3 rounded-xl border border-line p-3 sm:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,1fr)_2.5rem] sm:border-0 sm:p-0">
                <input type="text" name="unit[]" x-model="row.unit" list="unit-suggestions" placeholder="Ej. kg, saco (50 kg)" required maxlength="50" aria-label="Unidad" class="col-span-2 w-full rounded-xl border border-line bg-surface px-4 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/25 focus:outline-none sm:col-span-1">
                <input type="number" name="stock[]" x-model="row.stock" min="0" step="1" placeholder="Stock" required aria-label="Stock" class="w-full rounded-xl border border-line bg-surface px-4 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/25 focus:outline-none">
                <input type="number" name="price[]" x-model="row.price" min="0.1" step="0.01" placeholder="0.00" required aria-label="Precio" class="w-full rounded-xl border border-line bg-surface px-4 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/25 focus:outline-none">
                <button type="button" x-on:click="rows.splice(index, 1)" x-bind:disabled="rows.length === 1" class="col-span-2 rounded-lg p-2 text-muted transition hover:bg-danger/10 hover:text-danger disabled:opacity-30 sm:col-span-1" aria-label="Quitar unidad"><i class="fas fa-trash"></i></button>
            </div>
        </template>

        @foreach ($unitErrors as $message)
            <p class="text-xs text-danger">{{ $message }}</p>
        @endforeach

        <x-ui.button variant="secondary" size="sm" class="self-start" x-on:click="add()"><i class="fas fa-plus"></i> Agregar unidad</x-ui.button>
    </x-ui.card>

    <x-ui.card class="flex flex-col gap-4">
        <h2 class="text-lg font-semibold text-ink">Imágenes</h2>

        @if ($product && $multipleImages && $product->images->isNotEmpty())
            <fieldset>
                <legend class="mb-2 text-sm font-medium text-ink">Imágenes actuales (marca las que quieras eliminar)</legend>
                <div class="flex flex-wrap gap-3">
                    @foreach ($product->images as $image)
                        <label class="relative cursor-pointer overflow-hidden rounded-xl border-2 border-line has-checked:border-danger">
                            <img src="{{ Storage::url($image->image_path) }}" alt="" class="size-24 object-cover">
                            <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" class="peer sr-only">
                            <span class="absolute inset-0 hidden items-center justify-center bg-danger/60 text-white peer-checked:flex"><i class="fas fa-trash"></i></span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        @elseif ($product?->image_path)
            <img src="{{ Storage::url($product->image_path) }}" alt="Imagen actual" class="size-28 rounded-xl object-cover">
        @endif

        @if ($multipleImages)
            <div class="flex flex-col gap-1.5" x-data="{ previews: [] }">
                <label for="images" class="text-sm font-medium text-ink">{{ $product ? 'Agregar imágenes' : 'Imágenes' }}</label>
                <input type="file" id="images" name="image[]" accept="image/*" multiple x-on:change="previews = [...$event.target.files].map((file) => URL.createObjectURL(file))" class="text-sm text-muted file:mr-3 file:rounded-pill file:border-0 file:bg-brand-soft file:px-4 file:py-2 file:font-medium file:text-brand">
                <p class="text-xs text-muted">JPG, PNG o WebP de hasta 2 MB cada una. La primera será la principal.</p>
                <div class="flex flex-wrap gap-2" x-show="previews.length">
                    <template x-for="src in previews"><img x-bind:src="src" alt="" class="size-20 rounded-lg object-cover"></template>
                </div>
                @foreach (collect($errors->getMessages())->filter(fn ($messages, $key) => str_starts_with($key, 'image'))->flatten() as $message)
                    <p class="text-xs text-danger">{{ $message }}</p>
                @endforeach
            </div>
        @else
            <x-ui.image-upload name="image" :label="$product ? 'Reemplazar imagen' : 'Imagen'" hint="JPG, PNG o WebP de hasta 2 MB" />
        @endif
    </x-ui.card>

    <div class="flex justify-end gap-3">
        <x-ui.button variant="secondary" :href="$cancelUrl">Cancelar</x-ui.button>
        <x-ui.button type="submit">{{ $product ? 'Guardar cambios' : 'Publicar producto' }}</x-ui.button>
    </div>
</form>
