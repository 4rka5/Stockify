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
        $products = $this->productService->getAllProducts()->where('status', 'approved');
        $staffMembers = User::where('role', 'staff gudang')->get();
        return view('manajer.stocks.index', compact('products', 'staffMembers'));
    }

    public function storeIn(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $validated['type'] = 'in';
        $validated['user_id'] = Auth::id();
        $validated['date'] = now();
        $validated['status'] = 'pending';

        try {
            $this->stockTransactionService->createTransaction($validated);
            return back()->with('success', 'Transaksi barang masuk berhasil dicatat dan ditugaskan ke staff');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
        }
    }

    public function storeOut(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
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
        $validated['date'] = now();
        $validated['status'] = 'pending';

        try {
            $this->stockTransactionService->createTransaction($validated);
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
            $productCount = count($validated['product_ids']);

            // Create stock opname tasks for each product
            foreach ($validated['product_ids'] as $productId) {
                $product = Product::findOrFail($productId);

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
            }

            // Send notification to staff
            $this->notificationService->createNotification(
                $staffId,
                'Tugas Stock Opname Baru',
                "Anda ditugaskan untuk melakukan pengecekan stok fisik pada {$productCount} produk oleh Manajer " . auth()->user()->name,
                'staff.stock-opname.index'
            );

            return back()->with('success', "Berhasil menugaskan {$productCount} produk untuk dicek oleh {$staff->name}");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menugaskan stock opname: ' . $e->getMessage());
        }
    }
}
