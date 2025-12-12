<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Machine;

class NotificationService
{
    /**
     * Create a notification for a user or customer
     */
    public static function create($notifiable, string $type, string $title, string $message, array $data = []): Notification
    {
        return Notification::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'data' => $data,
        ]);
    }

    /**
     * Order status change notification for customers
     */
    public static function orderStatusChanged(Order $order): void
    {
        if ($order->customer && $order->customer->user) {
            $statusMessages = [
                'pending' => 'Your order has been received and is pending approval.',
                'approved' => 'Your order has been approved!',
                'picked_up' => 'Your laundry has been picked up and is being processed.',
                'washing' => 'Your laundry is now being washed.',
                'drying' => 'Your laundry is now being dried.',
                'folding' => 'Your laundry is being folded and prepared.',
                'quality_check' => 'Your laundry is undergoing quality check.',
                'ready' => 'Your laundry is ready for pickup!',
                'delivery_pending' => 'Your laundry is out for delivery.',
                'completed' => 'Your order has been completed. Thank you!',
                'cancelled' => 'Your order has been cancelled.',
            ];

            $message = $statusMessages[$order->status] ?? "Your order status has been updated to: {$order->status}";

            self::create(
                $order->customer->user,
                'order_status',
                "Order #{$order->id} Status Update",
                $message,
                [
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'url' => route('user.orders.show', $order->id)
                ]
            );
        }
    }

    /**
     * Capacity alert for admins when machines are full
     */
    public static function capacityAlert(): void
    {
        $washers = Machine::washers()->get();
        $dryers = Machine::dryers()->get();
        
        $totalWasherCapacity = $washers->sum('capacity_kg');
        $totalDryerCapacity = $dryers->sum('capacity_kg');
        
        $activeWasherLoad = $washers->where('status', 'in_use')->sum('capacity_kg');
        $activeDryerLoad = $dryers->where('status', 'in_use')->sum('capacity_kg');

        $washerUtilization = ($activeWasherLoad / $totalWasherCapacity) * 100;
        $dryerUtilization = ($activeDryerLoad / $totalDryerCapacity) * 100;

        // Alert when capacity is 80% or more
        if ($washerUtilization >= 80) {
            $adminUsers = User::where('role', 'admin')->get();
            
            foreach ($adminUsers as $admin) {
                self::create(
                    $admin,
                    'capacity_alert',
                    'Washer Capacity Warning',
                    "Washer capacity is at {$washerUtilization}%! Consider managing orders.",
                    [
                        'washer_utilization' => $washerUtilization,
                        'dryer_utilization' => $dryerUtilization,
                        'url' => route('admin.machines.dashboard')
                    ]
                );
            }
        }

        if ($dryerUtilization >= 80) {
            $adminUsers = User::where('role', 'admin')->get();
            
            foreach ($adminUsers as $admin) {
                self::create(
                    $admin,
                    'capacity_alert',
                    'Dryer Capacity Warning',
                    "Dryer capacity is at {$dryerUtilization}%! Consider managing orders.",
                    [
                        'washer_utilization' => $washerUtilization,
                        'dryer_utilization' => $dryerUtilization,
                        'url' => route('admin.machines.dashboard')
                    ]
                );
            }
        }
    }

    /**
     * New order notification for admins
     */
    public static function newOrderCreated(Order $order): void
    {
        $adminUsers = User::where('role', 'admin')->get();
        
        foreach ($adminUsers as $admin) {
            self::create(
                $admin,
                'new_order',
                'New Order Received',
                "Order #{$order->id} has been created by {$order->customer->name}.",
                [
                    'order_id' => $order->id,
                    'customer_name' => $order->customer->name,
                    'weight' => $order->weight,
                    'url' => route('admin.orders.show', $order->id)
                ]
            );
        }
    }

    /**
     * Pickup reminder for completed orders
     */
    public static function pickupReminder(Order $order): void
    {
        if ($order->customer && $order->customer->user && $order->status === 'ready') {
            self::create(
                $order->customer->user,
                'pickup_reminder',
                'Pickup Reminder',
                "Your laundry order #{$order->id} is ready for pickup! Please collect it soon.",
                [
                    'order_id' => $order->id,
                    'url' => route('user.orders.show', $order->id)
                ]
            );
        }
    }

    /**
     * Get unread notifications for user
     */
    public static function getUnreadNotifications($notifiable): \Illuminate\Database\Eloquent\Collection
    {
        return Notification::where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->id)
            ->unread()
            ->latest()
            ->limit(10)
            ->get();
    }

    /**
     * Mark notifications as read
     */
    public static function markAsRead($notificationId): bool
    {
        $notification = Notification::find($notificationId);
        
        if ($notification) {
            return $notification->markAsRead();
        }
        
        return false;
    }

    /**
     * Mark all notifications as read for user
     */
    public static function markAllAsRead($notifiable): int
    {
        return Notification::where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }
}
