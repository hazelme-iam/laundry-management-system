<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $breadcrumbs = [];
        
        // Sample orders data
        $orders = [
            [
                'id' => '00012',
                'customer_name' => 'John Doe',
                'service' => 'Dry clean',
                'weight' => 5,
                'price' => 'Rp25,000',
                'status' => 'On Progress',
                'status_class' => 'bg-yellow-400'
            ],
            [
                'id' => '00011',
                'customer_name' => 'Ann Smith',
                'service' => 'Clean and press',
                'weight' => 3,
                'price' => 'Rp31,500',
                'status' => 'On Progress',
                'status_class' => 'bg-yellow-400'
            ],
            [
                'id' => '00010',
                'customer_name' => 'Jim Park',
                'service' => 'Clean and press',
                'weight' => 2,
                'price' => 'Rp19,000',
                'status' => 'Pending',
                'status_class' => 'bg-red-500'
            ],
            [
                'id' => '00009',
                'customer_name' => 'Sarah Johnson',
                'service' => 'Dry clean',
                'weight' => 4,
                'price' => 'Rp28,000',
                'status' => 'Completed',
                'status_class' => 'bg-green-500'
            ],
            [
                'id' => '00008',
                'customer_name' => 'Mike Chen',
                'service' => 'Wash only',
                'weight' => 6,
                'price' => 'Rp22,500',
                'status' => 'Ready',
                'status_class' => 'bg-blue-500'
            ],
        ];

        return view('admin.dashboard', [
            'orders' => $orders,
            'breadcrumbs' => $breadcrumbs
        ]);
    }
}