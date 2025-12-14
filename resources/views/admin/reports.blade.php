{{-- resources/views/admin/reports.blade.php --}}
<x-sidebar-app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2>
    </x-slot>

    <div class="min-h-screen bg-gray-100">
        <div class="py-6 md:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Breadcrumb Navigation -->
                <div class="mb-6">
                    <x-breadcrumbs :items="['Reports' => null]" />
                </div>
                
                <!-- Header Card -->
                <div>
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Reports</h1>
                                <p class="text-gray-600 mt-1">View and analyze your business data</p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4H9a2 2 0 00-2 2v2a2 2 0 002 2h4a2 2 0 002-2v-2a2 2 0 00-2-2zm0 0H7v4m10 0v4"></path>
                                    </svg>
                                    Print
                                </button>
                                <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    PDF
                                </a>
                                <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date Range Picker Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <form method="GET" class="space-y-4 md:space-y-0 md:grid md:grid-cols-5 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="order_status" class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
                                <select id="order_status" name="order_status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('order_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="washing" {{ request('order_status') == 'washing' ? 'selected' : '' }}>Washing</option>
                                    <option value="drying" {{ request('order_status') == 'drying' ? 'selected' : '' }}>Drying</option>
                                    <option value="folding" {{ request('order_status') == 'folding' ? 'selected' : '' }}>Folding</option>
                                    <option value="quality_check" {{ request('order_status') == 'quality_check' ? 'selected' : '' }}>Quality Check</option>
                                    <option value="ready" {{ request('order_status') == 'ready' ? 'selected' : '' }}>Ready</option>
                                    <option value="completed" {{ request('order_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                                <select id="payment_status" name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All Payments</option>
                                    <option value="fully_paid" {{ request('payment_status') == 'fully_paid' ? 'selected' : '' }}>Fully Paid</option>
                                    <option value="partially_paid" {{ request('payment_status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                </select>
                            </div>
                            <div class="md:col-span-1 md:flex md:items-end gap-2">
                                <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Apply Filters
                                </button>
                                <a href="{{ route('admin.reports') }}" class="w-full md:w-auto text-center bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Summary Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Orders -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Orders</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $totalOrders }}</div>
                            <div class="text-sm {{ $ordersTrend >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $ordersTrend >= 0 ? '+' : '' }}{{ $ordersTrend }}% from last period
                            </div>
                        </div>
                    </div>

                    <!-- Total Revenue -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Revenue</div>
                            <div class="text-2xl font-bold text-gray-900">₱{{ number_format($totalRevenue, 2) }}</div>
                            <div class="text-sm {{ $revenueTrend >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $revenueTrend >= 0 ? '+' : '' }}{{ $revenueTrend }}% from last period
                            </div>
                        </div>
                    </div>

                    <!-- Average Order Value -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Average Order Value</div>
                            <div class="text-2xl font-bold text-gray-900">₱{{ number_format($avgOrderValue, 2) }}</div>
                            <div class="text-sm text-gray-600">Per order</div>
                        </div>
                    </div>

                    <!-- Completed Orders -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Completed Orders</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $completedOrders }}</div>
                            <div class="text-sm text-gray-600">
                                @if($totalOrders > 0)
                                    {{ round(($completedOrders / $totalOrders) * 100, 1) }}% completion rate
                                @else
                                    0% completion rate
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Sales Breakdown -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-6">Monthly Sales Breakdown</h2>
                        @if($monthlySales->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($monthlySales as $sale)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $sale['month'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $sale['orders'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">₱{{ number_format($sale['revenue'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-8">
                            <p class="text-gray-600">No monthly data available for the selected period.</p>
                        </div>
                        @endif
                    </div>
                </div>


                <!-- Recent Orders Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-lg font-medium text-gray-900">Recent Orders</h2>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">Sort by:</span>
                                <select id="orderSort" class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="highest">Highest Amount</option>
                                    <option value="lowest">Lowest Amount</option>
                                </select>
                            </div>
                        </div>
                        
                        @if($orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                            <a href="{{ route('admin.orders.show', $order->id) }}">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->customer->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($order->status === 'completed') bg-green-100 text-green-800
                                                @elseif($order->status === 'ready') bg-blue-100 text-blue-800
                                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                                @elseif(in_array($order->status, ['washing', 'drying', 'folding', 'quality_check'])) bg-purple-100 text-purple-800
                                                @elseif($order->status === 'approved') bg-indigo-100 text-indigo-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">₱{{ number_format($order->total_amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($order->amount_paid >= $order->total_amount) bg-green-100 text-green-800
                                                @elseif($order->amount_paid > 0) bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                ₱{{ number_format($order->amount_paid, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium 
                                            @if($order->total_amount - $order->amount_paid > 0) text-red-600 @else text-green-600 @endif">
                                            ₱{{ number_format($order->total_amount - $order->amount_paid, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 pt-6 border-t">
                            {{ $orders->appends(request()->query())->links() }}
                        </div>
                        @else
                        <div class="text-center py-12">
                            <p class="text-gray-600">No orders found for the selected filters.</p>
                            <a href="{{ route('admin.reports') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800">
                                Clear filters
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <style>
        /* Status color styles */
        .status-color-pending { background-color: #fbbf24; }
        .status-color-confirmed { background-color: #8b5cf6; }
        .status-color-processing { background-color: #7c3aed; }
        .status-color-ready { background-color: #3b82f6; }
        .status-color-completed { background-color: #10b981; }
        .status-color-cancelled { background-color: #ef4444; }
        
        .status-bar-pending { background-color: #fbbf24; }
        .status-bar-confirmed { background-color: #8b5cf6; }
        .status-bar-processing { background-color: #7c3aed; }
        .status-bar-ready { background-color: #3b82f6; }
        .status-bar-completed { background-color: #10b981; }
        .status-bar-cancelled { background-color: #ef4444; }
    </style>
</x-sidebar-app>