<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil
     */
    public function index()
    {
        // Récupérer les catégories principales (sans parent)
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->take(6)
            ->get();

        // Récupérer les produits vedettes actifs
        $featuredProducts = Product::with(['supplier', 'images', 'category'])
            ->where('status', 'active')
            ->where('is_featured', true)
            ->whereNotNull('approved_at')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Récupérer les nouveaux produits
        $newProducts = Product::with(['supplier', 'images', 'category'])
            ->where('status', 'active')
            ->whereNotNull('approved_at')
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();

        return view('home', compact('categories', 'featuredProducts', 'newProducts'));
    }
}
