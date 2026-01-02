<?php

namespace App\Repositories;

use App\Models\Attribute;

class AttributeRepository extends BaseRepository
{
    /**
     * Get all active attributes
     */
    public function getActive()
    {
        return $this->model->where('is_active', true)->orderBy('name')->get();
    }

    /**
     * Get attribute by id with relations
     */
    public function findWithRelations($id, $relations = [])
    {
        return $this->model->with($relations)->findOrFail($id);
    }
}
