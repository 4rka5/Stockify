<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Build query
        $query = Product::with(['category', 'supplier', 'stockTransactions']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Stock status filter
        $allProducts = $query->orderBy('name')->get();

        if ($request->filled('stock_status')) {
            $allProducts = $allProducts->filter(function($product) use ($request) {
                $currentStock = $product->current_stock;
                switch ($request->stock_status) {
                    case 'safe':
                        return $currentStock > $product->minimum_stock;
                    case 'low':
                        return $currentStock <= $product->minimum_stock && $currentStock > 0;
                    case 'out':
                        return $currentStock <= 0;
                    default:
                        return true;
                }
            });
        }

        // Paginate manually
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $paginatedProducts = $allProducts->slice($offset, $perPage);
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedProducts,
            $allProducts->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categories = Category::all();

        // Statistics
        $allProductsForStats = Product::with('stockTransactions')->get();
        $totalProducts = $allProductsForStats->count();
        $lowStock = $allProductsForStats->filter(fn($p) => $p->current_stock <= $p->minimum_stock && $p->current_stock > 0)->count();
        $outOfStock = $allProductsForStats->filter(fn($p) => $p->current_stock <= 0)->count();

        return view('staff.products.index', compact('products', 'categories', 'totalProducts', 'lowStock', 'outOfStock'));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'supplier', 'stockTransactions', 'stockTransactions.user'])
            ->findOrFail($id);

        return view('staff.products.show', compact('product'));
    }
}
