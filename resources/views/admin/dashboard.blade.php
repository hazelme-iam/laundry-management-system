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

            <!-- Stats Section -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 px-4 sm:px-0">
                <!-- Total Orders -->
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Total Orders</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $totalOrders ?? 0 }}</div>
                </div>

                <!-- Pending Orders -->
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Pending Orders</div>
                    <div class="text-2xl font-bold text-red-600">{{ $pendingOrders ?? 0 }}</div>
                </div>

                <!-- On Progress Orders -->
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">In Progress Orders</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $inProgressOrders ?? 0 }}</div>
                </div>

                <!-- Finished Orders -->
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Completed Orders</div>
                    <div class="text-2xl font-bold text-green-600">{{ $completedOrders ?? 0 }}</div>
                </div>
            </div>

            <!-- Laundry Completion Chart Component -->
            <div>
                @include('components.laundry-completion-chart')
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