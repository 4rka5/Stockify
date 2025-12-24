<?php

namespace App\Http\Controllers\Manajer;

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

        return view('manajer.suppliers.index', compact('suppliers', 'keyword'));
    }
}
