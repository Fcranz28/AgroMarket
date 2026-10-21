<?php

namespace App\Modules\Orders\Services;

use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\ProductUnit;
use App\Modules\Shared\Money;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

/**
 * Shopping cart kept in the session. It only stores ids and quantities: names, images and
 * prices are always read from the catalog, so the cart never shows stale prices.
 *
 * A line is a product in a unit (key "productId:unitId"); the same product can be in the
 * cart in several units.
 */
class SessionCart
{
    private const SESSION_KEY = 'cart';

    public const MAX_QUANTITY = 99;

    public function __construct(private Session $session) {}

    /**
     * Add a product to the cart. Without a unit, the cheapest unit is used (the price shown
     * in the catalog). The quantity is capped by the available stock.
     *
     * @return array{added: bool, message: string}
     */
    public function add(int $productId, ?int $unitId = null, int $quantity = 1): array
    {
        $product = Product::query()->with('units')->find($productId);
        $unit = $product ? $this->resolveUnit($product, $unitId) : null;

        if (! $product || ($unitId !== null && ! $unit)) {
            return ['added' => false, 'message' => 'Este producto ya no está disponible.'];
        }

        $stock = $unit->stock ?? $product->stock;
        $key = $this->key($product->id, $unit?->id);
        $items = $this->items();
        $current = $items[$key]['quantity'] ?? 0;
        $newQuantity = min($current + max($quantity, 1), $stock, self::MAX_QUANTITY);

        if ($newQuantity <= $current) {
            return ['added' => false, 'message' => "No hay más stock de {$product->name}."];
        }

        $items[$key] = ['product_id' => $product->id, 'unit_id' => $unit?->id, 'quantity' => $newQuantity];
        $this->save($items);

        return ['added' => true, 'message' => "{$product->name} se agregó al carrito."];
    }

    /**
     * Change the quantity of a line, capped by the available stock; zero or less removes it.
     */
    public function update(string $key, int $quantity): void
    {
        $items = $this->items();

        if (! isset($items[$key])) {
            return;
        }

        $line = $this->summary()['lines']->firstWhere('key', $key);

        if (! $line || $quantity <= 0) {
            $this->remove($key);

            return;
        }

        $items[$key]['quantity'] = min($quantity, $line['stock'], self::MAX_QUANTITY);
        $this->save($items);
    }

    public function remove(string $key): void
    {
        $items = $this->items();
        unset($items[$key]);
        $this->save($items);
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    /**
     * Number of units in the cart (what the cart badge shows).
     */
    public function count(): int
    {
        return array_sum(array_column($this->items(), 'quantity'));
    }

    public function isEmpty(): bool
    {
        return $this->items() === [];
    }

    /**
     * The cart in the format the checkout and the payment endpoints expect.
     *
     * @return list<array{id: int, unit_id: int|null, cantidad: int}>
     */
    public function toPaymentPayload(): array
    {
        return array_values(array_map(fn (array $item) => [
            'id' => $item['product_id'],
            'unit_id' => $item['unit_id'],
            'cantidad' => $item['quantity'],
        ], $this->items()));
    }

    /**
     * Lines with the current catalog data. Lines whose product or unit no longer exists are
     * dropped from the cart.
     *
     * @return array{lines: Collection<int, array{key: string, product: Product, unit: string, price: Money, quantity: int, stock: int, subtotal: Money}>, total: Money}
     */
    public function summary(): array
    {
        $items = $this->items();
        $products = Product::query()
            ->with('units')
            ->whereIn('id', array_column($items, 'product_id'))
            ->get()
            ->keyBy('id');

        $lines = collect();

        foreach ($items as $key => $item) {
            $product = $products->get($item['product_id']);
            $unit = $product && $item['unit_id'] !== null ? $product->units->firstWhere('id', $item['unit_id']) : null;

            if (! $product || ($item['unit_id'] !== null && ! $unit)) {
                unset($items[$key]);

                continue;
            }

            $price = Money::fromDecimal((string) ($unit->price ?? $product->price));

            $lines->push([
                'key' => $key,
                'product' => $product,
                'unit' => $unit->unit ?? $product->unit,
                'price' => $price,
                'quantity' => $item['quantity'],
                'stock' => $unit->stock ?? $product->stock,
                'subtotal' => $price->multiply($item['quantity']),
            ]);
        }

        if (count($items) !== count($this->items())) {
            $this->save($items);
        }

        return [
            'lines' => $lines,
            'total' => $lines->reduce(fn (Money $total, array $line) => $total->add($line['subtotal']), Money::zero()),
        ];
    }

    /**
     * @return array<string, array{product_id: int, unit_id: int|null, quantity: int}>
     */
    private function items(): array
    {
        return $this->session->get(self::SESSION_KEY, []);
    }

    /**
     * @param  array<string, array{product_id: int, unit_id: int|null, quantity: int}>  $items
     */
    private function save(array $items): void
    {
        ksort($items);

        $this->session->put(self::SESSION_KEY, $items);
    }

    private function resolveUnit(Product $product, ?int $unitId): ?ProductUnit
    {
        return $unitId !== null
            ? $product->units->firstWhere('id', $unitId)
            : $product->units->sortBy(fn (ProductUnit $unit) => (float) $unit->price)->first();
    }

    private function key(int $productId, ?int $unitId): string
    {
        return "{$productId}:{$unitId}";
    }
}
