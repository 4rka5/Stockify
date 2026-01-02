<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the product attributes using this attribute template
     */
    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class, 'attribute_id');
    }
}
