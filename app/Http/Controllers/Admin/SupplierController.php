<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function index(Request $request)
    {
        $keyword = $request->input('search');

        if ($keyword) {
            $suppliers = $this->supplierService->searchSuppliers($keyword);
        } else {
            $suppliers = $this->supplierService->getAllWithProductCount();
        }

        return view('admin.suppliers.index', compact('suppliers', 'keyword'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        try {
            $this->supplierService->createSupplier($validated);
            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Supplier berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan supplier: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $supplier = $this->supplierService->getSupplierById($id);
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit($id)
    {
        $supplier = $this->supplierService->getSupplierById($id);
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        try {
            $this->supplierService->updateSupplier($id, $validated);
            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Supplier berhasil diupdate');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate supplier: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->supplierService->deleteSupplier($id);
            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Supplier berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus supplier: ' . $e->getMessage());
        }
    }
}
