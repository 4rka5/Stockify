<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StockTransactionService;
use App\Services\ProductService;
use App\Rules\SufficientStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function index(Request $request)
    {
        $type = $request->get('type');
        $status = $request->get('status');
        $product_id = $request->get('product_id');

        $query = \App\Models\StockTransaction::with(['product', 'user'])->orderBy('date', 'desc');

        if ($type) {
            $query->where('type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($product_id) {
            $query->where('product_id', $product_id);
        }

        $transactions = $query->paginate(15);
        $products = $this->productService->getAllProducts();

        return view('admin.stocks.index', compact('transactions', 'products'));
    }

    public function create()
    {
        $products = $this->productService->getAllProducts();
        return view('admin.stocks.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:masuk,keluar',
            'quantity' => [
                'required',
                'integer',
                'min:1',
                new SufficientStock($request->product_id, $request->type)
            ],
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        try {
            $validated['user_id'] = Auth::id();
            $validated['status'] = $request->type === 'masuk' ? 'diterima' : 'dikeluarkan';

            $this->stockTransactionService->createTransaction($validated);
            return redirect()->route('admin.stock-transactions.index')
                ->with('success', 'Transaksi stok berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $transaction = $this->stockTransactionService->getTransactionById($id);
        return view('admin.stocks.show', compact('transaction'));
    }

    public function edit($id)
    {
        $transaction = $this->stockTransactionService->getTransactionById($id);
        $products = $this->productService->getAllProducts();
        return view('admin.stocks.edit', compact('transaction', 'products'));
    }

    public function update(Request $request, $id)
    {
        // Get transaksi yang akan diupdate untuk validasi
        $transaction = $this->stockTransactionService->getTransactionById($id);
        
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:masuk,keluar',
            'quantity' => [
                'required',
                'integer',
                'min:1',
                new SufficientStock($request->product_id, $request->type)
            ],
            'date' => 'required|date',
            'status' => 'required|in:pending,diterima,dikeluarkan,ditolak',
            'notes' => 'nullable|string',
        ]);

        try {
            $this->stockTransactionService->updateTransaction($id, $validated);
            return redirect()->route('admin.stock-transactions.index')
                ->with('success', 'Transaksi stok berhasil diupdate');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate transaksi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->stockTransactionService->deleteTransaction($id);
            return redirect()->route('admin.stock-transactions.index')
                ->with('success', 'Transaksi stok berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        try {
            $this->stockTransactionService->approveTransaction($id);
            return back()->with('success', 'Transaksi berhasil disetujui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyetujui transaksi: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $this->stockTransactionService->rejectTransaction($id);
            return back()->with('success', 'Transaksi berhasil ditolak');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menolak transaksi: ' . $e->getMessage());
        }
    }
}
