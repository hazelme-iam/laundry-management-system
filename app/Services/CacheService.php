<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Machine;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;

class CacheService
{
    // Cache durations in minutes
    const MACHINE_STATUS_CACHE = 5;
    const CUSTOMER_LIST_CACHE = 30;
    const ORDER_STATS_CACHE = 2;
    const PENDING_ORDERS_CACHE = 1;

    /**
     * Get machine statuses with caching
     */
    public static function getMachineStatuses()
    {
        return Cache::remember('machines.statuses', self::MACHINE_STATUS_CACHE * 60, function () {
            return [
                'washers' => Machine::washers()->with('currentOrder')->get(),
                'dryers' => Machine::dryers()->with('currentOrder')->get(),
                'available_washers' => Machine::washers()->idle()->count(),
                'available_dryers' => Machine::dryers()->idle()->count(),
                'total_washers' => Machine::washers()->count(),
                'total_dryers' => Machine::dryers()->count(),
            ];
        });
    }

    /**
     * Get customer list with caching
     */
    public static function getCustomerList()
    {
        return Cache::remember('customers.list', self::CUSTOMER_LIST_CACHE * 60, function () {
            $customers = [];
            
            // Get all regular users (non-admin) and create customer options for them
            $regularUsers = User::where('role', '!=', 'admin')->get();
            
            foreach ($regularUsers as $user) {
                // Check if user has a customer record
                $customerRecord = Customer::where('user_id', $user->id)->first();
                
                if ($customerRecord) {
                    // Use existing customer record
                    $customers[] = (object) [
                        'id' => $customerRecord->id,
                        'name' => $customerRecord->name,
                        'email' => $customerRecord->email,
                        'phone' => $customerRecord->phone,
                        'address' => $customerRecord->address,
                        'customer_type' => $customerRecord->customer_type,
                        'user_id' => $user->id,
                        'is_virtual' => false
                    ];
                } else {
                    // Create virtual customer object for user without customer record
                    $customers[] = (object) [
                        'id' => 'user_' . $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone ?? null,
                        'address' => null,
                        'customer_type' => 'online',
                        'user_id' => $user->id,
                        'is_virtual' => true
                    ];
                }
            }
            
            // Add walk-in customers (customers without user_id)
            $walkinCustomers = Customer::whereNull('user_id')
                ->select('id', 'name', 'email', 'phone', 'customer_type', 'user_id')
                ->get();
            
            foreach ($walkinCustomers as $customer) {
                $customers[] = (object) [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'address' => $customer->address,
                    'customer_type' => $customer->customer_type,
                    'user_id' => null,
                    'is_virtual' => false
                ];
            }
            
            // Sort by name
            usort($customers, function($a, $b) {
                return strcmp($a->name, $b->name);
            });
            
            return collect($customers);
        });
    }

    /**
     * Get order statistics with caching
     */
    public static function getOrderStats()
    {
        return Cache::remember('orders.stats', self::ORDER_STATS_CACHE * 60, function () {
            return [
                'pending' => Order::where('status', 'pending')->count(),
                'in_progress' => Order::whereIn('status', ['picked_up', 'washing', 'drying', 'folding', 'quality_check'])->count(),
                'completed' => Order::where('status', 'completed')->count(),
                'total' => Order::count(),
                'today' => Order::whereDate('created_at', today())->count(),
            ];
        });
    }

    /**
     * Get pending orders with caching
     */
    public static function getPendingOrders()
    {
        return Cache::remember('orders.pending', self::PENDING_ORDERS_CACHE * 60, function () {
            return Order::with(['customer'])
                ->where('status', 'pending')
                ->orderBy('created_at')
                ->limit(10)
                ->get();
        });
    }

    /**
     * Clear machine status cache
     */
    public static function clearMachineCache()
    {
        Cache::forget('machines.statuses');
    }

    /**
     * Clear customer cache
     */
    public static function clearCustomerCache()
    {
        Cache::forget('customers.list');
    }

    /**
     * Clear order statistics cache
     */
    public static function clearOrderStatsCache()
    {
        Cache::forget('orders.stats');
        Cache::forget('orders.pending');
    }

    /**
     * Clear all related caches when machine status changes
     */
    public static function clearMachineRelatedCaches()
    {
        self::clearMachineCache();
        self::clearOrderStatsCache();
    }

    /**
     * Clear all related caches when orders change
     */
    public static function clearOrderRelatedCaches()
    {
        self::clearOrderStatsCache();
    }

    /**
     * Clear all related caches when customers change
     */
    public static function clearCustomerRelatedCaches()
    {
        self::clearCustomerCache();
        self::clearOrderStatsCache();
    }
}
