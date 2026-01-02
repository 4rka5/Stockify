<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AttributeService;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    protected $attributeService;

    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }

    public function index(Request $request)
    {
        $keyword = $request->get('search');

        if ($keyword) {
            $attributes = $this->attributeService->searchAttribute($keyword);
        } else {
            $attributes = $this->attributeService->getAllAttributes();
        }

        return view('admin.attributes.index', compact('attributes', 'keyword'));
    }

    public function create()
    {
        return view('admin.attributes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            $this->attributeService->createAttribute($validated);
            return redirect()->route('admin.attributes.index')
                ->with('success', 'Atribut berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan atribut: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $attribute = $this->attributeService->getAttributeById($id);
        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            $this->attributeService->updateAttribute($id, $validated);
            return redirect()->route('admin.attributes.index')
                ->with('success', 'Atribut berhasil diupdate');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate atribut: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->attributeService->deleteAttribute($id);
            return redirect()->route('admin.attributes.index')
                ->with('success', 'Atribut berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus atribut: ' . $e->getMessage());
        }
    }
}
