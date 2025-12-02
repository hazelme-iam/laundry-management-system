{{-- resources/views/admin/reports.blade.php --}}
<x-sidebar-app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumb Navigation -->
            <x-breadcrumbs :items="['Reports' => null]" />
            
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0 px-4 sm:px-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
                    <p class="text-gray-600">View and analyze your business data</p>
                </div>
                <div class="flex space-x-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export
                    </button>
                </div>
            </div>

            <!-- Date Range Picker -->
            <div class="bg-white p-4 rounded-lg shadow">
                <form class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Apply Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-sm font-medium text-gray-500">Total Orders</div>
                    <div class="text-2xl font-bold text-gray-900">1,234</div>
                    <div class="text-sm text-green-600">+12% from last month</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-sm font-medium text-gray-500">Total Revenue</div>
                    <div class="text-2xl font-bold text-gray-900">₱45,678.90</div>
                    <div class="text-sm text-green-600">+8% from last month</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-sm font-medium text-gray-500">Average Order Value</div>
                    <div class="text-2xl font-bold text-gray-900">₱1,234.56</div>
                    <div class="text-sm text-red-600">-2% from last month</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-sm font-medium text-gray-500">Completed Orders</div>
                    <div class="text-2xl font-bold text-gray-900">1,156</div>
                    <div class="text-sm text-green-600">+15% from last month</div>
                </div>
            </div>

            <!-- Reports Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <!-- Table Header -->
                <div class="p-4 border-b flex justify-between items-center">
                    <h2 class="text-lg font-medium text-gray-900">Order History</h2>
                    <div class="relative">
                        <select class="appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option>Last 7 days</option>
                            <option>Last 30 days</option>
                            <option>This month</option>
                            <option>Last month</option>
                            <option>This year</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Table -->
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

                <!-- Pagination -->
                <div class="px-6 py-4 border-t">
                    <div class="flex flex-col sm:flex-row items-center justify-between">
                        <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">24</span> results
                        </div>
                        <div class="flex space-x-1">
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
</x-sidebar-app>