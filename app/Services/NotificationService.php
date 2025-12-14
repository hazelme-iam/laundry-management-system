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
        // Daily capacity constants
        $washersCount = 5;
        $dryersCount = 5;
        $operatingHoursPerDay = 12;
        $cycleCapacityKg = 8;
        
        $dailyWasherCapacity = $washersCount * $operatingHoursPerDay * $cycleCapacityKg; // 480kg
        $dailyDryerCapacity = $dryersCount * $operatingHoursPerDay * $cycleCapacityKg; // 480kg
        
        // Get confirmed weight from orders (approved, in progress, or with confirmed weight)
        $confirmedWeightOrders = Order::where(function ($query) {
            $query->whereNotNull('confirmed_weight')
                ->whereIn('status', ['picked_up', 'washing', 'drying', 'folding', 'quality_check', 'ready'])
                ->orWhere('status', 'approved');
        })->get();
        
        $confirmedWeight = $confirmedWeightOrders->sum(function ($order) {
            return $order->confirmed_weight ?? $order->weight;
        });
        
        $washerUtilization = $dailyWasherCapacity > 0 ? round(($confirmedWeight / $dailyWasherCapacity) * 100) : 0;
        $dryerUtilization = $dailyDryerCapacity > 0 ? round(($confirmedWeight / $dailyDryerCapacity) * 100) : 0;

        // Alert when capacity is 80% or more
        if ($washerUtilization >= 80) {
            $adminUsers = User::where('role', 'admin')->get();
            
            foreach ($adminUsers as $admin) {
                // Check if notification already exists to avoid duplicates
                $existingNotif = Notification::where('notifiable_type', get_class($admin))
                    ->where('notifiable_id', $admin->id)
                    ->where('type', 'capacity_alert')
                    ->where('is_read', false)
                    ->where('created_at', '>=', now()->subHours(1))
                    ->first();
                
                if (!$existingNotif) {
                    self::create(
                        $admin,
                        'capacity_alert',
                        'Washer Capacity Warning',
                        "Washer capacity is at {$washerUtilization}%! Consider managing orders.",
                        [
                            'washer_utilization' => $washerUtilization,
                            'dryer_utilization' => $dryerUtilization,
                            'url' => route('machines.dashboard')
                        ]
                    );
                }
            }
        }

        if ($dryerUtilization >= 80) {
            $adminUsers = User::where('role', 'admin')->get();
            
            foreach ($adminUsers as $admin) {
                // Check if notification already exists to avoid duplicates
                $existingNotif = Notification::where('notifiable_type', get_class($admin))
                    ->where('notifiable_id', $admin->id)
                    ->where('type', 'capacity_alert')
                    ->where('is_read', false)
                    ->where('created_at', '>=', now()->subHours(1))
                    ->first();
                
                if (!$existingNotif) {
                    self::create(
                        $admin,
                        'capacity_alert',
                        'Dryer Capacity Warning',
                        "Dryer capacity is at {$dryerUtilization}%! Consider managing orders.",
                        [
                            'washer_utilization' => $washerUtilization,
                            'dryer_utilization' => $dryerUtilization,
                            'url' => route('machines.dashboard')
                        ]
                    );
                }
            }
        }
    }

    /**
     * New order notification for admins
     * Only notify for online customer orders (not admin-created orders)
     */
    public static function newOrderCreated(Order $order): void
    {
        // Only notify if order was created by an online customer (not by admin)
        // Check if the order was created by the customer themselves
        if ($order->customer && $order->customer->user && $order->created_by === $order->customer->user_id) {
            $adminUsers = User::where('role', 'admin')->get();
            
            foreach ($adminUsers as $admin) {
                self::create(
                    $admin,
                    'new_order',
                    'New Online Order Received',
                    "Online order #{$order->id} has been created by {$order->customer->name}.",
                    [
                        'order_id' => $order->id,
                        'customer_name' => $order->customer->name,
                        'weight' => $order->weight,
                        'url' => route('admin.orders.show', $order->id)
                    ]
                );
            }
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

    /**
     * Washing completed notification for admins
     * Notifies admins when an order finishes washing
     */
    public static function washingCompleted(Order $order): void
    {
        $adminUsers = User::where('role', 'admin')->get();
        
        foreach ($adminUsers as $admin) {
            self::create(
                $admin,
                'washing_completed',
                "Order #{$order->id} - Washing Complete",
                "Order #{$order->id} from {$order->customer->name} has finished washing and is ready for drying.",
                [
                    'order_id' => $order->id,
                    'customer_name' => $order->customer->name,
                    'status' => 'washing_done',
                    'url' => route('admin.orders.show', $order->id)
                ]
            );
        }
    }

    /**
     * Drying completed notification for admins
     * Notifies admins when an order finishes drying in machine
     */
    public static function dryingCompleted(Order $order): void
    {
        $adminUsers = User::where('role', 'admin')->get();
        
        foreach ($adminUsers as $admin) {
            self::create(
                $admin,
                'drying_completed',
                "Order #{$order->id} - Drying Complete",
                "Order #{$order->id} from {$order->customer->name} has finished drying and is ready for folding.",
                [
                    'order_id' => $order->id,
                    'customer_name' => $order->customer->name,
                    'status' => 'drying_done',
                    'url' => route('admin.orders.show', $order->id)
                ]
            );
        }
    }

    /**
     * Machine available notification for admins
     * Notifies admins when a machine becomes available after finishing an order
     */
    public static function machineAvailable(Machine $machine, Order $order = null): void
    {
        $adminUsers = User::where('role', 'admin')->get();
        
        foreach ($adminUsers as $admin) {
            // Build title and message based on whether order is provided
            if ($order) {
                $title = "Order #{$order->id} - {$machine->type} #{$machine->id} Now Available";
                $message = "Order #{$order->id} from {$order->customer->name} has finished and {$machine->type} #{$machine->id} is now available.";
            } else {
                $title = "Machine #{$machine->id} Now Available";
                $message = "{$machine->type} #{$machine->id} is now available and ready for use.";
            }
            
            self::create(
                $admin,
                'machine_available',
                $title,
                $message,
                [
                    'machine_id' => $machine->id,
                    'machine_type' => $machine->type,
                    'machine_name' => $machine->name,
                    'order_id' => $order?->id,
                    'customer_name' => $order?->customer?->name,
                    'url' => route('machines.dashboard')
                ]
            );
        }
    }

    /**
     * Backlog notification for online customers
     * Notifies customer when their order is placed in backlog (will be washed tomorrow)
     */
    public static function orderPlacedInBacklog(Order $order): void
    {
        // Only notify if customer has a user account (online customer)
        if ($order->customer && $order->customer->user) {
            // Use confirmed weight if available, otherwise use weight
            $displayWeight = $order->confirmed_weight ?? $order->weight ?? 'TBD';
            
            self::create(
                $order->customer->user,
                'order_backlog',
                "Order #{$order->id} Placed in Backlog",
                "Your laundry order #{$order->id} ({$displayWeight}kg) has been placed in backlog. Due to today's high volume, it will be washed tomorrow instead. We appreciate your patience!",
                [
                    'order_id' => $order->id,
                    'weight' => $displayWeight,
                    'customer_name' => $order->customer->name,
                    'estimated_finish' => $order->estimated_finish?->format('M d, Y g:i A'),
                    'url' => route('user.orders.show', $order->id)
                ]
            );
        }
    }
}
