<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'assigned_by',
        'assigned_at',
        'system_stock',
        'physical_stock',
        'difference',
        'notes',
        'checked_at',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'approved_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    /**
     * Get the product that owns the stock opname.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user (staff) who performed the check.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the manajer who approved the opname.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the manajer who assigned the task.
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Scope for pending opnames.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved opnames.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
