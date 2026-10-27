<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class Cart
{
    private const KEY = 'crane_cart';

    public function add(Product $product, int $quantity = 1): void
    {
        $items = session(self::KEY, []);
        $items[$product->id] = min(99, ($items[$product->id] ?? 0) + $quantity);
        session([self::KEY => $items]);
    }

    public function update(int $productId, int $quantity): void
    {
        $items = session(self::KEY, []);
        if ($quantity < 1) {
            unset($items[$productId]);
        } else {
            $items[$productId] = min(99, $quantity);
        }
        session([self::KEY => $items]);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    /** @return Collection<int, array{product: Product, quantity: int, line_total: int}> */
    public function lines(): Collection
    {
        $quantities = session(self::KEY, []);
        if ($quantities === []) {
            return collect();
        }

        return Product::query()->publiclyVisible()->whereIn('id', array_keys($quantities))->get()
            ->filter(fn (Product $product) => $product->supportsDirectSale())
            ->map(fn (Product $product) => [
                'product' => $product,
                'quantity' => (int) $quantities[$product->id],
                'line_total' => $product->price_irr * (int) $quantities[$product->id],
            ])->values();
    }

    public function total(): int
    {
        return $this->lines()->sum('line_total');
    }
}
