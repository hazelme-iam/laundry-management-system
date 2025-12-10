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
            });

        return view('admin.dashboard', [
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'inProgressOrders' => $inProgressOrders,
            'completedOrders' => $completedOrders,
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
}