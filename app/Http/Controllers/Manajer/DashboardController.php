<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\StockTransactionService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $productService;
    protected $stockTransactionService;

    public function __construct(
        ProductService $productService,
        StockTransactionService $stockTransactionService
    ) {
        $this->productService = $productService;
        $this->stockTransactionService = $stockTransactionService;
    }

    public function index()
    {
        $lowStockProducts = $this->productService->getLowStockProducts();
        $todayIncoming = $this->stockTransactionService->getTodayTransactions()
            ->where('type', 'in', 'approved');
        $todayOutgoing = $this->stockTransactionService->getTodayTransactions()
            ->where('type', 'out', 'approved');
        $pendingTransactions = $this->stockTransactionService->getPendingTransactions();

        // Get pending stock opname
        $pendingOpnames = \App\Models\StockOpname::with(['product', 'user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('manajer.dashboard', compact(
            'lowStockProducts',
            'todayIncoming',
            'todayOutgoing',
            'pendingTransactions',
            'pendingOpnames'
        ));
    }
}
