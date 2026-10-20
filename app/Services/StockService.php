<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\ProductUnit;

class StockService
{
    /**
     * Take stock for the given lines. Must run inside a database transaction.
     *
     * Products with units keep their stock per unit; products.stock is kept as the sum of
     * the units so the catalog stays consistent.
     *
     * @param  iterable<array{product_id: int, product_unit_id: int|null, quantity: int}>  $lines
     *
     * @throws InsufficientStockException
     */
    public function reserve(iterable $lines): void
    {
        foreach ($lines as $line) {
            $product = Product::whereKey($line['product_id'])->lockForUpdate()->firstOrFail();

            if ($line['product_unit_id'] === null) {
                $this->ensureAvailable($product->stock, $line['quantity'], $product->name);
                $product->decrement('stock', $line['quantity']);

                continue;
            }

            $unit = ProductUnit::whereKey($line['product_unit_id'])->lockForUpdate()->firstOrFail();

            $this->ensureAvailable($unit->stock, $line['quantity'], "{$product->name} ({$unit->unit})");
            $unit->decrement('stock', $line['quantity']);
            $this->syncProductStock($product);
        }
    }

    /**
     * Give back the stock of the given lines (e.g. when an order is cancelled).
     *
     * @param  iterable<array{product_id: int, product_unit_id: int|null, quantity: int}>  $lines
     */
    public function release(iterable $lines): void
    {
        foreach ($lines as $line) {
            $product = Product::whereKey($line['product_id'])->lockForUpdate()->first();

            if (! $product) {
                continue;
            }

            $unit = $line['product_unit_id'] !== null
                ? ProductUnit::whereKey($line['product_unit_id'])->lockForUpdate()->first()
                : null;

            if ($unit) {
                $unit->increment('stock', $line['quantity']);
                $this->syncProductStock($product);
            } else {
                $product->increment('stock', $line['quantity']);
            }
        }
    }

    private function syncProductStock(Product $product): void
    {
        $product->update(['stock' => $product->units()->sum('stock')]);
    }

    /**
     * @throws InsufficientStockException
     */
    private function ensureAvailable(int|string|null $available, int $requested, string $name): void
    {
        if ((int) $available < $requested) {
            throw new InsufficientStockException("Stock insuficiente para el producto: {$name}.");
        }
    }
}
