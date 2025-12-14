<x-sidebar-app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Backlog Notification Alert -->
            @if($backlogNotification)
            <div class="mb-6 px-4 sm:px-0">
                <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-orange-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-orange-800">
                                <strong>Order #{{ $backlogNotification['order_id'] }}</strong> - {{ $backlogNotification['customer_name'] }}
                            </p>
                            <p class="text-sm text-orange-700 mt-1">
                                {{ $backlogNotification['message'] }}
                            </p>
                            <p class="text-xs text-orange-600 mt-1">
                                Weight: <strong>{{ $backlogNotification['weight'] }}kg</strong> - This order has been automatically moved to backlog as it exceeds today's capacity.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0 px-4 sm:px-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                    <p class="text-gray-600">Manage laundry orders and system overview</p>
                </div>
            </div>
            
            <!-- Breadcrumb Navigation -->
            <div class="px-4 sm:px-0 mb-6">
                <x-breadcrumbs :items="$breadcrumbs ?? []" />
            </div>

            {{-- Top Section: 2x2 Stats + la Completion --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6 px-4 sm:px-0">
                {{-- LEFT: 2x2 Stats Grid --}}
                <div class="lg:col-span-2 space-y-4">
                    {{-- First Row --}}
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Total Orders -->
                        <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                            <div class="text-sm text-gray-500">Total Laundry</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $totalOrders ?? 0 }}</div>
                            <div class="text-xs text-gray-400 mt-1">Total Laundry</div>
                        </div>

                        <!-- Pending Orders -->
                        <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                            <div class="text-sm text-gray-500">Pending Laundry</div>
                            <div class="text-2xl font-bold text-red-600">{{ $pendingOrders ?? 0 }}</div>
                            <div class="text-xs text-gray-400 mt-1">Pending Laundry</div>
                        </div>
                    </div>

                    {{-- Second Row --}}
                    <div class="grid grid-cols-2 gap-4">
                        <!-- In Progress Orders -->
                        <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                            <div class="text-sm text-gray-500">In Progress Laundry</div>
                            <div class="text-2xl font-bold text-yellow-600">{{ $inProgressOrders ?? 0 }}</div>
                            <div class="text-xs text-gray-400 mt-1">In Progress</div>
                        </div>

                        <!-- Completed Orders -->
                        <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                            <div class="text-sm text-gray-500">Completed Laundry</div>
                            <div class="text-2xl font-bold text-green-600">{{ $completedOrders ?? 0 }}</div>
                            <div class="text-xs text-gray-400 mt-1">Completed</div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Order Completion Card --}}
                <div class="lg:col-span-1">
                        @include('components.order-completion-card', [
                            'completionPercentage' => $chartData['completionPercentage'] ?? 0,
                            'completed' => $chartData['completed'] ?? 0,
                            'unfinished' => $chartData['unfinished'] ?? 0
                        ])
                </div>
            </div>
            <!-- Laundry Completion Chart Component -->
            <div>
                {{-- Pass all required data to the component --}}
                @include('components.laundry-completion-chart', [
                    'chartData' => $chartData,
                    'backlogOrders' => $backlogOrders,
                    'todayOrders' => $todayOrders,
                    'todayOrdersSummary' => $todayOrdersSummary,
                    'capacityData' => $capacityData
                ])
            </div>

            <!-- Orders Section Component -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mx-4 sm:mx-0">
                @include('components.orders-section', ['orders' => $orders])
            </div>
        </div>
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Stack the scripts from components -->
    @stack('scripts')
</x-sidebar-app>