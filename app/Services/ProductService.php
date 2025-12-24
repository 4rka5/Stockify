<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts()
    {
        return $this->productRepository->getAllWithRelations();
    }

    public function paginateProducts($perPage = 15)
    {
        return $this->productRepository->paginateWithRelations($perPage);
    }

    public function getProductById($id)
    {
        return $this->productRepository->findWithRelations($id);
    }

    public function createProduct(array $data)
    {
        try {
            DB::beginTransaction();

            // Handle image upload
            if (isset($data['image']) && $data['image']) {
                $data['image'] = $this->uploadImage($data['image']);
            }

            // Generate SKU if not provided
            if (!isset($data['sku']) || empty($data['sku'])) {
                $data['sku'] = $this->generateSKU();
            }

            $product = $this->productRepository->create($data);

            DB::commit();
            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to create product: ' . $e->getMessage());
        }
    }

    public function updateProduct($id, array $data)
    {
        try {
            DB::beginTransaction();

            $product = $this->productRepository->findOrFail($id);

            // Handle image upload
            if (isset($data['image']) && $data['image']) {
                // Delete old image
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $this->uploadImage($data['image']);
            }

            $product = $this->productRepository->update($id, $data);

            DB::commit();
            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to update product: ' . $e->getMessage());
        }
    }

    public function deleteProduct($id)
    {
        try {
            DB::beginTransaction();

            $product = $this->productRepository->findOrFail($id);

            // Delete image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $this->productRepository->delete($id);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to delete product: ' . $e->getMessage());
        }
    }

    public function searchProduct($keyword)
    {
        return $this->productRepository->search($keyword);
    }

    public function getLowStockProducts()
    {
        return $this->productRepository->getLowStockProducts();
    }

    public function getProductsByCategory($categoryId)
    {
        return $this->productRepository->getByCategory($categoryId);
    }

    public function getProductsBySupplier($supplierId)
    {
        return $this->productRepository->getBySupplier($supplierId);
    }

    public function getTopStockProducts($limit = 5)
    {
        return $this->productRepository->getTopStockProducts($limit);
    }

    private function uploadImage($image)
    {
        $path = $image->store('products', 'public');
        return $path;
    }

    private function generateSKU()
    {
        return 'PRD-' . strtoupper(uniqid());
    }

    public function getProductCurrentStock($productId)
    {
        $product = $this->productRepository->findOrFail($productId);
        return $product->current_stock;
    }
}
