<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Tampilkan halaman pusat notifikasi terpaginasi.
     */
    public function index(Request $request): View
    {
        $notifications = $this->notificationService->getNotifications(auth()->id(), 15);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Tandai notifikasi tertentu sebagai sudah dibaca.
     */
    public function markAsRead(Request $request, Notification $notification): RedirectResponse
    {
        // Pastikan notifikasi milik user yang sedang login
        if ($notification->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke notifikasi ini.');
        }

        $this->notificationService->markAsRead($notification);

        return redirect()->back()->with('success', 'Notifikasi ditandai sebagai sudah dibaca.');
    }

    /**
     * Tandai semua notifikasi unread user sebagai sudah dibaca.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $this->notificationService->markAllAsRead(auth()->id());

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sebagai sudah dibaca.');
    }
}
