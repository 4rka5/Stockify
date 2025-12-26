<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SupplierRepository;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected $productRepository;
    protected $categoryRepository;
    protected $supplierRepository;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        SupplierRepository $supplierRepository
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->supplierRepository = $supplierRepository;
    }

    /**
     * Display stock report/summary
     */
    public function index(Request $request)
    {
        $query = $this->productRepository->query();
        $query->with(['category', 'supplier', 'stockTransactions']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by supplier
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'empty':
                    // Products with zero stock - menggunakan whereRaw yang lebih sederhana
                    $query->whereRaw('(
                        SELECT COALESCE(SUM(CASE WHEN type = "in" THEN quantity ELSE -quantity END), 0)
                        FROM stock_transactions
                        WHERE stock_transactions.product_id = products.id
                    ) = 0');
                    break;
                case 'low':
                    // Products with low stock (below or equal minimum, but not zero)
                    $query->whereRaw('(
                        SELECT COALESCE(SUM(CASE WHEN type = "in" THEN quantity ELSE -quantity END), 0)
                        FROM stock_transactions
                        WHERE stock_transactions.product_id = products.id
                    ) <= products.minimum_stock')
                    ->whereRaw('(
                        SELECT COALESCE(SUM(CASE WHEN type = "in" THEN quantity ELSE -quantity END), 0)
                        FROM stock_transactions
                        WHERE stock_transactions.product_id = products.id
                    ) > 0');
                    break;
                case 'normal':
                    // Products with normal stock (above minimum)
                    $query->whereRaw('(
                        SELECT COALESCE(SUM(CASE WHEN type = "in" THEN quantity ELSE -quantity END), 0)
                        FROM stock_transactions
                        WHERE stock_transactions.product_id = products.id
                    ) > products.minimum_stock');
                    break;
            }
        }

        $products = $query->get();
        $categories = $this->categoryRepository->getAll();
        $suppliers = $this->supplierRepository->getAll();

        return view('admin.stock.index', compact('products', 'categories', 'suppliers'));
    }
}
