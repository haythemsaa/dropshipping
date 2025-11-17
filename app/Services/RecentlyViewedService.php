<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class RecentlyViewedService
{
    protected const SESSION_KEY = 'recently_viewed_products';
    protected const MAX_ITEMS = 8;

    /**
     * Add a product to recently viewed list
     */
    public function addProduct(int $productId): void
    {
        $viewed = $this->getProductIds();

        // Remove if already exists
        $viewed = array_filter($viewed, fn($id) => $id !== $productId);

        // Add to beginning
        array_unshift($viewed, $productId);

        // Limit to max items
        $viewed = array_slice($viewed, 0, self::MAX_ITEMS);

        Session::put(self::SESSION_KEY, $viewed);
    }

    /**
     * Get recently viewed product IDs
     */
    public function getProductIds(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    /**
     * Get recently viewed products
     */
    public function getProducts(int $limit = 8): \Illuminate\Database\Eloquent\Collection
    {
        $productIds = $this->getProductIds();

        if (empty($productIds)) {
            return collect();
        }

        // Get products and maintain order
        $products = Product::with(['images', 'supplier'])
            ->whereIn('id', $productIds)
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($product) use ($productIds) {
                return array_search($product->id, $productIds);
            })
            ->take($limit);

        return $products;
    }

    /**
     * Clear recently viewed products
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }
}
