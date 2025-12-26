<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository extends BaseRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function getAllWithProductCount()
    {
        return $this->model->withCount('products')->get();
    }

    public function findByName($name)
    {
        return $this->model->where('name', 'like', '%' . $name . '%')
            ->withCount('products')
            ->get();
    }
}
