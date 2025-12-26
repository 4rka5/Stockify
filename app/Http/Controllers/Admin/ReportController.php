<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SupplierRepository;
use App\Repositories\StockTransactionRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $productRepository;
    protected $categoryRepository;
    protected $supplierRepository;
    protected $stockTransactionRepository;
    protected $userRepository;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        SupplierRepository $supplierRepository,
        StockTransactionRepository $stockTransactionRepository,
        UserRepository $userRepository
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->supplierRepository = $supplierRepository;
        $this->stockTransactionRepository = $stockTransactionRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Display comprehensive reports dashboard
     */
    public function index(Request $request)
    {
        // Handle quick filter presets
        $filter = $request->input('filter');
        
        if ($filter) {
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
                    $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
                    $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
            }
        } else {
            // Date range filter
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        }

        // General Statistics
        $totalProducts = $this->productRepository->count();
        $totalCategories = $this->categoryRepository->count();
        $totalSuppliers = $this->supplierRepository->count();
        $totalUsers = $this->userRepository->count();

        // Stock Statistics
        $products = $this->productRepository->query()->with('stockTransactions')->get();
        $totalStock = $products->sum(function($product) {
            return $product->current_stock;
        });
        $lowStockCount = $products->filter(function($product) {
            return $product->current_stock <= $product->minimum_stock && $product->current_stock > 0;
        })->count();
        $outOfStockCount = $products->filter(function($product) {
            return $product->current_stock == 0;
        })->count();

        // Stock Value
        $totalStockValue = $products->sum(function($product) {
            return $product->current_stock * $product->purchase_price;
        });
        $potentialRevenue = $products->sum(function($product) {
            return $product->current_stock * $product->selling_price;
        });
        $potentialProfit = $potentialRevenue - $totalStockValue;

        // Transaction Statistics (filtered by date)
        $transactions = $this->stockTransactionRepository->query()
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->with('product')
            ->get();

        $incomingCount = $transactions->where('type', 'in')->filter(function($t) {
            return in_array($t->status, ['diterima']);
        })->count();
        $outgoingCount = $transactions->where('type', 'out')->filter(function($t) {
            return in_array($t->status, ['dikeluarkan']);
        })->count();
        $pendingCount = $transactions->where('status', 'pending')->count();

        $totalIncoming = $transactions->where('type', 'in')->filter(function($t) {
            return in_array($t->status, ['diterima']);
        })->sum('quantity');
        $totalOutgoing = $transactions->where('type', 'out')->filter(function($t) {
            return in_array($t->status, ['dikeluarkan']);
        })->sum('quantity');

        // Top Products by Stock Value
        $topProductsByValue = $products->sortByDesc(function($product) {
            return $product->current_stock * $product->selling_price;
        })->take(5);

        // Low Stock Products
        $lowStockProducts = $products->filter(function($product) {
            return $product->current_stock <= $product->minimum_stock;
        })->sortBy('current_stock')->take(10);

        // Category Distribution
        $categoryStats = $this->categoryRepository->query()
            ->withCount('products')
            ->get()
            ->map(function($category) use ($products) {
                $categoryProducts = $products->where('category_id', $category->id);
                return [
                    'name' => $category->name,
                    'product_count' => $categoryProducts->count(),
                    'total_stock' => $categoryProducts->sum(function($p) { return $p->current_stock; }),
                    'stock_value' => $categoryProducts->sum(function($p) {
                        return $p->current_stock * $p->purchase_price;
                    })
                ];
            });

        // Monthly Transaction Trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth()->format('Y-m-d');
            $monthEnd = $month->copy()->endOfMonth()->format('Y-m-d');

            $monthTransactions = $this->stockTransactionRepository->query()
                ->whereDate('date', '>=', $monthStart)
                ->whereDate('date', '<=', $monthEnd)
                ->get();

            $monthlyTrend[] = [
                'month' => $month->format('M Y'),
                'incoming' => $monthTransactions->where('type', 'in')->filter(function($t) {
                    return in_array($t->status, ['diterima']);
                })->sum('quantity'),
                'outgoing' => $monthTransactions->where('type', 'out')->filter(function($t) {
                    return in_array($t->status, ['dikeluarkan']);
                })->sum('quantity'),
            ];
        }

        // Recent Transactions
        $recentTransactions = $this->stockTransactionRepository->query()
            ->with(['product', 'user'])
            ->latest('date')
            ->take(10)
            ->get();

        return view('admin.reports.index', compact(
            'totalProducts',
            'totalCategories',
            'totalSuppliers',
            'totalUsers',
            'totalStock',
            'lowStockCount',
            'outOfStockCount',
            'totalStockValue',
            'potentialRevenue',
            'potentialProfit',
            'incomingCount',
            'outgoingCount',
            'pendingCount',
            'totalIncoming',
            'totalOutgoing',
            'topProductsByValue',
            'lowStockProducts',
            'categoryStats',
            'monthlyTrend',
            'recentTransactions',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export stock report
     */
    public function exportStock(Request $request)
    {
        $products = $this->productRepository->query()
            ->with(['category', 'supplier', 'stockTransactions'])
            ->get();

        $csv = [];
        $csv[] = ['SKU', 'Nama Produk', 'Kategori', 'Supplier', 'Stok', 'Minimum Stok', 'Status', 'Harga Beli', 'Nilai Stok'];

        foreach ($products as $product) {
            $currentStock = $product->current_stock;
            $status = $currentStock == 0 ? 'Habis' : ($currentStock <= $product->minimum_stock ? 'Rendah' : 'Normal');
            $stockValue = $currentStock * $product->purchase_price;

            $csv[] = [
                $product->sku,
                $product->name,
                $product->category->name ?? '-',
                $product->supplier->name ?? '-',
                $currentStock,
                $product->minimum_stock,
                $status,
                $product->purchase_price,
                $stockValue
            ];
        }

        $filename = 'laporan_stok_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        foreach ($csv as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    /**
     * Export transaction report
     */
    public function exportTransactions(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $transactions = $this->stockTransactionRepository->query()
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->with(['product', 'user'])
            ->orderBy('date', 'desc')
            ->get();

        $csv = [];
        $csv[] = ['Tanggal', 'Produk', 'SKU', 'Tipe', 'Jumlah', 'Status', 'User', 'Keterangan'];

        foreach ($transactions as $transaction) {
            $csv[] = [
                date('d-m-Y H:i', strtotime($transaction->date)),
                $transaction->product->name ?? '-',
                $transaction->product->sku ?? '-',
                ucfirst($transaction->type),
                $transaction->quantity,
                ucfirst($transaction->status),
                $transaction->user->name ?? '-',
                $transaction->notes ?? '-'
            ];
        }

        $filename = 'laporan_transaksi_' . $startDate . '_' . $endDate . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        foreach ($csv as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }
}
