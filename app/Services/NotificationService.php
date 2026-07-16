<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationService
{
    public function send(int $userId, string $type, string $title, string $message): ?Notification
    {
        $user = User::find($userId);
        if (! $user) {
            return null;
        }

        if ($type === 'service_reminder' && ! $user->enable_service_reminders) {
            return null;
        }

        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    /**
     * Tandai notifikasi tertentu sebagai sudah dibaca.
     */
    public function markAsRead(Notification $notification): Notification
    {
        $notification->update(['is_read' => true]);

        return $notification;
    }

    /**
     * Tandai seluruh notifikasi unread milik seorang user sebagai sudah dibaca.
     *
     * @return int Jumlah baris yang diperbarui
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Dapatkan jumlah notifikasi unread milik seorang user.
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Ambil notifikasi terpaginasi milik seorang user.
     */
    public function getNotifications(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
