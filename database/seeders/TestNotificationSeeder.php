<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $adminUser = User::where('role', 'admin')->first();
        
        if ($adminUser) {
            // Create sample notifications for admin
            Notification::create([
                'type' => 'new_order',
                'title' => 'New Order Received',
                'message' => 'Order #123 has been created by John Doe.',
                'notifiable_type' => User::class,
                'notifiable_id' => $adminUser->id,
                'data' => [
                    'order_id' => 123,
                    'customer_name' => 'John Doe',
                    'weight' => 5.5,
                    'url' => route('admin.orders.show', 123)
                ],
                'is_read' => false,
            ]);

            Notification::create([
                'type' => 'capacity_alert',
                'title' => 'Washer Capacity Warning',
                'message' => 'Washer capacity is at 85%! Consider managing orders.',
                'notifiable_type' => User::class,
                'notifiable_id' => $adminUser->id,
                'data' => [
                    'washer_utilization' => 85,
                    'dryer_utilization' => 70,
                    'url' => route('machines.dashboard')
                ],
                'is_read' => false,
            ]);

            Notification::create([
                'type' => 'order_status',
                'title' => 'Order Status Update',
                'message' => 'Order #124 is now being washed.',
                'notifiable_type' => User::class,
                'notifiable_id' => $adminUser->id,
                'data' => [
                    'order_id' => 124,
                    'status' => 'washing',
                    'url' => route('admin.orders.show', 124)
                ],
                'is_read' => true,
                'read_at' => now()->subMinutes(30),
            ]);
        }

        // Get regular user
        $regularUser = User::where('role', '!=', 'admin')->first();
        
        if ($regularUser) {
            // Create sample notifications for regular user
            Notification::create([
                'type' => 'order_status',
                'title' => 'Your Order is Being Washed',
                'message' => 'Your order #125 is now being washed.',
                'notifiable_type' => User::class,
                'notifiable_id' => $regularUser->id,
                'data' => [
                    'order_id' => 125,
                    'status' => 'washing',
                    'url' => route('user.orders.show', 125)
                ],
                'is_read' => false,
            ]);

            Notification::create([
                'type' => 'pickup_reminder',
                'title' => 'Pickup Reminder',
                'message' => 'Your laundry order #126 is ready for pickup! Please collect it soon.',
                'notifiable_type' => User::class,
                'notifiable_id' => $regularUser->id,
                'data' => [
                    'order_id' => 126,
                    'url' => route('user.orders.show', 126)
                ],
                'is_read' => false,
            ]);
        }

        echo "Test notifications created successfully!\n";
    }
}
