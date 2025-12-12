{{-- resources/views/admin/reports.blade.php --}}
<x-sidebar-app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2>
    </x-slot>

    <!-- Add this gray background wrapper -->
    <div class="min-h-screen bg-gray-100">
        <div class="py-6 md:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Breadcrumb Navigation -->
                <div class="mb-6">
                    <x-breadcrumbs :items="['Reports' => null]" />
                </div>
                
                <!-- Header Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Reports</h1>
                                <p class="text-gray-600 mt-1">View and analyze your business data</p>
                            </div>
                            <div>
                                <button class="w-full md:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date Range Picker Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <form class="space-y-4 md:space-y-0 md:grid md:grid-cols-4 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="md:col-span-2 md:flex md:items-end">
                                <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Apply Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Summary Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Orders</div>
                            <div class="text-2xl font-bold text-gray-900">1,234</div>
                            <div class="text-sm text-green-600">+12% from last month</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Revenue</div>
                            <div class="text-2xl font-bold text-gray-900">₱45,678.90</div>
                            <div class="text-sm text-green-600">+8% from last month</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Average Order Value</div>
                            <div class="text-2xl font-bold text-gray-900">₱1,234.56</div>
                            <div class="text-sm text-red-600">-2% from last month</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Completed Orders</div>
                            <div class="text-2xl font-bold text-gray-900">1,156</div>
                            <div class="text-sm text-green-600">+15% from last month</div>
                        </div>
                    </div>
                </div>

                <!-- Reports Table Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Table Header -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                            <h2 class="text-lg font-medium text-gray-900">Order History</h2>
                            <div class="w-full sm:w-auto">
                                <select class="w-full sm:w-auto appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option>Last 7 days</option>
                                    <option>Last 30 days</option>
                                    <option>This month</option>
                                    <option>Last month</option>
                                    <option>This year</option>
                                </select>
                            </div>
                        </div>

                        <!-- Table Content -->
                        @php
                            $rows = [];
                            for ($i = 1; $i <= 5; $i++) {
                                $rows[] = (object)[
                                    'id' => 1000 + $i,
                                    'customer' => (object)['name' => 'Customer ' . $i],
                                    'created_at' => now()->subDays($i),
                                    'status' => 'completed',
                                    'total' => rand(1000, 5000)
                                ];
                            }
                        @endphp
                        
                        <!-- Mobile-friendly table -->
                        <div class="overflow-x-auto -mx-6 sm:mx-0">
                            <x-table :headers="['Order ID', 'Customer', 'Date', 'Status', 'Total']" :rows="collect($rows)">
                                @foreach($rows as $order)
                                    <x-table-row>
                                        <x-table-cell>#{{ $order->id }}</x-table-cell>
                                        <x-table-cell>{{ $order->customer->name }}</x-table-cell>
                                        <x-table-cell>{{ $order->created_at->format('Y-m-d') }}</x-table-cell>
                                        <x-table-cell>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </x-table-cell>
                                        <x-table-cell>₱{{ number_format($order->total, 2) }}</x-table-cell>
                                    </x-table-row>
                                @endforeach
                            </x-table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 pt-6 border-t">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-gray-700">
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">24</span> results
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button class="px-3 py-1 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Previous
                                    </button>
                                    <button class="px-3 py-1 rounded-md border border-blue-600 bg-blue-600 text-sm font-medium text-white">
                                        1
                                    </button>
                                    <button class="px-3 py-1 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        2
                                    </button>
                                    <button class="px-3 py-1 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        3
                                    </button>
                                    <button class="px-3 py-1 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</x-sidebar-app>