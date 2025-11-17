<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockAlertController extends Controller
{
    /**
     * Subscribe to stock alert
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Check if product/variant is actually out of stock
        if ($request->variant_id) {
            $variant = ProductVariant::findOrFail($request->variant_id);
            if ($variant->stock_quantity > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce produit est actuellement en stock'
                ], 400);
            }
        } else {
            $product = Product::findOrFail($request->product_id);
            if ($product->getTotalStock() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce produit est actuellement en stock'
                ], 400);
            }
        }

        // Check if alert already exists
        $existing = StockAlert::where('product_id', $request->product_id)
            ->where('email', $request->email)
            ->when($request->variant_id, fn($q) => $q->where('variant_id', $request->variant_id))
            ->where('is_notified', false)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Vous êtes déjà inscrit pour recevoir une alerte pour ce produit'
            ], 400);
        }

        // Create stock alert
        StockAlert::create([
            'product_id' => $request->product_id,
            'variant_id' => $request->variant_id,
            'user_id' => auth()->id(),
            'email' => $request->email,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vous serez notifié par email dès que ce produit sera de nouveau disponible!'
        ]);
    }
}
