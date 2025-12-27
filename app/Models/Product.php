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
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    public $timestamps = true;

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
     * Get the user who created the product.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the product.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope for approved products only.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending products.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
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
