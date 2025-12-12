<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        // Ensure user can only mark their own notifications
        if ($notification->notifiable_id !== $request->user()->id || 
            $notification->notifiable_type !== get_class($request->user())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $success = NotificationService::markAsRead($notification->id);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notification marked as read' : 'Failed to mark notification as read'
        ]);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $count = NotificationService::markAllAsRead($user);

        return response()->json([
            'success' => true,
            'message' => "Marked {$count} notifications as read",
            'count' => $count
        ]);
    }

    /**
     * Store a newly created notification in storage.
     */
    public function store(Request $request)
    {
        // This would typically be called by the system, not by users
        return response()->json(['message' => 'Method not available'], 405);
    }

    /**
     * Display the specified notification.
     */
    public function show(Request $request, Notification $notification)
    {
        // Ensure user can only view their own notifications
        if ($notification->notifiable_id !== $request->user()->id || 
            $notification->notifiable_type !== get_class($request->user())) {
            abort(403);
        }

        // Mark as read if not already read
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }

    /**
     * Update the specified notification in storage.
     */
    public function update(Request $request, string $id)
    {
        return response()->json(['message' => 'Method not available'], 405);
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy(Request $request, Notification $notification)
    {
        // Ensure user can only delete their own notifications
        if ($notification->notifiable_id !== $request->user()->id || 
            $notification->notifiable_type !== get_class($request->user())) {
            abort(403);
        }

        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }
}
