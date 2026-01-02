<?php

namespace App\Services;

use App\Repositories\AttributeRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttributeService
{
    protected $attributeRepository;

    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Create new attribute
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $attribute = $this->attributeRepository->create($data);

            DB::commit();
            return $attribute;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating attribute: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update attribute
     */
    public function update($id, array $data)
    {
        try {
            DB::beginTransaction();

            $attribute = $this->attributeRepository->findOrFail($id);
            $oldData = $attribute->toArray();

            $attribute = $this->attributeRepository->update($id, $data);

            DB::commit();
            return $attribute;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating attribute: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete attribute
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $attribute = $this->attributeRepository->findOrFail($id);
            $name = $attribute->name;

            $this->attributeRepository->delete($id);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting attribute: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all active attributes
     */
    public function getActiveAttributes()
    {
        return $this->attributeRepository->getActive();
    }

    /**
     * Get all attributes with product count
     */
    public function getAllAttributes()
    {
        try {
            return \App\Models\Attribute::withCount('productAttributes')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            throw new \Exception('Gagal mengambil data atribut: ' . $e->getMessage());
        }
    }

    /**
     * Search attributes
     */
    public function searchAttribute($keyword)
    {
        try {
            return \App\Models\Attribute::withCount('productAttributes')
                ->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('description', 'like', '%' . $keyword . '%')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            throw new \Exception('Gagal mencari atribut: ' . $e->getMessage());
        }
    }

    /**
     * Get attribute by ID
     */
    public function getAttributeById($id)
    {
        try {
            $attribute = $this->attributeRepository->findWithRelations($id, ['productAttributes']);
            if (!$attribute) {
                throw new \Exception('Atribut tidak ditemukan');
            }
            return $attribute;
        } catch (\Exception $e) {
            throw new \Exception('Gagal mengambil data atribut: ' . $e->getMessage());
        }
    }

    /**
     * Create attribute (alias for create)
     */
    public function createAttribute(array $data)
    {
        return $this->create($data);
    }

    /**
     * Update attribute (alias for update)
     */
    public function updateAttribute($id, array $data)
    {
        return $this->update($id, $data);
    }

    /**
     * Delete attribute (alias for delete)
     */
    public function deleteAttribute($id)
    {
        return $this->delete($id);
    }
}
