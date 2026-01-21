<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $products = Product::all();

        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => $products,
        ]);
    }


    public function salesStats(Request $request)
    {
        $period = $request->query('period', 'day');

        $query = SalesTransaction::query();

        // Filter based on period
        switch ($period) {
            case 'day':
                $query->whereDate('created_at', today());
                $groupFormat = '%H:%i'; // Hour:Minute
                break;
            case 'week':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                $groupFormat = '%Y-%m-%d';
                break;
            case 'month':
                $query->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month);
                $groupFormat = '%Y-%m-%d';
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                $groupFormat = '%Y-%m';
                break;
            default:
                return response()->json([
                    'message' => 'Invalid period',
                ], 400);
        }

        $stats = $query->select(
            DB::raw("DATE_FORMAT(created_at, '{$groupFormat}') as time_period"),
            DB::raw('COUNT(*) as transaction_count'),
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(total_price) as total_revenue')
        )
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '{$groupFormat}')"))
            ->orderBy('time_period')
            ->get();


        $topProductsQuery = SalesTransaction::query();


        switch ($period) {
            case 'day':
                $topProductsQuery->whereDate('created_at', today());
                break;
            case 'week':
                $topProductsQuery->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'month':
                $topProductsQuery->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month);
                break;
            case 'year':
                $topProductsQuery->whereYear('created_at', now()->year);
                break;
        }

        $topProducts = $topProductsQuery->with('product:id,name,price,photo')
            ->select(
                'product_id',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return response()->json([
            'message' => 'Sales statistics retrieved successfully',
            'period' => $period,
            'data' => [
                'stats' => $stats,
                'top_products' => $topProducts,
                'total_transactions' => $stats->sum('transaction_count'),
                'total_quantity' => $stats->sum('total_quantity'),
                'total_revenue' => $stats->sum('total_revenue'),
            ],
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('products', 'public');
            $validated['photo'] = $photoPath;
        }

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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('products', 'public');
            $validated['photo'] = $photoPath;
        }

        $product->update($validated);

        return response()->json([
            'message' => 'Produk berhasil diperbarui',
            'data' => $product,
        ]);
    }

    /**
     * Delete product (Admin only)
     */
    public function destroy(Product $product)
    {
        $productName = $product->name;

        // Delete associated sales transactions
        $product->salesTransactions()->delete();

        // Delete the product
        $product->delete();

        return response()->json([
            'message' => "Produk '{$productName}' berhasil dihapus",
        ]);
    }
}
