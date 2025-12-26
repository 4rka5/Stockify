<?php

namespace App\Repositories;

use App\Models\Supplier;

class SupplierRepository extends BaseRepository
{
    public function __construct(Supplier $model)
    {
        parent::__construct($model);
    }

    public function getAllWithProductCount()
    {
        return $this->model->withCount('products')->get();
    }

    public function findByName($name)
    {
        return $this->model->where('name', 'like', '%' . $name . '%')->get();
    }

    public function search($keyword)
    {
        return $this->model->where(function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('email', 'like', '%' . $keyword . '%')
                ->orWhere('phone', 'like', '%' . $keyword . '%');
        })->get();
    }

    public function searchSuppliers($keyword = null)
    {
        $query = $this->model->withCount('products');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                  ->orWhere('email', 'like', '%' . $keyword . '%')
                  ->orWhere('phone', 'like', '%' . $keyword . '%')
                  ->orWhere('address', 'like', '%' . $keyword . '%');
            });
        }

        return $query->orderBy('id', 'desc')->get();
    }
}
