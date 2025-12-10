<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $breadcrumbs = [];
        
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
        
        // Backlog orders for tomorrow (orders due tomorrow but not completed)
        $tomorrow = now()->addDay()->format('Y-m-d');
        $backlogOrders = Order::with(['customer'])
            ->whereDate('estimated_finish', $tomorrow)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('estimated_finish', 'asc')
            ->take(3)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => str_pad($order->id, 3, '0', STR_PAD_LEFT),
                    'customer_name' => $order->customer->name ?? 'Unknown',
                    'weight' => $order->weight,
                    'service_type' => ucfirst($order->service_type ?? 'Standard'),
                    'estimated_time' => $order->estimated_finish->format('g:i A')
                ];
            })
            ->toArray(); // Convert to array
        
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
            'breadcrumbs' => $breadcrumbs
        ]);
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