<?php

namespace App\Services;

use App\Repositories\SupplierRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class SupplierService
{
    protected $supplierRepository;

    public function __construct(SupplierRepository $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function getAllSuppliers()
    {
        return $this->supplierRepository->all();
    }

    public function getAllWithProductCount()
    {
        return $this->supplierRepository->getAllWithProductCount();
    }

    public function getSupplierById($id)
    {
        return $this->supplierRepository->findOrFail($id);
    }

    public function createSupplier(array $data)
    {
        try {
            DB::beginTransaction();

            $supplier = $this->supplierRepository->create($data);

            DB::commit();
            return $supplier;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to create supplier: ' . $e->getMessage());
        }
    }

    public function updateSupplier($id, array $data)
    {
        try {
            DB::beginTransaction();

            $supplier = $this->supplierRepository->update($id, $data);

            DB::commit();
            return $supplier;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to update supplier: ' . $e->getMessage());
        }
    }

    public function deleteSupplier($id)
    {
        try {
            DB::beginTransaction();

            $this->supplierRepository->delete($id);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to delete supplier: ' . $e->getMessage());
        }
    }

    public function searchSupplier($keyword)
    {
        return $this->supplierRepository->search($keyword);
    }
}
