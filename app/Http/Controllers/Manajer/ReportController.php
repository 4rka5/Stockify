<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\StockTransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $productRepository;
    protected $categoryRepository;
    protected $stockTransactionRepository;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        StockTransactionRepository $stockTransactionRepository
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->stockTransactionRepository = $stockTransactionRepository;
    }

    /**
     * Display reports for Manajer
     */
    public function index(Request $request)
    {
        // Prioritas: Manual date filter > Quick filter > Default
        $filter = $request->input('filter');
        $hasManualDate = $request->has('start_date') || $request->has('end_date');
        $category = $request->input('category');

        if ($hasManualDate) {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        } elseif ($filter) {
            switch ($filter) {
                case 'today':
                    $startDate = Carbon::today()->format('Y-m-d');
                    $endDate = Carbon::today()->format('Y-m-d');
                    break;
                case 'week':
                    $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                    $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                    $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                    break;
                case 'year':
                    $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                    $endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                    break;
                default:
                    $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                    $endDate = Carbon::now()->format('Y-m-d');
            }
        } else {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
        }

        // Get products
        $productsQuery = $this->productRepository->query()->with('stockTransactions', 'category')
            ->where('status', 'approved');

        if ($category) {
            $productsQuery->where('category_id', $category);
        }

        $products = $productsQuery->get();

        // Stock Statistics
        $totalStock = $products->sum(function($product) {
            return $product->current_stock;
        });

        $lowStockCount = $products->filter(function($product) {
            return $product->current_stock <= $product->minimum_stock && $product->current_stock > 0;
        })->count();

        $outOfStockCount = $products->filter(function($product) {
            return $product->current_stock == 0;
        })->count();

        // Transaction Statistics (filtered by date)
        $transactionsQuery = $this->stockTransactionRepository->query()
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->with('product');

        if ($category) {
            $transactionsQuery->whereHas('product', function($q) use ($category) {
                $q->where('category_id', $category);
            });
        }

        $transactions = $transactionsQuery->get();

        $incomingCount = $transactions->where('type', 'in')
            ->whereIn('status', ['diterima'])->count();
        $outgoingCount = $transactions->where('type', 'out')
            ->whereIn('status', ['dikeluarkan'])->count();

        $totalIncoming = $transactions->where('type', 'in')
            ->whereIn('status', ['diterima'])->sum('quantity');
        $totalOutgoing = $transactions->where('type', 'out')
            ->whereIn('status', ['dikeluarkan'])->sum('quantity');

        // Category Distribution
        $categoryStats = $this->categoryRepository->query()
            ->withCount('products')
            ->get()
            ->map(function($cat) use ($products) {
                $categoryProducts = $products->where('category_id', $cat->id);
                return [
                    'name' => $cat->name,
                    'product_count' => $categoryProducts->count(),
                    'total_stock' => $categoryProducts->sum(function($p) {
                        return $p->current_stock;
                    }),
                ];
            });

        // Recent Transactions
        $recentTransactions = $this->stockTransactionRepository->query()
            ->with(['product', 'user'])
            ->whereBetween('date', [$startDate, $endDate])
            ->latest('date')
            ->take(10)
            ->get();

        // Categories for filter
        $categories = $this->categoryRepository->getAll();

        return view('manajer.reports.index', compact(
            'totalStock',
            'lowStockCount',
            'outOfStockCount',
            'incomingCount',
            'outgoingCount',
            'totalIncoming',
            'totalOutgoing',
            'categoryStats',
            'recentTransactions',
            'startDate',
            'endDate',
            'categories'
        ));
    }
}
