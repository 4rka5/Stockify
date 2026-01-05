<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\StockTransactionService;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\NotificationService;
use App\Services\ActivityLogService;
use App\Repositories\StockTransactionRepository;
use App\Repositories\ProductRepository;
use App\Models\StockTransaction;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StockController extends Controller
{
    protected $stockTransactionService;
    protected $productService;
    protected $categoryService;
    protected $notificationService;
    protected $activityLogService;
    protected $stockTransactionRepository;
    protected $productRepository;

    public function __construct(
        StockTransactionService $stockTransactionService,
        ProductService $productService,
        CategoryService $categoryService,
        NotificationService $notificationService,
        ActivityLogService $activityLogService,
        StockTransactionRepository $stockTransactionRepository,
        ProductRepository $productRepository
    ) {
        $this->stockTransactionService = $stockTransactionService;
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->notificationService = $notificationService;
        $this->activityLogService = $activityLogService;
        $this->stockTransactionRepository = $stockTransactionRepository;
        $this->productRepository = $productRepository;
    }

    public function in()
    {
        // Get pending incoming transactions assigned to current user
        $pendingTransactions = $this->stockTransactionRepository->getPendingByUserAndType(Auth::id(), 'in', 10);

        // Get all products for new transaction form (approved only)
        $products = $this->productRepository->getApprovedProducts()->sortByDesc(function ($product) {
            return $product->current_stock;
        })->values();

        // Get all suppliers
        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        // Statistics
        $confirmedToday = $this->stockTransactionRepository->countByStatusAndUser('diterima', Auth::id(), 'in', true);
        $totalThisMonth = $this->stockTransactionRepository->countByStatusAndUser('diterima', Auth::id(), 'in', false, true);

        return view('staff.stocks.in', compact('pendingTransactions', 'confirmedToday', 'totalThisMonth', 'products', 'suppliers'));
    }

    public function storeIn(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ], [
            'product_id.required' => 'Produk harus dipilih',
            'product_id.exists' => 'Produk tidak ditemukan',
            'supplier_id.exists' => 'Supplier tidak ditemukan',
            'quantity.required' => 'Jumlah harus diisi',
            'quantity.integer' => 'Jumlah harus berupa angka',
            'quantity.min' => 'Jumlah minimal 1',
        ]);

        try {
            $transactionData = [
                'product_id' => $validated['product_id'],
                'supplier_id' => $validated['supplier_id'] ?? null,
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

            // Log activity
            $this->activityLogService->logCreate(
                'StockTransaction',
                null,
                'Staff mengajukan transaksi barang masuk untuk produk ' . $product->name . ' sebanyak ' . $validated['quantity'] . ' unit'
            );

            return back()->with('success', 'Transaksi barang masuk berhasil diajukan. Menunggu approval manajer.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan transaksi: ' . $e->getMessage());
        }
    }

    public function out()
    {
        // Get pending outgoing transactions assigned to current user
        $pendingTransactions = $this->stockTransactionRepository->getPendingByUserAndType(Auth::id(), 'out', 10);

        // Get all products for new transaction form (approved only)
        $products = $this->productRepository->getApprovedProducts()->sortByDesc(function ($product) {
            return $product->current_stock;
        })->values();

        // Get all suppliers
        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        // Statistics
        $confirmedToday = $this->stockTransactionRepository->countByStatusAndUser('dikeluarkan', Auth::id(), 'out', true);
        $totalThisMonth = $this->stockTransactionRepository->countByStatusAndUser('dikeluarkan', Auth::id(), 'out', false, true);

        return view('staff.stocks.out', compact('pendingTransactions', 'confirmedToday', 'totalThisMonth', 'products', 'suppliers'));
    }

    public function storeOut(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ], [
            'product_id.required' => 'Produk harus dipilih',
            'product_id.exists' => 'Produk tidak ditemukan',
            'supplier_id.exists' => 'Supplier tidak ditemukan',
            'quantity.required' => 'Jumlah harus diisi',
            'quantity.integer' => 'Jumlah harus berupa angka',
            'quantity.min' => 'Jumlah minimal 1',
        ]);

        try {
            // Check stock availability
            $product = $this->productRepository->findWithRelations($validated['product_id']);

            if ($product->current_stock < $validated['quantity']) {
                return back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $product->current_stock . ' unit');
            }

            $transactionData = [
                'product_id' => $validated['product_id'],
                'supplier_id' => $validated['supplier_id'] ?? null,
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

            // Log activity
            $this->activityLogService->logCreate(
                'StockTransaction',
                null,
                'Staff mengajukan transaksi barang keluar untuk produk ' . $product->name . ' sebanyak ' . $validated['quantity'] . ' unit'
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
            $transaction = StockTransaction::with(['product', 'assignedBy'])->findOrFail($id);

            // Check if transaction is assigned to current user
            if ($transaction->assigned_to !== Auth::id()) {
                return back()->with('error', 'Anda tidak memiliki akses untuk memproses transaksi ini');
            }

            // Check if already processed
            if ($transaction->status !== 'pending') {
                return back()->with('error', 'Transaksi sudah diproses sebelumnya');
            }

            // For stock out, check availability before confirming
            if ($validated['status'] === 'dikeluarkan') {
                $product = Product::with('stockTransactions')->findOrFail($transaction->product_id);
                if ($product->current_stock < $transaction->quantity) {
                    return back()->with('error', 'Stok tidak mencukupi untuk mengeluarkan barang');
                }
            }

            // Update status
            $transaction->status = $validated['status'];

            // If rejected, add reason to notes
            if ($validated['status'] === 'ditolak' && isset($validated['reason'])) {
                $transaction->notes = ($transaction->notes ? $transaction->notes . ' | ' : '') . 'Ditolak: ' . $validated['reason'];
            }

            $transaction->save();

            // Send notification to the manager who assigned this task
            if ($transaction->assigned_by) {
                $statusText = $validated['status'] === 'diterima' ? 'diterima' :
                             ($validated['status'] === 'dikeluarkan' ? 'dikeluarkan' : 'ditolak');
                $message = "Staff " . Auth::user()->name . " telah menyelesaikan tugas {$transaction->type} untuk produk {$transaction->product->name} dengan status: {$statusText}";

                $this->notificationService->sendNotification(
                    $transaction->assigned_by,
                    $message,
                    'info',
                    route('manajer.transactions.show', $transaction->id)
                );
            }

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'confirmed_transaction',
                'description' => "Staff mengkonfirmasi transaksi {$transaction->type} untuk produk {$transaction->product->name} dengan status {$validated['status']}",
                'ip_address' => $request->ip(),
            ]);

            // Stock is updated automatically through transaction status change
            if ($validated['status'] === 'diterima') {
                return redirect()->route('staff.stock.in')->with('success', 'Barang masuk berhasil dikonfirmasi dan stok telah diperbarui');
            } elseif ($validated['status'] === 'dikeluarkan') {
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
