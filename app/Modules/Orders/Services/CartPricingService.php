<?php

namespace App\Modules\Orders\Services;

use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\ProductUnit;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CartPricingService
{
    /**
     * Price a cart using server-side product data only. Prices already include IGV.
     *
     * Each line is a product in a unit. Lines without a unit use the product's cheapest
     * unit (the price shown in the catalog), or the product itself for legacy products
     * without units. Repeated lines are merged.
     *
     * @param  array<int, array{id: int|string, unit_id?: int|string|null, cantidad: int|string}>  $cart
     * @return array{lines: Collection<int, array{product_id: int, product_unit_id: int|null, unit: string, price: string, quantity: int}>, total_cents: int, hash: string}
     *
     * @throws ValidationException
     */
    public function price(array $cart): array
    {
        $products = Product::query()
            ->with('units')
            ->whereIn('id', collect($cart)->pluck('id')->map(fn ($id) => (int) $id))
            ->get()
            ->keyBy('id');

        $merged = [];

        foreach ($cart as $item) {
            $line = $this->resolveUnit($products[(int) $item['id']], $item['unit_id'] ?? null);
            $key = "{$line['product_id']}:{$line['product_unit_id']}";

            $merged[$key] ??= [...$line, 'quantity' => 0];
            $merged[$key]['quantity'] += (int) $item['cantidad'];
        }

        ksort($merged);

        $totalCents = 0;
        $hashSource = [];

        foreach ($merged as $line) {
            $totalCents += $this->toCents($line['price']) * $line['quantity'];
            $hashSource[] = [$line['product_id'], $line['product_unit_id'], $line['quantity']];
        }

        return [
            'lines' => collect(array_values($merged)),
            'total_cents' => $totalCents,
            'hash' => hash('sha256', (string) json_encode($hashSource)),
        ];
    }

    /**
     * @return array{product_id: int, product_unit_id: int|null, unit: string, price: string}
     *
     * @throws ValidationException
     */
    private function resolveUnit(Product $product, int|string|null $unitId): array
    {
        $unit = filled($unitId)
            ? $product->units->firstWhere('id', (int) $unitId)
            : $product->units->sortBy(fn (ProductUnit $unit) => (float) $unit->price)->first();

        if (filled($unitId) && ! $unit) {
            throw ValidationException::withMessages([
                'cart' => "La unidad seleccionada para {$product->name} ya no está disponible.",
            ]);
        }

        return [
            'product_id' => $product->id,
            'product_unit_id' => $unit?->id,
            'unit' => $unit->unit ?? $product->unit,
            'price' => (string) ($unit->price ?? $product->price),
        ];
    }

    private function toCents(string $price): int
    {
        return (int) round(((float) $price) * 100);
    }
}
