<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\StockTransactionService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
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

    public function in()
    {
        $products = $this->productService->getAllProducts();
        $pendingTransactions = $this->stockTransactionService->getPendingTransactions()
            ->where('type', 'in');

        return view('staff.stock.in', compact('products', 'pendingTransactions'));
    }

    public function out()
    {
        $products = $this->productService->getAllProducts();
        $pendingTransactions = $this->stockTransactionService->getPendingTransactions()
            ->where('type', 'out');

        return view('staff.stock.out', compact('products', 'pendingTransactions'));
    }

    public function check()
    {
        $products = $this->productService->getAllProducts();
        return view('staff.stock.check', compact('products'));
    }

    public function storeIn(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $validated['type'] = 'in';
        $validated['user_id'] = Auth::id();
        $validated['date'] = now();
        $validated['status'] = 'pending';

        try {
            $this->stockTransactionService->createTransaction($validated);
            return back()->with('success', 'Barang masuk berhasil dicatat, menunggu persetujuan manajer');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mencatat barang masuk: ' . $e->getMessage());
        }
    }

    public function storeOut(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $validated['type'] = 'out';
        $validated['user_id'] = Auth::id();
        $validated['date'] = now();
        $validated['status'] = 'pending';

        try {
            $this->stockTransactionService->createTransaction($validated);
            return back()->with('success', 'Barang keluar berhasil dicatat, menunggu persetujuan manajer');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mencatat barang keluar: ' . $e->getMessage());
        }
    }
}
