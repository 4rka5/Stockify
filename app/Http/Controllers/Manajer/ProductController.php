<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\SupplierService;
use App\Services\NotificationService;
use App\Services\ActivityLogService;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $productService;
    protected $categoryService;
    protected $supplierService;
    protected $notificationService;
    protected $activityLogService;
    protected $productRepository;
    protected $userRepository;

    public function __construct(
        ProductService $productService,
        CategoryService $categoryService,
        SupplierService $supplierService,
        NotificationService $notificationService,
        ActivityLogService $activityLogService,
        ProductRepository $productRepository,
        UserRepository $userRepository
    ) {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->supplierService = $supplierService;
        $this->notificationService = $notificationService;
        $this->activityLogService = $activityLogService;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $keyword = $request->get('search');

        $products = $this->productRepository->searchWithFilters($keyword, 'approved', 15);

        $categories = $this->categoryService->getAllCategories();
        $suppliers = $this->supplierService->getAllSuppliers();

        return view('manajer.products.index', compact('products', 'categories', 'suppliers'));
    }

    public function create()
    {
        $categories = $this->categoryService->getAllCategories();
        $suppliers = $this->supplierService->getAllSuppliers();
        $staffUsers = $this->userRepository->getByRole('staff gudang');
        $attributes = \App\Models\Attribute::where('is_active', true)->with('category')->orderBy('name')->get();

        return view('manajer.products.create', compact('categories', 'suppliers', 'staffUsers', 'attributes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'create_task' => 'nullable|boolean',
            'task_type' => 'nullable|required_if:create_task,1|in:in,out',
            'assigned_staff_id' => 'nullable|required_if:create_task,1|exists:users,id',
            'task_quantity' => 'nullable|required_if:create_task,1|integer|min:1',
            'task_notes' => 'nullable|string|max:500',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_id' => 'nullable|exists:attributes,id',
            'attributes.*.name' => 'nullable|string|max:100',
            'attributes.*.value' => 'nullable|string|max:255',
        ]);

        try {
            // Generate SKU automatically if not provided
            if (empty($validated['sku'])) {
                $validated['sku'] = $this->generateUniqueSKU($validated['name']);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('products', $imageName, 'public');
                $validated['image'] = 'products/' . $imageName;
            }

            // Set status pending and creator
            $validated['status'] = 'pending';
            $validated['created_by'] = Auth::id();

            $product = Product::create($validated);

            // Save product attributes
            if ($request->has('attributes') && is_array($request->attributes)) {
                foreach ($request->attributes as $attribute) {
                    if (!empty($attribute['name']) && !empty($attribute['value'])) {
                        $product->attributes()->create([
                            'name' => $attribute['name'],
                            'value' => $attribute['value'],
                        ]);
                    }
                }
            }

            // Create pending task if requested
            if ($request->create_task && $request->task_type && $request->assigned_staff_id && $request->task_quantity) {
                \App\Models\StockTransaction::create([
                    'product_id' => $product->id,
                    'user_id' => $request->assigned_staff_id,
                    'type' => $request->task_type,
                    'quantity' => $request->task_quantity,
                    'date' => now(),
                    'notes' => $request->task_notes,
                    'status' => 'pending_product_approval', // Special status
                    'assigned_by' => Auth::id(),
                ]);
            }

            // Notify all admin
            $this->notificationService->createForRole(
                'admin',
                'Produk Baru Menunggu Approval',
                Auth::user()->name . ' mengajukan produk baru: ' . $product->name . ' (' . $product->sku . ')',
                'info',
                route('admin.products.approval')
            );

            // Log activity
            $this->activityLogService->logCreate(
                'Product',
                $product->id,
                'Manajer mengajukan produk baru: ' . $product->name . ' (' . $product->sku . ')'
            );

            return redirect()->route('manajer.products.index')
                ->with('success', 'Produk berhasil diajukan. Menunggu approval dari admin.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique SKU for product
     */
    private function generateUniqueSKU($productName)
    {
        // Get first 3 characters from product name (uppercase, no spaces)
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $productName), 0, 3));

        // If prefix is less than 3 chars, pad with 'X'
        $prefix = str_pad($prefix, 3, 'X');

        // Generate SKU with format: PREFIX-YYYYMMDD-XXXX
        $date = date('Ymd');
        $attempts = 0;
        $maxAttempts = 100;

        do {
            $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $sku = "{$prefix}-{$date}-{$random}";
            $exists = Product::where('sku', $sku)->exists();
            $attempts++;
        } while ($exists && $attempts < $maxAttempts);

        if ($attempts >= $maxAttempts) {
            // Fallback: use timestamp
            $sku = "{$prefix}-" . time();
        }

        return $sku;
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return redirect()->route('manajer.products.index')
                ->with('error', 'Produk tidak ditemukan');
        }

        // Load attributes relationship
        $product->load('attributes');

        return view('manajer.products.show', compact('product'));
    }
}
