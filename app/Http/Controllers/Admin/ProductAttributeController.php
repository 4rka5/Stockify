<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\Product;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Display a listing of attributes.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $productId = $request->get('product_id');

        $query = ProductAttribute::with('product');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('value', 'like', "%{$search}%");
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $attributes = $query->latest()->paginate(20);
        $products = Product::where('status', 'approved')->orderBy('name')->get();

        return view('admin.attributes.index', compact('attributes', 'products'));
    }

    /**
     * Show the form for creating a new attribute.
     */
    public function create()
    {
        $products = Product::where('status', 'approved')->orderBy('name')->get();
        return view('admin.attributes.create', compact('products'));
    }

    /**
     * Store a newly created attribute in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ]);

        try {
            $attribute = ProductAttribute::create($validated);

            $this->activityLogService->logCreate(
                'ProductAttribute',
                $attribute->id,
                "Membuat atribut produk: {$attribute->name} = {$attribute->value} untuk produk {$attribute->product->name}"
            );

            return redirect()->route('admin.attributes.index')
                ->with('success', 'Atribut produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan atribut: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified attribute.
     */
    public function show($id)
    {
        $attribute = ProductAttribute::with('product')->findOrFail($id);
        return view('admin.attributes.show', compact('attribute'));
    }

    /**
     * Show the form for editing the specified attribute.
     */
    public function edit($id)
    {
        $attribute = ProductAttribute::findOrFail($id);
        $products = Product::where('status', 'approved')->orderBy('name')->get();
        return view('admin.attributes.edit', compact('attribute', 'products'));
    }

    /**
     * Update the specified attribute in storage.
     */
    public function update(Request $request, $id)
    {
        $attribute = ProductAttribute::findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ]);

        try {
            $oldValues = $attribute->toArray();
            $attribute->update($validated);

            $this->activityLogService->logUpdate(
                'ProductAttribute',
                $attribute->id,
                "Mengupdate atribut produk: {$attribute->name}",
                $oldValues,
                $attribute->toArray()
            );

            return redirect()->route('admin.attributes.index')
                ->with('success', 'Atribut produk berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate atribut: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified attribute from storage.
     */
    public function destroy($id)
    {
        try {
            $attribute = ProductAttribute::findOrFail($id);
            $attributeName = $attribute->name;
            $productName = $attribute->product->name;

            $attribute->delete();

            $this->activityLogService->logDelete(
                'ProductAttribute',
                $id,
                "Menghapus atribut produk: {$attributeName} dari produk {$productName}"
            );

            return redirect()->route('admin.attributes.index')
                ->with('success', 'Atribut produk berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus atribut: ' . $e->getMessage());
        }
    }
}
