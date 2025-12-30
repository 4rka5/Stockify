<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model instance.
     */
    public function subject()
    {
        if ($this->model && $this->model_id) {
            $modelClass = "App\\Models\\{$this->model}";
            if (class_exists($modelClass)) {
                return $modelClass::find($this->model_id);
            }
        }
        return null;
    }

    /**
     * Scope for specific action.
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for specific model.
     */
    public function scopeForModel($query, $model)
    {
        return $query->where('model', $model);
    }

    /**
     * Scope for specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
