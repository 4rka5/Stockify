<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'attribute_id',
        'name',
        'value',
    ];

    public $timestamps = false;

    /**
     * Get the product that owns the attribute.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attribute template.
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
