<x-sidebar-app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            {{-- Top Section: 2x2 Stats + Order Completion --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6 px-4 sm:px-0">
                {{-- LEFT: 2x2 Stats Grid --}}
                <div class="lg:col-span-2 space-y-4">
                    {{-- First Row --}}
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Total Orders -->
                        <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                            <div class="text-sm text-gray-500">Total Orders</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $totalOrders ?? 0 }}</div>
                            <div class="text-xs text-gray-400 mt-1">Total Orders</div>
                        </div>

                        <!-- Pending Orders -->
                        <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                            <div class="text-sm text-gray-500">Pending Orders</div>
                            <div class="text-2xl font-bold text-red-600">{{ $pendingOrders ?? 0 }}</div>
                            <div class="text-xs text-gray-400 mt-1">Pending Orders</div>
                        </div>
                    </div>

                    {{-- Second Row --}}
                    <div class="grid grid-cols-2 gap-4">
                        <!-- In Progress Orders -->
                        <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                            <div class="text-sm text-gray-500">In progress Orders</div>
                            <div class="text-2xl font-bold text-yellow-600">{{ $inProgressOrders ?? 0 }}</div>
                            <div class="text-xs text-gray-400 mt-1">In Progress</div>
                        </div>

                        <!-- Completed Orders -->
                        <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                            <div class="text-sm text-gray-500">Completed Orders</div>
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