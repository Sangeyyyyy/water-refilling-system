<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Return unread notifications for the authenticated admin user.
     */
    public function index()
    {
        $user = auth('web')->user();

        if (!$user) {
            return response()->json(['notifications' => [], 'count' => 0]);
        }

        $notifications = $user->unreadNotifications()
            ->latest()
            ->take(20)
            ->get()
            ->map(function($n) {
                $data = is_array($n->data) ? $n->data : json_decode($n->data, true);
                return [
                    'id'         => $n->id,
                    'message'    => $data['message'] ?? 'New notification received',
                    'order_id'   => $data['order_id'] ?? null,
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'count'         => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(string $id)
    {
        $user = auth('web')->user();

        if ($user) {
            $notification = $user->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        $user = auth('web')->user();

        if ($user) {
            $user->unreadNotifications()->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }
}
