<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $keyword = $request->get('search');

        if ($keyword) {
            $categories = $this->categoryService->searchCategory($keyword);
        } else {
            $categories = $this->categoryService->getAllWithProductCount();
        }

        return view('admin.categories.index', compact('categories', 'keyword'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $this->categoryService->createCategory($validated);
            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $category = $this->categoryService->getCategoryById($id);
        return view('admin.categories.show', compact('category'));
    }

    public function edit($id)
    {
        $category = $this->categoryService->getCategoryById($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $this->categoryService->updateCategory($id, $validated);
            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil diupdate');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate kategori: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->categoryService->deleteCategory($id);
            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}
