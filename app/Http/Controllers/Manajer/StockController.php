<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Services\StockTransactionService;
use App\Services\ProductService;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\StockOpname;
use App\Models\Product;
use App\Rules\SufficientStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    protected $stockTransactionService;
    protected $productService;
    protected $notificationService;

    public function __construct(
        StockTransactionService $stockTransactionService,
        ProductService $productService,
        NotificationService $notificationService
    ) {
        $this->stockTransactionService = $stockTransactionService;
        $this->productService = $productService;
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $products = $this->productService->getAllProducts()
            ->where('status', 'approved')
            ->sortByDesc(function ($product) {
                return $product->current_stock;
            })
            ->values(); // Reset array keys after sorting
        $staffMembers = User::where('role', 'staff gudang')->get();
        return view('manajer.stocks.index', compact('products', 'staffMembers'));
    }

    public function create()
    {
        $products = $this->productService->getAllProducts()
            ->where('status', 'approved')
            ->sortBy('name')
            ->values();
        $staffMembers = User::where('role', 'staff gudang')->get();
        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        return view('manajer.transactions.create', compact('products', 'staffMembers', 'suppliers'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type');

        if ($type === 'in') {
            return $this->storeIn($request);
        } elseif ($type === 'out') {
            return $this->storeOut($request);
        } elseif ($type === 'opname') {
            return $this->storeOpname($request);
        }

        return back()->with('error', 'Tipe transaksi tidak valid');
    }

    public function storeOpname(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $product = Product::findOrFail($validated['product_id']);
            $staffId = $validated['assigned_to'];
            $staff = User::findOrFail($staffId);

            // Check if this product already has an assigned task for this staff
            $existingTask = StockOpname::where('product_id', $validated['product_id'])
                ->where('user_id', $staffId)
                ->where('status', 'assigned')
                ->first();

            if ($existingTask) {
                return back()->with('error', "Produk {$product->name} sudah memiliki tugas stock opname yang belum selesai untuk staff ini");
            }

            StockOpname::create([
                'product_id' => $validated['product_id'],
                'user_id' => $staffId,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'system_stock' => 0,
                'physical_stock' => 0,
                'difference' => 0,
                'notes' => $validated['notes'] ?? null,
                'checked_at' => null,
                'status' => 'assigned',
            ]);

            // Send notification to staff
            $this->notificationService->create(
                $staffId,
                'Tugas Stock Opname Baru',
                "Anda ditugaskan untuk melakukan pengecekan stok fisik produk {$product->name} oleh Manajer " . auth()->user()->name,
                'info',
                route('staff.stock-opname.index')
            );

            return back()->with('success', "Berhasil menugaskan stock opname produk {$product->name} ke {$staff->name}");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menugaskan stock opname: ' . $e->getMessage());
        }
    }

    public function storeIn(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $validated['type'] = 'in';
        $validated['user_id'] = Auth::id();
        $validated['assigned_by'] = Auth::id();
        $validated['date'] = now();
        $validated['status'] = 'pending';

        try {
            $transaction = $this->stockTransactionService->createTransaction($validated);

            // Send notification to assigned staff
            $product = Product::find($validated['product_id']);
            $staff = User::find($validated['assigned_to']);
            $this->notificationService->create(
                $validated['assigned_to'],
                'Tugas Transaksi Barang Masuk',
                'Anda ditugaskan untuk melakukan transaksi barang masuk ' . $product->name . ' sebanyak ' . $validated['quantity'] . ' unit oleh Manajer ' . Auth::user()->name,
                'info',
                route('staff.stock.in')
            );

            return back()->with('success', 'Transaksi barang masuk berhasil dicatat dan ditugaskan ke staff');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
        }
    }

    public function storeOut(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => [
                'required',
                'integer',
                'min:1',
                new SufficientStock($request->product_id, 'keluar')
            ],
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $validated['type'] = 'out';
        $validated['user_id'] = Auth::id();
        $validated['assigned_by'] = Auth::id();
        $validated['date'] = now();
        $validated['status'] = 'pending';

        try {
            $transaction = $this->stockTransactionService->createTransaction($validated);

            // Send notification to assigned staff
            $product = Product::find($validated['product_id']);
            $staff = User::find($validated['assigned_to']);
            $this->notificationService->create(
                $validated['assigned_to'],
                'Tugas Transaksi Barang Keluar',
                'Anda ditugaskan untuk melakukan transaksi barang keluar ' . $product->name . ' sebanyak ' . $validated['quantity'] . ' unit oleh Manajer ' . Auth::user()->name,
                'warning',
                route('staff.stock.out')
            );

            return back()->with('success', 'Transaksi barang keluar berhasil dicatat dan ditugaskan ke staff');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
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

    public function assignStockOpname(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $staffId = $validated['assigned_to'];
            $staff = User::findOrFail($staffId);
            $productCount = 0;
            $skippedProducts = [];

            // Create stock opname tasks for each product
            foreach ($validated['product_ids'] as $productId) {
                $product = Product::findOrFail($productId);

                // Check if this product already has an assigned task for this staff
                $existingTask = StockOpname::where('product_id', $productId)
                    ->where('user_id', $staffId)
                    ->where('status', 'assigned')
                    ->first();

                if ($existingTask) {
                    $skippedProducts[] = $product->name;
                    continue; // Skip this product
                }

                StockOpname::create([
                    'product_id' => $productId,
                    'user_id' => $staffId, // Staff yang akan mengerjakan
                    'assigned_by' => Auth::id(), // Manajer yang assign
                    'assigned_at' => now(),
                    'system_stock' => 0, // Will be filled by staff
                    'physical_stock' => 0, // Will be filled by staff
                    'difference' => 0, // Will be calculated by staff
                    'notes' => $validated['notes'] ?? null,
                    'checked_at' => null, // Will be filled when staff completes
                    'status' => 'assigned', // New status for assigned tasks
                ]);

                $productCount++;
            }

            // Send notification to staff
            if ($productCount > 0) {
                $this->notificationService->create(
                    $staffId,
                    'Tugas Stock Opname Baru',
                    "Anda ditugaskan untuk melakukan pengecekan stok fisik pada {$productCount} produk oleh Manajer " . auth()->user()->name,
                    'info',
                    'staff.stock-opname.index'
                );
            }

            // Prepare response message
            $message = '';
            if ($productCount > 0) {
                $message = "Berhasil menugaskan {$productCount} produk untuk dicek oleh {$staff->name}";
            }

            if (!empty($skippedProducts)) {
                $skippedList = implode(', ', $skippedProducts);
                $skippedMessage = count($skippedProducts) > 1
                    ? "Produk berikut dilewati karena sudah memiliki tugas yang belum selesai: {$skippedList}"
                    : "Produk {$skippedList} dilewati karena sudah memiliki tugas yang belum selesai";

                if ($productCount > 0) {
                    $message .= '. ' . $skippedMessage;
                } else {
                    return back()->with('info', $skippedMessage);
                }
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menugaskan stock opname: ' . $e->getMessage());
        }
    }
}
