<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\StockTransactionService;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\NotificationService;
use App\Models\StockTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StockController extends Controller
{
    protected $stockTransactionService;
    protected $productService;
    protected $categoryService;
    protected $notificationService;

    public function __construct(
        StockTransactionService $stockTransactionService,
        ProductService $productService,
        CategoryService $categoryService,
        NotificationService $notificationService
    ) {
        $this->stockTransactionService = $stockTransactionService;
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->notificationService = $notificationService;
    }

    public function in()
    {
        // Get pending incoming transactions assigned to current user
        $pendingTransactions = StockTransaction::with(['product', 'user', 'assignedStaff'])
            ->where('type', 'in')
            ->where('status', 'pending')
            ->where('assigned_to', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get all products for new transaction form (approved only)
        $products = Product::with('category')->approved()->get()->sortByDesc(function ($product) {
            return $product->current_stock;
        })->values();

        // Statistics
        $confirmedToday = StockTransaction::where('type', 'in')
            ->where('status', 'diterima')
            ->where('assigned_to', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->count();

        $totalThisMonth = StockTransaction::where('type', 'in')
            ->where('status', 'diterima')
            ->where('assigned_to', Auth::id())
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        return view('staff.stocks.in', compact('pendingTransactions', 'confirmedToday', 'totalThisMonth', 'products'));
    }

    public function storeIn(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ], [
            'product_id.required' => 'Produk harus dipilih',
            'product_id.exists' => 'Produk tidak ditemukan',
            'quantity.required' => 'Jumlah harus diisi',
            'quantity.integer' => 'Jumlah harus berupa angka',
            'quantity.min' => 'Jumlah minimal 1',
        ]);

        try {
            $transactionData = [
                'product_id' => $validated['product_id'],
                'type' => 'in',
                'quantity' => $validated['quantity'],
                'date' => now(),
                'status' => 'pending', // Menunggu approval manajer
                'user_id' => Auth::id(),
                'assigned_to' => null, // Tidak ada assignment, ini input mandiri
                'notes' => $validated['notes'] ?? 'Input mandiri oleh staff',
            ];

            $this->stockTransactionService->createTransaction($transactionData);

            // Notify all manajer
            $product = Product::find($validated['product_id']);
            $this->notificationService->createForRole(
                'manajer gudang',
                'Transaksi Barang Masuk Baru',
                Auth::user()->name . ' mengajukan transaksi barang masuk untuk produk ' . $product->name . ' sebanyak ' . $validated['quantity'] . ' unit',
                'info',
                route('manajer.approval.index')
            );

            return back()->with('success', 'Transaksi barang masuk berhasil diajukan. Menunggu approval manajer.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan transaksi: ' . $e->getMessage());
        }
    }

    public function out()
    {
        // Get pending outgoing transactions assigned to current user
        $pendingTransactions = StockTransaction::with(['product', 'product.category', 'user', 'assignedStaff'])
            ->where('type', 'out')
            ->where('status', 'pending')
            ->where('assigned_to', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get all products for new transaction form (approved only)
        $products = Product::with(['category', 'stockTransactions'])->approved()->get()->sortByDesc(function ($product) {
            return $product->current_stock;
        })->values();

        // Statistics
        $preparedToday = StockTransaction::where('type', 'out')
            ->where('status', 'dikeluarkan')
            ->where('assigned_to', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->count();

        $totalThisMonth = StockTransaction::where('type', 'out')
            ->where('status', 'dikeluarkan')
            ->where('assigned_to', Auth::id())
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        return view('staff.stocks.out', compact('pendingTransactions', 'preparedToday', 'totalThisMonth', 'products'));
    }

    public function storeOut(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ], [
            'product_id.required' => 'Produk harus dipilih',
            'product_id.exists' => 'Produk tidak ditemukan',
            'quantity.required' => 'Jumlah harus diisi',
            'quantity.integer' => 'Jumlah harus berupa angka',
            'quantity.min' => 'Jumlah minimal 1',
        ]);

        try {
            // Check stock availability
            $product = Product::with('stockTransactions')->findOrFail($validated['product_id']);

            if ($product->current_stock < $validated['quantity']) {
                return back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $product->current_stock . ' unit');
            }

            $transactionData = [
                'product_id' => $validated['product_id'],
                'type' => 'out',
                'quantity' => $validated['quantity'],
                'date' => now(),
                'status' => 'pending', // Menunggu approval manajer
                'user_id' => Auth::id(),
                'assigned_to' => null, // Tidak ada assignment, ini input mandiri
                'notes' => $validated['notes'] ?? 'Input mandiri oleh staff',
            ];

            $this->stockTransactionService->createTransaction($transactionData);

            // Notify all manajer
            $this->notificationService->createForRole(
                'manajer gudang',
                'Transaksi Barang Keluar Baru',
                Auth::user()->name . ' mengajukan transaksi barang keluar untuk produk ' . $product->name . ' sebanyak ' . $validated['quantity'] . ' unit',
                'warning',
                route('manajer.approval.index')
            );

            return back()->with('success', 'Transaksi barang keluar berhasil diajukan. Menunggu approval manajer.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan transaksi: ' . $e->getMessage());
        }
    }

    public function check(Request $request)
    {
        // Build query
        $query = Product::with('category', 'stockTransactions')->where('status', 'approved');

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

        // Get all products and sort by current_stock descending, then by status (safe > low > out)
        $allProducts = $query->get()->sort(function ($a, $b) {
            // First, compare by stock quantity (descending)
            if ($a->current_stock != $b->current_stock) {
                return $b->current_stock - $a->current_stock;
            }

            // If stock is the same, prioritize by status
            // Safe (above minimum) = 0, Low (at or below minimum but > 0) = 1, Out (0 or below) = 2
            $statusA = $a->current_stock > $a->minimum_stock ? 0 : ($a->current_stock > 0 ? 1 : 2);
            $statusB = $b->current_stock > $b->minimum_stock ? 0 : ($b->current_stock > 0 ? 1 : 2);

            return $statusA - $statusB;
        })->values();

        // Filter by stock status if needed
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

        $categories = $this->categoryService->getAllCategories();

        // Calculate statistics
        $allProductsForStats = Product::with('stockTransactions')->get();
        $totalProducts = $allProductsForStats->count();
        $safeStock = $allProductsForStats->filter(fn($p) => $p->current_stock > $p->minimum_stock)->count();
        $lowStock = $allProductsForStats->filter(fn($p) => $p->current_stock <= $p->minimum_stock && $p->current_stock > 0)->count();
        $outOfStock = $allProductsForStats->filter(fn($p) => $p->current_stock <= 0)->count();

        return view('staff.stocks.check', compact('products', 'categories', 'totalProducts', 'safeStock', 'lowStock', 'outOfStock'));
    }

    public function confirm(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:diterima,dikeluarkan,ditolak',
            'reason' => 'required_if:status,ditolak|nullable|string'
        ]);

        try {
            $transaction = StockTransaction::findOrFail($id);

            // Check if transaction is assigned to current user
            if ($transaction->assigned_to !== Auth::id()) {
                return back()->with('error', 'Anda tidak memiliki akses untuk memproses transaksi ini');
            }

            // Check if already processed
            if ($transaction->status !== 'pending') {
                return back()->with('error', 'Transaksi sudah diproses sebelumnya');
            }

            // Update status
            $transaction->status = $validated['status'];

            // If rejected, add reason to notes
            if ($validated['status'] === 'ditolak' && isset($validated['reason'])) {
                $transaction->notes = ($transaction->notes ? $transaction->notes . ' | ' : '') . 'Ditolak: ' . $validated['reason'];
            }

            $transaction->save();

            // Stock is updated automatically through transaction status change
            if ($validated['status'] === 'diterima') {
                return redirect()->route('staff.stock.in')->with('success', 'Barang masuk berhasil dikonfirmasi dan stok telah diperbarui');
            } elseif ($validated['status'] === 'dikeluarkan') {
                $product = Product::with('stockTransactions')->findOrFail($transaction->product_id);

                // Check stock availability using dynamic calculation
                if ($product->current_stock < $transaction->quantity) {
                    return back()->with('error', 'Stok tidak mencukupi untuk mengeluarkan barang');
                }

                return redirect()->route('staff.stock.out')->with('success', 'Barang keluar berhasil dikonfirmasi dan stok telah diperbarui');
            } else {
                $statusName = $transaction->type === 'in' ? 'barang masuk' : 'barang keluar';
                return back()->with('success', 'Transaksi ' . $statusName . ' berhasil ditolak');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses konfirmasi: ' . $e->getMessage());
        }
    }
}
