<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'name',
        'sku',
        'description',
        'purchase_price',
        'selling_price',
        'image',
        'minimum_stock',
    ];

    public $timestamps = false;

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the supplier that owns the product.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the attributes for the product.
     */
    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    /**
     * Get the stock transactions for the product.
     */
    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    /**
     * Get the current stock of the product.
     */
    public function getCurrentStockAttribute()
    {
        $stockIn = $this->stockTransactions()
            ->where('type', 'in')
            ->where('status', 'diterima')
            ->sum('quantity');

        $stockOut = $this->stockTransactions()
            ->where('type', 'out')
            ->where('status', 'dikeluarkan')
            ->sum('quantity');

        return $stockIn - $stockOut;
    }

    /**
     * Check if stock is low.
     */
    public function isLowStock()
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}
