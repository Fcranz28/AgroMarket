{{--
    Tabla de productos de los paneles.
    Variables: $products, $editRoute, $showRoute, $destroyRoute (null para ocultar "Eliminar"), $showOwner (opcional).
--}}
@php
    use App\Modules\Shared\Money;

    $showOwner ??= false;
@endphp

@if ($products->isEmpty())
    <div class="flex flex-col items-center gap-3 px-5 py-14 text-center">
        <i class="fas fa-box-open text-4xl text-brand"></i>
        <p class="text-muted">Aún no hay productos.</p>
    </div>
@else
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="border-b border-line text-xs tracking-wide text-muted uppercase">
                <tr>
                    <th scope="col" class="px-5 py-3 font-semibold">Producto</th>
                    @if ($showOwner)
                        <th scope="col" class="px-5 py-3 font-semibold">Agricultor</th>
                    @endif
                    <th scope="col" class="px-5 py-3 font-semibold">Precio desde</th>
                    <th scope="col" class="px-5 py-3 font-semibold">Stock</th>
                    <th scope="col" class="px-5 py-3 font-semibold">Estado</th>
                    <th scope="col" class="px-5 py-3 text-right font-semibold">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @foreach ($products as $product)
                    <tr class="transition hover:bg-canvas/60">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <x-catalog.product-image :product="$product" class="size-11 shrink-0 rounded-lg" />
                                <div class="min-w-0">
                                    <p class="truncate font-medium text-ink">{{ $product->name }}</p>
                                    @if ($product->relationLoaded('category') && $product->category)
                                        <p class="text-xs text-muted">{{ $product->category->name }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        @if ($showOwner)
                            <td class="px-5 py-3 text-muted">{{ $product->user?->name ?? '—' }}</td>
                        @endif
                        <td class="px-5 py-3 text-ink">{{ Money::fromDecimal($product->price)->format() }} <span class="text-muted">/ {{ $product->unit }}</span></td>
                        <td class="px-5 py-3 text-ink">{{ $product->stock }}</td>
                        <td class="px-5 py-3">
                            <x-ui.badge :color="$product->stock > 0 ? 'success' : 'danger'">{{ $product->stock > 0 ? 'Disponible' : 'Agotado' }}</x-ui.badge>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route($showRoute, $product) }}" class="rounded-lg p-2 text-muted transition hover:bg-brand-soft hover:text-brand" title="Ver" aria-label="Ver {{ $product->name }}"><i class="fas fa-eye"></i></a>
                                <a href="{{ route($editRoute, $product) }}" class="rounded-lg p-2 text-muted transition hover:bg-brand-soft hover:text-brand" title="Editar" aria-label="Editar {{ $product->name }}"><i class="fas fa-pen"></i></a>
                                @if ($destroyRoute)
                                    <form method="POST" action="{{ route($destroyRoute, $product) }}" x-data x-on:submit.prevent="Swal.fire({ title: '¿Eliminar producto?', text: @js('«'.$product->name.'» dejará de mostrarse en la tienda.'), icon: 'warning', showCancelButton: true, confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar', confirmButtonColor: '#c53030' }).then((result) => result.isConfirmed && $el.submit())">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg p-2 text-muted transition hover:bg-danger/10 hover:text-danger" title="Eliminar" aria-label="Eliminar {{ $product->name }}"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if (method_exists($products, 'links') && $products->hasPages())
        <div class="border-t border-line px-5 py-4">{{ $products->links() }}</div>
    @endif
@endif
