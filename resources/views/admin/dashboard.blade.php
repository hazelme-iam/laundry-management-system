<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stats Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    <!-- Total Orders -->
                    <div class="bg-blue-700 text-white rounded-lg p-4 sm:p-6 shadow w-full">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-sm sm:text-base">Total Orders</span>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold">40</div>
                    </div>

                    <!-- Pending Orders -->
                    <div class="bg-white rounded-lg p-4 sm:p-6 shadow flex flex-col items-start justify-center w-full">
                        <span class="text-gray-500 font-semibold text-sm sm:text-base">Pending Orders</span>
                        <span class="text-2xl sm:text-3xl font-bold mt-2">0</span>
                    </div>

                    <!-- On Progress Orders -->
                    <div class="bg-white rounded-lg p-4 sm:p-6 shadow flex flex-col items-start justify-center w-full">
                        <span class="text-gray-500 font-semibold text-sm sm:text-base">On Progress Orders</span>
                        <span class="text-2xl sm:text-3xl font-bold mt-2">3</span>
                    </div>

                    <!-- Finished Orders -->
                    <div class="bg-white rounded-lg p-4 sm:p-6 shadow flex flex-col items-start justify-center w-full">
                        <span class="text-gray-500 font-semibold text-sm sm:text-base">Finished Orders</span>
                        <span class="text-2xl sm:text-3xl font-bold mt-2">9</span>
                    </div>
                </div>
            </div>

            <!-- Laundry Completion Chart Component -->
            @include('components.laundry-completion-chart')

            

            <!-- Orders Section Component -->
            @include('components.orders-section', ['orders' => $orders])
           
        </div>
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Stack the scripts from components -->
    @stack('scripts')
</x-app-layout>