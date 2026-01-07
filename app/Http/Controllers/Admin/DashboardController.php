<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\UserService;
use App\Services\StockTransactionService;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $productService;
    protected $userService;
    protected $stockTransactionService;
    protected $categoryService;

    public function __construct(
        ProductService $productService,
        UserService $userService,
        StockTransactionService $stockTransactionService,
        CategoryService $categoryService
    ) {
        $this->productService = $productService;
        $this->userService = $userService;
        $this->stockTransactionService = $stockTransactionService;
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $totalProducts = $this->productService->getApprovedProducts()->count();
        $totalUsers = $this->userService->getAllUsers()->count();
        $totalCategories = $this->categoryService->getAllCategories()->count();

        // Transaksi bulan ini
        $transactionsIn = $this->stockTransactionService->getMonthlyTransactionsByType('in');
        $transactionsOut = $this->stockTransactionService->getMonthlyTransactionsByType('out');

        // Total transaksi bulan ini
        $totalTransactionsIn = $transactionsIn->sum('quantity');
        $totalTransactionsOut = $transactionsOut->sum('quantity');

        $lowStockProducts = $this->productService->getLowStockProducts();
        $todayTransactions = $this->stockTransactionService->getTodayTransactions();
        $pendingTransactions = $this->stockTransactionService->getPendingTransactions();

        // Top 5 produk dengan stok terbanyak untuk grafik
        $topStockProducts = $this->productService->getTopStockProducts(5);

        // Aktivitas pengguna terbaru (10 transaksi terakhir)
        $recentActivities = $this->stockTransactionService->getRecentActivities(10);

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalUsers',
            'totalCategories',
            'totalTransactionsIn',
            'totalTransactionsOut',
            'lowStockProducts',
            'todayTransactions',
            'pendingTransactions',
            'topStockProducts',
            'recentActivities'
        ));
    }
}
