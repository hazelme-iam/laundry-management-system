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
        $type = $request->get('type');
        
        // Build query
        $query = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id);
        
        // Apply type filter if provided
        if ($type) {
            $query->where('type', $type);
        }
        
        // Get notifications
        $notifications = $query->latest()->paginate(15);
        
        // Get counts for filter tabs
        $totalCount = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->count();
        
        $orderStatusCount = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->where('type', 'order_status')
            ->count();
        
        $backlogCount = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->where('type', 'order_backlog')
            ->count();
        
        $pickupCount = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->where('type', 'pickup_reminder')
            ->count();
        
        $washingCount = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->where('type', 'washing_completed')
            ->count();
        
        $dryingCount = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->where('type', 'drying_completed')
            ->count();
        
        $machineAvailableCount = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->where('type', 'machine_available')
            ->count();
        
        $unreadCount = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->unread()
            ->count();

        return view('user.notifications', compact(
            'notifications',
            'totalCount',
            'orderStatusCount',
            'backlogCount',
            'pickupCount',
            'washingCount',
            'dryingCount',
            'machineAvailableCount',
            'unreadCount'
        ));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $notificationId): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            $notification = $user->notifications()->find($notificationId);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            // Mark as read using direct update to ensure it persists
            $notification->update([
                'is_read' => true,
                'read_at' => now()
            ]);
            
            // Verify it was actually marked as read
            $notification->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'is_read' => $notification->is_read
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking notification as read', [
                'notification_id' => $notificationId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            // Mark all unread notifications as read
            $count = $user->unreadNotifications()->update([
                'is_read' => true,
                'read_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Marked {$count} notifications as read",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read: ' . $e->getMessage()
            ], 500);
        }
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

    /**
     * Check for new notifications (for real-time polling)
     */
    public function checkNew(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Get the latest unread notification
        $latestNotification = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->unread()
            ->latest()
            ->first();

        // Count unread notifications
        $unreadCount = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->unread()
            ->count();

        return response()->json([
            'hasNewNotifications' => $unreadCount > 0,
            'unreadCount' => $unreadCount,
            'latestNotification' => $latestNotification ? [
                'title' => $latestNotification->title,
                'message' => $latestNotification->message,
                'type' => $latestNotification->type,
            ] : null,
        ]);
    }
}
