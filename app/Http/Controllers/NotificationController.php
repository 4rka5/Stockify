<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display all notifications.
     */
    public function index()
    {
        $notifications = $this->notificationService->getAllNotifications(Auth::id());
        $unreadCount = $this->notificationService->getUnreadCount(Auth::id());

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get notifications for dropdown (AJAX).
     */
    public function getNotifications()
    {
        $notifications = $this->notificationService->getUnreadNotifications(Auth::id(), 5);
        $unreadCount = $this->notificationService->getUnreadCount(Auth::id());

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead($id)
    {
        $notification = $this->notificationService->markAsRead($id);

        if ($notification && $notification->link) {
            return redirect($notification->link);
        }

        return redirect()->back()->with('success', 'Notifikasi ditandai sebagai dibaca');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(Auth::id());

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sebagai dibaca');
    }

    /**
     * Delete a notification.
     */
    public function destroy($id)
    {
        $this->notificationService->delete($id);

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus');
    }

    /**
     * Delete all notifications.
     */
    public function destroyAll()
    {
        $this->notificationService->deleteAll(Auth::id());

        return redirect()->back()->with('success', 'Semua notifikasi berhasil dihapus');
    }
}
