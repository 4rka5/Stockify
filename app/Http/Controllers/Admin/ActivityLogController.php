<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use App\Services\UserService;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    protected $activityLogService;
    protected $userService;

    public function __construct(
        ActivityLogService $activityLogService,
        UserService $userService
    ) {
        $this->activityLogService = $activityLogService;
        $this->userService = $userService;
    }

    /**
     * Display activity logs.
     */
    public function index(Request $request)
    {
        $filters = [
            'user_id' => $request->get('user_id'),
            'action' => $request->get('action'),
            'model' => $request->get('model'),
            'search' => $request->get('search'),
            'per_page' => 20,
        ];

        // Date filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $filters['start_date'] = Carbon::parse($request->start_date)->startOfDay();
            $filters['end_date'] = Carbon::parse($request->end_date)->endOfDay();
        } else {
            // Default to last 30 days
            $filters['start_date'] = Carbon::now()->subDays(30)->startOfDay();
            $filters['end_date'] = Carbon::now()->endOfDay();
        }

        $activities = $this->activityLogService->getFilteredActivities($filters);
        $users = $this->userService->getAllUsers();

        // Statistics
        $totalActivities = ActivityLog::whereBetween('created_at', [
            $filters['start_date'],
            $filters['end_date']
        ])->count();

        $actionCounts = ActivityLog::whereBetween('created_at', [
            $filters['start_date'],
            $filters['end_date']
        ])
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->pluck('count', 'action')
            ->toArray();

        $topUsers = ActivityLog::with('user')
            ->whereBetween('created_at', [
                $filters['start_date'],
                $filters['end_date']
            ])
            ->selectRaw('user_id, COUNT(*) as count')
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return view('admin.activity-logs.index', compact(
            'activities',
            'users',
            'totalActivities',
            'actionCounts',
            'topUsers',
            'filters'
        ));
    }

    /**
     * Show activity details.
     */
    public function show($id)
    {
        $activity = ActivityLog::with('user')->findOrFail($id);
        return view('admin.activity-logs.show', compact('activity'));
    }

    /**
     * Delete old activity logs.
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $date = Carbon::now()->subDays($request->days);
        $count = ActivityLog::where('created_at', '<', $date)->delete();

        return back()->with('success', "Berhasil menghapus {$count} log aktivitas yang lebih lama dari {$request->days} hari.");
    }
}

