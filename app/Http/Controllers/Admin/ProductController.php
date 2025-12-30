<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\SupplierService;
use App\Services\NotificationService;
use App\Services\ActivityLogService;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $productService;
    protected $categoryService;
    protected $supplierService;
    protected $notificationService;
    protected $activityLogService;

    public function __construct(
        ProductService $productService,
        CategoryService $categoryService,
        SupplierService $supplierService,
        NotificationService $notificationService,
        ActivityLogService $activityLogService
    ) {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->supplierService = $supplierService;
        $this->notificationService = $notificationService;
        $this->activityLogService = $activityLogService;
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
            'attributes' => 'nullable|array',
            'attributes.*.name' => 'nullable|string|max:100',
            'attributes.*.value' => 'nullable|string|max:255',
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
        $product->load('attributes');
        return view('admin.products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = $this->productService->getProductById($id);
        $product->load('attributes');
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
            'attributes' => 'nullable|array',
            'attributes.*.name' => 'nullable|string|max:100',
            'attributes.*.value' => 'nullable|string|max:255',
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

    /**
     * Export products to Excel
     */
    public function export()
    {
        $this->activityLogService->log('export', 'Mengekspor data produk ke Excel');

        $products = Product::with(['category', 'supplier'])->get();

        $filename = 'products_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Headers
        fputcsv($output, [
            'SKU',
            'Nama Produk',
            'Kategori',
            'Supplier',
            'Deskripsi',
            'Harga Beli',
            'Harga Jual',
            'Minimum Stok',
            'Stok Saat Ini',
            'Status',
        ]);

        // Data
        foreach ($products as $product) {
            fputcsv($output, [
                $product->sku,
                $product->name,
                $product->category->name ?? '',
                $product->supplier->name ?? '',
                $product->description,
                $product->purchase_price,
                $product->selling_price,
                $product->minimum_stock,
                $product->current_stock,
                $product->status,
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        $filename = 'template_import_produk.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Headers
        fputcsv($output, [
            'sku',
            'name',
            'category',
            'supplier',
            'description',
            'purchase_price',
            'selling_price',
            'minimum_stock',
        ]);

        // Sample data
        fputcsv($output, [
            'PROD001',
            'Contoh Produk 1',
            'Elektronik',
            'Supplier A',
            'Deskripsi produk contoh',
            '100000',
            '150000',
            '10',
        ]);

        fputcsv($output, [
            'PROD002',
            'Contoh Produk 2',
            'Pakaian',
            'Supplier B',
            'Deskripsi produk contoh 2',
            '50000',
            '75000',
            '5',
        ]);

        fclose($output);
        exit;
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('admin.products.import');
    }

    /**
     * Import products from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $handle = fopen($file->getRealPath(), 'r');

            // Skip BOM if present
            $bom = fread($handle, 3);
            if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                rewind($handle);
            }

            // Read header
            $header = fgetcsv($handle);

            if (!$header || !in_array('sku', $header)) {
                fclose($handle);
                return back()->with('error', 'Format file tidak valid. Pastikan file menggunakan template yang benar.');
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];
            $row = 1;

            while (($data = fgetcsv($handle)) !== false) {
                $row++;

                // Convert to associative array
                $rowData = array_combine($header, $data);

                // Validate
                $validator = Validator::make($rowData, [
                    'sku' => 'required|string|unique:products,sku',
                    'name' => 'required|string|max:255',
                    'category' => 'required|string',
                    'supplier' => 'required|string',
                    'purchase_price' => 'required|numeric|min:0',
                    'selling_price' => 'required|numeric|min:0',
                    'minimum_stock' => 'required|integer|min:0',
                ]);

                if ($validator->fails()) {
                    $errors[] = [
                        'row' => $row,
                        'errors' => $validator->errors()->all()
                    ];
                    $skipped++;
                    continue;
                }

                // Find category
                $category = Category::where('name', $rowData['category'])->first();
                if (!$category) {
                    $errors[] = [
                        'row' => $row,
                        'errors' => ["Kategori '{$rowData['category']}' tidak ditemukan"]
                    ];
                    $skipped++;
                    continue;
                }

                // Find supplier
                $supplier = Supplier::where('name', $rowData['supplier'])->first();
                if (!$supplier) {
                    $errors[] = [
                        'row' => $row,
                        'errors' => ["Supplier '{$rowData['supplier']}' tidak ditemukan"]
                    ];
                    $skipped++;
                    continue;
                }

                // Create product
                Product::create([
                    'category_id' => $category->id,
                    'supplier_id' => $supplier->id,
                    'name' => $rowData['name'],
                    'sku' => $rowData['sku'],
                    'description' => $rowData['description'] ?? null,
                    'purchase_price' => $rowData['purchase_price'],
                    'selling_price' => $rowData['selling_price'],
                    'minimum_stock' => $rowData['minimum_stock'],
                    'status' => 'approved',
                    'created_by' => Auth::id(),
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);

                $imported++;
            }

            fclose($handle);

            $this->activityLogService->log(
                'import',
                "Mengimpor produk: {$imported} berhasil, {$skipped} dilewati"
            );

            if ($imported > 0 && $skipped == 0) {
                return redirect()->route('admin.products.index')
                    ->with('success', "Berhasil mengimpor {$imported} produk.");
            } elseif ($imported > 0 && $skipped > 0) {
                return redirect()->route('admin.products.index')
                    ->with('warning', "Berhasil mengimpor {$imported} produk. {$skipped} baris dilewati.")
                    ->with('import_errors', $errors);
            } else {
                return back()
                    ->with('error', 'Tidak ada produk yang berhasil diimpor.')
                    ->with('import_errors', $errors);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor file: ' . $e->getMessage());
        }
    }
}

