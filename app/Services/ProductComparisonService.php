<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class ProductComparisonService
{
    protected const SESSION_KEY = 'product_comparison';
    protected const MAX_ITEMS = 4; // Maximum products to compare

    /**
     * Add a product to comparison list
     */
    public function addProduct(int $productId): bool
    {
        $productIds = $this->getProductIds();

        // Check if already in comparison
        if (in_array($productId, $productIds)) {
            return false;
        }

        // Check max limit
        if (count($productIds) >= self::MAX_ITEMS) {
            return false;
        }

        $productIds[] = $productId;
        Session::put(self::SESSION_KEY, $productIds);

        return true;
    }

    /**
     * Remove a product from comparison list
     */
    public function removeProduct(int $productId): void
    {
        $productIds = $this->getProductIds();
        $productIds = array_filter($productIds, fn($id) => $id !== $productId);
        Session::put(self::SESSION_KEY, array_values($productIds));
    }

    /**
     * Get comparison product IDs
     */
    public function getProductIds(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    /**
     * Get comparison products
     */
    public function getProducts(): \Illuminate\Database\Eloquent\Collection
    {
        $productIds = $this->getProductIds();

        if (empty($productIds)) {
            return collect();
        }

        // Get products and maintain order
        $products = Product::with(['images', 'supplier', 'category', 'variants'])
            ->whereIn('id', $productIds)
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($product) use ($productIds) {
                return array_search($product->id, $productIds);
            });

        return $products;
    }

    /**
     * Get count of products in comparison
     */
    public function getCount(): int
    {
        return count($this->getProductIds());
    }

    /**
     * Check if product is in comparison
     */
    public function hasProduct(int $productId): bool
    {
        return in_array($productId, $this->getProductIds());
    }

    /**
     * Clear all products from comparison
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Check if comparison list is full
     */
    public function isFull(): bool
    {
        return count($this->getProductIds()) >= self::MAX_ITEMS;
    }

    /**
     * Get max items allowed
     */
    public function getMaxItems(): int
    {
        return self::MAX_ITEMS;
    }
}
