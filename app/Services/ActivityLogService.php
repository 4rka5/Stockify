<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log an activity.
     */
    public function log($action, $description, $model = null, $modelId = null, $properties = null)
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log a create action.
     */
    public function logCreate($model, $modelId, $description)
    {
        return $this->log('create', $description, $model, $modelId);
    }

    /**
     * Log an update action.
     */
    public function logUpdate($model, $modelId, $description, $oldValues = [], $newValues = [])
    {
        return $this->log('update', $description, $model, $modelId, [
            'old' => $oldValues,
            'new' => $newValues,
        ]);
    }

    /**
     * Log a delete action.
     */
    public function logDelete($model, $modelId, $description)
    {
        return $this->log('delete', $description, $model, $modelId);
    }

    /**
     * Log a login action.
     */
    public function logLogin($userId, $description = 'User logged in')
    {
        return ActivityLog::create([
            'user_id' => $userId,
            'action' => 'login',
            'model' => 'User',
            'model_id' => $userId,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log a logout action.
     */
    public function logLogout($description = 'User logged out')
    {
        return $this->log('logout', $description, 'User', Auth::id());
    }

    /**
     * Log an approval action.
     */
    public function logApprove($model, $modelId, $description)
    {
        return $this->log('approve', $description, $model, $modelId);
    }

    /**
     * Log a rejection action.
     */
    public function logReject($model, $modelId, $description, $reason = null)
    {
        return $this->log('reject', $description, $model, $modelId, [
            'reason' => $reason
        ]);
    }

    /**
     * Get recent activities.
     */
    public function getRecentActivities($limit = 10)
    {
        return ActivityLog::with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities by user.
     */
    public function getActivitiesByUser($userId, $limit = null)
    {
        $query = ActivityLog::with('user')
            ->where('user_id', $userId)
            ->latest();

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get activities by model.
     */
    public function getActivitiesByModel($model, $modelId = null)
    {
        $query = ActivityLog::with('user')
            ->where('model', $model);

        if ($modelId) {
            $query->where('model_id', $modelId);
        }

        return $query->latest()->get();
    }

    /**
     * Get activities by action.
     */
    public function getActivitiesByAction($action, $limit = null)
    {
        $query = ActivityLog::with('user')
            ->where('action', $action)
            ->latest();

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get activities with filters.
     */
    public function getFilteredActivities($filters = [])
    {
        $query = ActivityLog::with('user')->latest();

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['model'])) {
            $query->where('model', $filters['model']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('created_at', [
                $filters['start_date'],
                $filters['end_date']
            ]);
        }

        if (isset($filters['search'])) {
            $query->where('description', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }
}
