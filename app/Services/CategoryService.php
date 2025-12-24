<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->all();
    }

    public function getAllWithProductCount()
    {
        return $this->categoryRepository->getAllWithProductCount();
    }

    public function getCategoryById($id)
    {
        return $this->categoryRepository->findOrFail($id);
    }

    public function createCategory(array $data)
    {
        try {
            DB::beginTransaction();

            $category = $this->categoryRepository->create($data);

            DB::commit();
            return $category;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to create category: ' . $e->getMessage());
        }
    }

    public function updateCategory($id, array $data)
    {
        try {
            DB::beginTransaction();

            $category = $this->categoryRepository->update($id, $data);

            DB::commit();
            return $category;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to update category: ' . $e->getMessage());
        }
    }

    public function deleteCategory($id)
    {
        try {
            DB::beginTransaction();

            $this->categoryRepository->delete($id);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to delete category: ' . $e->getMessage());
        }
    }

    public function searchCategory($name)
    {
        return $this->categoryRepository->findByName($name);
    }
}
