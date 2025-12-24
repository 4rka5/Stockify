<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification for a specific user.
     */
    public function create($userId, $title, $message, $type = 'info', $link = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'is_read' => false,
        ]);
    }

    /**
     * Create notifications for multiple users.
     */
    public function createForMultipleUsers($userIds, $title, $message, $type = 'info', $link = null)
    {
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'link' => $link,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Notification::insert($notifications);
    }

    /**
     * Create notification for all users with specific role.
     */
    public function createForRole($role, $title, $message, $type = 'info', $link = null)
    {
        $users = User::where('role', $role)->pluck('id');
        $this->createForMultipleUsers($users, $title, $message, $type, $link);
    }

    /**
     * Get unread notifications for a user.
     */
    public function getUnreadNotifications($userId, $limit = null)
    {
        $query = Notification::where('user_id', $userId)
            ->unread()
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get all notifications for a user.
     */
    public function getAllNotifications($userId, $limit = null)
    {
        $query = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get unread notification count for a user.
     */
    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->count();
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
        return $notification;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead($userId)
    {
        Notification::where('user_id', $userId)
            ->unread()
            ->update(['is_read' => true]);
    }

    /**
     * Delete a notification.
     */
    public function delete($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->delete();
            return true;
        }
        return false;
    }

    /**
     * Delete all notifications for a user.
     */
    public function deleteAll($userId)
    {
        Notification::where('user_id', $userId)->delete();
    }

    /**
     * Notify about new stock transaction.
     */
    public function notifyStockTransaction($transaction, $action)
    {
        $type = $transaction->type === 'in' ? 'Barang Masuk' : 'Barang Keluar';

        if ($action === 'created') {
            // Notify manajer about new pending transaction
            $this->createForRole(
                'manajer gudang',
                "Transaksi {$type} Baru",
                "Transaksi {$type} untuk produk {$transaction->product->name} menunggu persetujuan.",
                'info',
                route('manajer.stock.index')
            );
        } elseif ($action === 'approved') {
            // Notify staff who created the transaction
            $this->create(
                $transaction->user_id,
                "Transaksi {$type} Disetujui",
                "Transaksi {$type} untuk produk {$transaction->product->name} telah disetujui.",
                'success',
                route('staff.stock.index')
            );
        } elseif ($action === 'rejected') {
            // Notify staff who created the transaction
            $this->create(
                $transaction->user_id,
                "Transaksi {$type} Ditolak",
                "Transaksi {$type} untuk produk {$transaction->product->name} telah ditolak.",
                'danger',
                route('staff.stock.index')
            );
        }
    }

    /**
     * Notify about low stock.
     */
    public function notifyLowStock($product)
    {
        // Notify admin and manajer about low stock
        $this->createForRole(
            'admin',
            'Stok Produk Menipis',
            "Produk {$product->name} memiliki stok yang menipis ({$product->current_stock} unit tersisa).",
            'warning',
            route('admin.products.index')
        );

        $this->createForRole(
            'manajer gudang',
            'Stok Produk Menipis',
            "Produk {$product->name} memiliki stok yang menipis ({$product->current_stock} unit tersisa).",
            'warning',
            route('manajer.stock.check')
        );
    }
}
