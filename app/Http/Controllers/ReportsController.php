<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Get date range from request or default to last 30 days
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Get orders within date range
        $ordersQuery = Order::whereBetween('created_at', [$startDate, $endDate]);
        
        // Summary statistics
        $totalOrders = $ordersQuery->count();
        $totalRevenue = $ordersQuery->sum('total_amount');
        $completedOrders = $ordersQuery->where('status', 'completed')->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Get orders for table with pagination
        $orders = $ordersQuery->with('customer')
            ->latest()
            ->paginate(10);

        // Calculate percentage changes (comparing with previous period)
        $previousStartDate = now()->subDays(60)->format('Y-m-d');
        $previousEndDate = now()->subDays(31)->format('Y-m-d');
        
        $previousOrdersQuery = Order::whereBetween('created_at', [$previousStartDate, $previousEndDate]);
        $previousTotalOrders = $previousOrdersQuery->count();
        $previousTotalRevenue = $previousOrdersQuery->sum('total_amount');

        $ordersChange = $previousTotalOrders > 0 ? 
            (($totalOrders - $previousTotalOrders) / $previousTotalOrders) * 100 : 0;
        $revenueChange = $previousTotalRevenue > 0 ? 
            (($totalRevenue - $previousTotalRevenue) / $previousTotalRevenue) * 100 : 0;

        return view('admin.reports', compact(
            'orders',
            'totalOrders',
            'totalRevenue', 
            'completedOrders',
            'averageOrderValue',
            'ordersChange',
            'revenueChange',
            'startDate',
            'endDate'
        ));
    }

    public function export(Request $request)
    {
        // Get date range from request
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Get orders within date range
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('customer')
            ->latest()
            ->get();

        // Generate CSV (simplified version)
        $csv = "Order ID,Customer,Date,Status,Total\n";
        
        foreach ($orders as $order) {
            $csv .= "{$order->id},{$order->customer?->name},{$order->created_at->format('Y-m-d')},{$order->status},{$order->total_amount}\n";
        }

        $filename = "laundry-reports-{$startDate}-to-{$endDate}.csv";
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
