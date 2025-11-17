<?php

namespace App\View\Components;

use App\Services\RecentlyViewedService;
use Illuminate\View\Component;

class RecentlyViewedProducts extends Component
{
    public $products;

    /**
     * Create a new component instance.
     */
    public function __construct(RecentlyViewedService $recentlyViewedService)
    {
        $this->products = $recentlyViewedService->getProducts(8);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        // Only render if we have products
        if ($this->products->isEmpty()) {
            return '';
        }

        return view('components.recently-viewed-products');
    }
}
