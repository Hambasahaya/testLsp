<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get all products (accessible to authenticated users)
     */
    public function index(Request $request)
    {
        $products = Product::all();

        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => $products,
        ]);
    }

    /**
     * Create new product (Admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Sell product (Admin & Seller only)
     * Decreases stock and creates a transaction record
     */
    public function sell(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Check if stock is sufficient
        if ($product->stock < $validated['quantity']) {
            return response()->json([
                'message' => 'Stock tidak cukup',
                'available_stock' => $product->stock,
                'requested_quantity' => $validated['quantity'],
            ], 422);
        }

        // Decrease stock
        $product->decrement('stock', $validated['quantity']);
        $totalPrice = $product->price * $validated['quantity'];

        // Create transaction record
        $transaction = $request->user()->salesTransactions()->create([
            'product_id' => $product->id,
            'quantity' => $validated['quantity'],
            'total_price' => $totalPrice,
        ]);

        return response()->json([
            'message' => 'Penjualan berhasil',
            'data' => [
                'transaction' => $transaction,
                'product' => $product->fresh(),
            ],
        ]);
    }

    /**
     * Update product (Admin only)
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Produk berhasil diperbarui',
            'data' => $product,
        ]);
    }
}
