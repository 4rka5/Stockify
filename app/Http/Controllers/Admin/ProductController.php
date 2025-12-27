<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\SupplierService;
use App\Services\NotificationService;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $productService;
    protected $categoryService;
    protected $supplierService;
    protected $notificationService;

    public function __construct(
        ProductService $productService,
        CategoryService $categoryService,
        SupplierService $supplierService,
        NotificationService $notificationService
    ) {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->supplierService = $supplierService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $status = $request->get('status', 'approved'); // Default show approved only

        $query = Product::with(['category', 'supplier', 'creator']);

        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $products = $query->latest()->paginate(15);

        // Count pending products
        $pendingCount = Product::where('status', 'pending')->count();

        return view('admin.products.index', compact('products', 'pendingCount'));
    }

    public function approval(Request $request)
    {
        $keyword = $request->get('search');
        $status = $request->get('status', 'pending'); // Default show pending

        $query = Product::with(['category', 'supplier', 'creator', 'approver', 'stockTransactions']);

        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }

        if ($status && $status !== '') {
            $query->where('status', $status);
        }

        $products = $query->latest()->paginate(15);

        // Count by status
        $pendingCount = Product::where('status', 'pending')->count();
        $approvedCount = Product::where('status', 'approved')->count();
        $rejectedCount = Product::where('status', 'rejected')->count();

        return view('admin.products.approval', compact('products', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    public function create()
    {
        $categories = $this->categoryService->getAllCategories();
        $suppliers = $this->supplierService->getAllSuppliers();
        return view('admin.products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $this->productService->createProduct($validated);
            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return view('admin.products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = $this->productService->getProductById($id);
        $categories = $this->categoryService->getAllCategories();
        $suppliers = $this->supplierService->getAllSuppliers();
        return view('admin.products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $id,
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $this->productService->updateProduct($id, $validated);
            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil diupdate');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate produk: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->productService->deleteProduct($id);
            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        try {
            $product = Product::findOrFail($id);

            if ($product->status !== 'pending') {
                return back()->with('error', 'Produk sudah diproses sebelumnya');
            }

            $product->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Check for pending tasks waiting for product approval
            $pendingTasks = \App\Models\StockTransaction::where('product_id', $product->id)
                ->where('status', 'pending_product_approval')
                ->get();

            foreach ($pendingTasks as $task) {
                // Update task status to pending (now ready for staff)
                $task->update(['status' => 'pending']);

                // Notify assigned staff
                $this->notificationService->create(
                    $task->user_id,
                    'Tugas Baru: ' . ($task->type === 'in' ? 'Barang Masuk' : 'Barang Keluar'),
                    'Produk ' . $product->name . ' telah disetujui. Anda ditugaskan untuk ' .
                    ($task->type === 'in' ? 'menerima' : 'mengeluarkan') . ' barang sebanyak ' . $task->quantity . ' unit.',
                    'info',
                    route('staff.stocks.' . $task->type)
                );
            }

            // Notify creator
            if ($product->creator) {
                $this->notificationService->create(
                    $product->creator->id,
                    'Produk Disetujui',
                    'Produk ' . $product->name . ' (' . $product->sku . ') telah disetujui oleh admin' .
                    ($pendingTasks->count() > 0 ? ' dan tugas telah dikirim ke staff.' : '.'),
                    'success',
                    route('manajer.products.index')
                );
            }

            return back()->with('success', 'Produk berhasil disetujui' .
                ($pendingTasks->count() > 0 ? ' dan ' . $pendingTasks->count() . ' tugas telah diaktifkan.' : '.'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyetujui produk: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        try {
            $product = Product::findOrFail($id);

            if ($product->status !== 'pending') {
                return back()->with('error', 'Produk sudah diproses sebelumnya');
            }

            $product->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $validated['rejection_reason'] ?? 'Tidak ada alasan'
            ]);

            // Delete pending tasks for this rejected product
            $deletedTasks = \App\Models\StockTransaction::where('product_id', $product->id)
                ->where('status', 'pending_product_approval')
                ->delete();

            // Notify creator
            if ($product->creator) {
                $this->notificationService->create(
                    $product->creator->id,
                    'Produk Ditolak',
                    'Produk ' . $product->name . ' (' . $product->sku . ') ditolak. Alasan: ' . ($validated['rejection_reason'] ?? 'Tidak ada alasan') .
                    ($deletedTasks > 0 ? ' Tugas terkait telah dibatalkan.' : ''),
                    'danger',
                    route('manajer.products.index')
                );
            }

            return back()->with('success', 'Produk berhasil ditolak' . ($deletedTasks > 0 ? ' dan tugas terkait dibatalkan.' : '.'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menolak produk: ' . $e->getMessage());
        }
    }
}
