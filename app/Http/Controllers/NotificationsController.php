<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->get(['id', 'title', 'message', 'type', 'is_read', 'created_at']);

        return $this->successResponse($notifications);
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
        }

        return $this->successResponse($notification, 'Marked as read');
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this->successResponse(null, 'All marked as read');
    }

    public function unreadCount(Request $request)
    {
        $count = $request->user()
            ->notifications()
            ->where('is_read', false)
            ->count();

        return $this->successResponse(['unread_count' => $count]);
    }
}
