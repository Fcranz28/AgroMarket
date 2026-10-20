<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartPricingService
{
    /**
     * Price a cart using server-side product data only.
     *
     * Duplicate products are merged so the same product cannot be priced twice.
     *
     * @param  array<int, array{id: int|string, cantidad: int|string}>  $cart
     * @return array{lines: Collection<int, array{product_id: int, quantity: int, price: string}>, total_cents: int, hash: string}
     */
    public function price(array $cart): array
    {
        $quantities = collect($cart)
            ->groupBy(fn (array $item) => (int) $item['id'])
            ->map(fn (Collection $items) => $items->sum(fn (array $item) => (int) $item['cantidad']))
            ->sortKeys();

        $products = Product::query()->whereIn('id', $quantities->keys())->get()->keyBy('id');

        $lines = $quantities->map(fn (int $quantity, int $productId) => [
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => (string) $products[$productId]->price,
        ])->values();

        $totalCents = $lines->sum(fn (array $line) => $this->toCents($line['price']) * $line['quantity']);

        return [
            'lines' => $lines,
            'total_cents' => $totalCents,
            'hash' => hash('sha256', $quantities->toJson()),
        ];
    }

    private function toCents(string $price): int
    {
        return (int) round(((float) $price) * 100);
    }
}
