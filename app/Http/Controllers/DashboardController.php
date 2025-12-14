<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ShopSetting;
use App\Services\ShopService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private const WASHERS_COUNT = 5;
    private const DRYERS_COUNT = 5;
    private const CYCLE_CAPACITY_KG = 8;
    private const OPERATING_HOURS_PER_DAY = 12;

    public function index()
    {
        $breadcrumbs = [];
        
        // Get shop settings
        $shopSettings = ShopSetting::get();
        
        // Calculate daily capacity
        $capacityData = $this->calculateDailyCapacity();
        
        // Fetch real order statistics from database
        $totalOrders = Order::count();
        $pendingOrders = Order::pending()->count();
        $inProgressOrders = Order::whereIn('status', ['picked_up', 'washing', 'drying', 'folding', 'quality_check'])->count();
        $completedOrders = Order::completed()->count();
        
        // Chart data - completed vs unfinished orders
        $unfinishedOrders = $totalOrders - $completedOrders;
        $chartData = [
            'completed' => $completedOrders,
            'unfinished' => $unfinishedOrders,
            'completionPercentage' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 0
        ];
        
        // Backlog orders: orders created today that exceed daily capacity
        $today = now()->format('Y-m-d');
        $todayWeight = Order::whereDate('created_at', $today)->sum('weight');
        $dailyWasherCapacity = self::WASHERS_COUNT * self::OPERATING_HOURS_PER_DAY * self::CYCLE_CAPACITY_KG;
        
        // Only show backlog if today's orders exceed capacity
        $backlogOrders = [];
        if ($todayWeight > $dailyWasherCapacity) {
            // Get today's orders sorted by created_at (oldest first) to show which ones overflow
            $allTodayOrders = Order::with(['customer'])
                ->whereDate('created_at', $today)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->orderBy('created_at', 'asc')
                ->get();
            
            $cumulativeWeight = 0;
            foreach ($allTodayOrders as $order) {
                $cumulativeWeight += $order->weight;
                // Only include orders that exceed the daily capacity
                if ($cumulativeWeight > $dailyWasherCapacity) {
                    $backlogOrders[] = [
                        'id' => str_pad($order->id, 3, '0', STR_PAD_LEFT),
                        'customer_name' => $order->customer->name ?? 'Unknown',
                        'weight' => $order->weight,
                        'service_type' => ucfirst($order->service_type ?? 'Standard'),
                        'estimated_time' => $order->estimated_finish ? $order->estimated_finish->format('g:i A') : 'N/A'
                    ];
                }
                if (count($backlogOrders) >= 3) break;
            }
        }
        
        // Today's orders with status breakdown
        $today = now()->format('Y-m-d');
        $todayOrders = Order::with(['customer'])
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => str_pad($order->id, 3, '0', STR_PAD_LEFT),
                    'customer_name' => $order->customer->name ?? 'Unknown',
                    'weight' => $order->weight,
                    'service_type' => ucfirst($order->service_type ?? 'Standard'),
                    'status' => $this->getStatusLabel($order->status),
                    'status_color' => $this->getStatusColor($order->status),
                    'created_at' => $order->created_at->diffForHumans(),
                ];
            })
            ->toArray(); // Convert to array
        
        // Today's orders summary
        $todayOrdersSummary = [
            'total' => Order::whereDate('created_at', $today)->count(),
            'completed' => Order::whereDate('created_at', $today)->where('status', 'completed')->count(),
            'processing' => Order::whereDate('created_at', $today)->whereIn('status', ['picked_up', 'washing', 'drying', 'folding', 'quality_check'])->count(),
            'pending' => Order::whereDate('created_at', $today)->whereIn('status', ['pending', 'approved'])->count(),
        ];
        
        // Fetch recent orders for display
        $orders = Order::with(['customer', 'creator', 'updater'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => str_pad($order->id, 5, '0', STR_PAD_LEFT),
                    'customer_name' => $order->customer->name ?? 'Unknown',
                    'service' => ucfirst($order->service_type ?? 'Standard'),
                    'weight' => $order->weight,
                    'price' => 'Rp' . number_format($order->total_amount, 0, ',', '.'),
                    'status' => ucfirst(str_replace('_', ' ', $order->status)),
                    'status_class' => $this->getStatusClass($order->status)
                ];
            })
            ->toArray(); // Convert to array

        return view('admin.dashboard', [
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'inProgressOrders' => $inProgressOrders,
            'completedOrders' => $completedOrders,
            'chartData' => $chartData,
            'backlogOrders' => $backlogOrders,
            'todayOrders' => $todayOrders,
            'todayOrdersSummary' => $todayOrdersSummary,
            'orders' => $orders,
            'capacityData' => $capacityData,
            'breadcrumbs' => $breadcrumbs,
            'shopSettings' => $shopSettings
        ]);
    }
    
    private function calculateDailyCapacity()
    {
        // Calculate cycles per machine per day (assuming 1 hour per cycle)
        $cyclesPerMachinePerDay = self::OPERATING_HOURS_PER_DAY;
        
        // Calculate total daily capacity in kg
        $dailyWasherCapacity = self::WASHERS_COUNT * $cyclesPerMachinePerDay * self::CYCLE_CAPACITY_KG;
        $dailyDryerCapacity = self::DRYERS_COUNT * $cyclesPerMachinePerDay * self::CYCLE_CAPACITY_KG;
        
        // Today's orders weight (all orders created today)
        $today = now()->format('Y-m-d');
        $todayWeight = Order::whereDate('created_at', $today)->sum('weight');
        
        // Currently in-progress weight (washing, drying, etc.)
        $inProgressWeight = Order::whereIn('status', ['picked_up', 'washing', 'drying', 'folding', 'quality_check'])
            ->sum('weight');
        
        // Allocate today's weight to capacity: up to daily limit, overflow is backlog
        $todayCapacityUsed = min($todayWeight, $dailyWasherCapacity);
        $todayBacklogWeight = max(0, $todayWeight - $dailyWasherCapacity);
        
        // Backlog weight (orders due tomorrow but not completed)
        $tomorrow = now()->addDay()->format('Y-m-d');
        $tomorrowBacklogWeight = Order::whereDate('estimated_finish', $tomorrow)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->sum('weight');
        
        // Total backlog = today's overflow + tomorrow's orders
        $totalBacklogWeight = $todayBacklogWeight + $tomorrowBacklogWeight;
        
        // Calculate utilization percentages based on today's allocated capacity
        $washerUtilization = $dailyWasherCapacity > 0 ? round(($todayCapacityUsed / $dailyWasherCapacity) * 100) : 0;
        $dryerUtilization = $dailyDryerCapacity > 0 ? round(($todayCapacityUsed / $dailyDryerCapacity) * 100) : 0;
        
        // Determine if there's backlog: only when today's capacity is full (100%+) or overflow exists
        $hasBacklog = $todayBacklogWeight > 0 || $washerUtilization >= 100 || $dryerUtilization >= 100;
        
        return [
            'washers' => [
                'count' => self::WASHERS_COUNT,
                'daily_capacity_kg' => $dailyWasherCapacity,
                'current_load_kg' => $todayCapacityUsed,
                'utilization_percent' => $washerUtilization,
                'remaining_capacity_kg' => max(0, $dailyWasherCapacity - $todayCapacityUsed)
            ],
            'dryers' => [
                'count' => self::DRYERS_COUNT,
                'daily_capacity_kg' => $dailyDryerCapacity,
                'current_load_kg' => $todayCapacityUsed,
                'utilization_percent' => $dryerUtilization,
                'remaining_capacity_kg' => max(0, $dailyDryerCapacity - $todayCapacityUsed)
            ],
            'today_weight' => $todayWeight,
            'backlog_weight' => $totalBacklogWeight,
            'has_backlog' => $hasBacklog,
            'operating_hours' => self::OPERATING_HOURS_PER_DAY,
            'cycle_capacity_kg' => self::CYCLE_CAPACITY_KG
        ];
    }
    
    private function getStatusClass($status)
    {
        $statusClasses = [
            'pending' => 'bg-red-500',
            'approved' => 'bg-blue-500',
            'picked_up' => 'bg-yellow-400',
            'washing' => 'bg-yellow-400',
            'drying' => 'bg-yellow-400',
            'folding' => 'bg-yellow-400',
            'quality_check' => 'bg-yellow-400',
            'ready' => 'bg-blue-500',
            'delivery_pending' => 'bg-purple-500',
            'completed' => 'bg-green-500',
            'cancelled' => 'bg-gray-500',
            'rejected' => 'bg-red-500'
        ];
        
        return $statusClasses[$status] ?? 'bg-gray-500';
    }
    
    private function getStatusLabel($status)
    {
        $statusLabels = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'picked_up' => 'Processing',
            'washing' => 'Processing',
            'drying' => 'Processing',
            'folding' => 'Processing',
            'quality_check' => 'Processing',
            'ready' => 'Ready',
            'delivery_pending' => 'Delivery',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'rejected' => 'Rejected'
        ];
        
        return $statusLabels[$status] ?? 'Unknown';
    }
    
    private function getStatusColor($status)
    {
        $statusColors = [
            'pending' => 'text-yellow-600',
            'approved' => 'text-blue-600',
            'picked_up' => 'text-purple-600',
            'washing' => 'text-purple-600',
            'drying' => 'text-purple-600',
            'folding' => 'text-purple-600',
            'quality_check' => 'text-purple-600',
            'ready' => 'text-green-600',
            'delivery_pending' => 'text-orange-600',
            'completed' => 'text-green-600',
            'cancelled' => 'text-gray-600',
            'rejected' => 'text-red-600'
        ];
        
        return $statusColors[$status] ?? 'text-gray-600';
    }
}