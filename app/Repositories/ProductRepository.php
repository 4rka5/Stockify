<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getAllWithRelations()
    {
        return $this->model->with(['category', 'supplier', 'attributes'])->get();
    }

    public function paginateWithRelations($perPage = 15)
    {
        return $this->model->with(['category', 'supplier'])->paginate($perPage);
    }

    public function findWithRelations($id)
    {
        return $this->model->with(['category', 'supplier', 'attributes', 'stockTransactions'])->findOrFail($id);
    }

    public function findBySku($sku)
    {
        return $this->model->where('sku', $sku)->first();
    }

    public function search($keyword)
    {
        return $this->model->with(['category', 'supplier'])
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('sku', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%');
            })->get();
    }

    public function getLowStockProducts()
    {
        return $this->model->with(['category', 'supplier'])
            ->where('status', 'approved')
            ->get()
            ->filter(function ($product) {
                return $product->isLowStock();
            });
    }

    public function getByCategory($categoryId)
    {
        return $this->model->with(['category', 'supplier'])
            ->where('category_id', $categoryId)
            ->where('status', 'approved')
            ->get();
    }

    public function getBySupplier($supplierId)
    {
        return $this->model->with(['category', 'supplier'])
            ->where('supplier_id', $supplierId)
            ->where('status', 'approved')
            ->get();
    }

    public function getTopStockProducts($limit = 5)
    {
        return $this->model->with(['category', 'supplier', 'stockTransactions'])
            ->where('status', 'approved')
            ->get()
            ->sortByDesc(function ($product) {
                return $product->current_stock;
            })
            ->take($limit);
    }
}
