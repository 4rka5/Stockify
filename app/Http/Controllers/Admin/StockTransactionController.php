<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StockTransactionService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class StockTransactionController extends Controller
{
    protected $stockTransactionService;
    protected $productService;

    public function __construct(
        StockTransactionService $stockTransactionService,
        ProductService $productService
    ) {
        $this->stockTransactionService = $stockTransactionService;
        $this->productService = $productService;
    }

    public function index()
    {
        $transactions = $this->stockTransactionService->paginateTransactions(15);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = $this->stockTransactionService->getTransactionById($id);
        return view('admin.transactions.show', compact('transaction'));
    }
}
