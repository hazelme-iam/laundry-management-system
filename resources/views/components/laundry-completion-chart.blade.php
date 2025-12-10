<div class="p-4 sm:p-6"> <!-- Added responsive padding -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"> <!-- Added responsive gap -->
        <!-- Order Completion Chart -->
        <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base sm:text-lg font-semibold text-gray-800">Order Completion</h2>
                <span class="text-xs sm:text-sm text-gray-500">Today</span>
            </div>
            
            <div class="flex flex-col items-center">
                <!-- Donut Chart - FIXED -->
                <div class="relative w-32 h-32 sm:w-40 sm:h-40 mb-4"> <!-- Responsive sizes -->
                    <canvas id="laundryCompletionChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-lg sm:text-xl font-bold text-gray-800">75%</div>
                            <div class="text-xs text-gray-500">Complete</div>
                        </div>
                    </div>
                </div>
                
                <!-- Legend -->
                <div class="space-y-2 w-full">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                            <span class="text-xs sm:text-sm text-gray-600">Completed</span>
                        </div>
                        <span class="text-xs sm:text-sm font-semibold">9</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-300 rounded-full mr-2"></div>
                            <span class="text-xs sm:text-sm text-gray-600">Unfinished</span>
                        </div>
                        <span class="text-xs sm:text-sm font-semibold">3</span>
                    </div>
                    <div class="pt-2 border-t border-gray-200">
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-500">Total:</span>
                            <span class="font-semibold">12 orders</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backlog Card -->
        <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base sm:text-lg font-semibold text-gray-800">Backlog Orders</h2>
                <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">Tomorrow</span>
            </div>
            
            <div class="space-y-3">
                <!-- Backlog Item 1 -->
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-100">
                    <div class="flex items-center space-x-2 min-w-0">
                        <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-white text-xs font-bold">#001</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-800 truncate">Juan Dela Cruz</div>
                            <div class="text-xs text-gray-500">5kg • Regular</div>
                        </div>
                    </div>
                    <div class="text-right shrink-0 ml-2">
                        <div class="text-xs font-semibold text-orange-600">9:00 AM</div>
                    </div>
                </div>

                <!-- Backlog Item 2 -->
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-100">
                    <div class="flex items-center space-x-2 min-w-0">
                        <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-white text-xs font-bold">#002</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-800 truncate">Maria Santos</div>
                            <div class="text-xs text-gray-500">3kg • Dry Clean</div>
                        </div>
                    </div>
                    <div class="text-right shrink-0 ml-2">
                        <div class="text-xs font-semibold text-orange-600">10:30 AM</div>
                    </div>
                </div>

                <!-- Backlog Item 3 -->
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-100">
                    <div class="flex items-center space-x-2 min-w-0">
                        <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-white text-xs font-bold">#003</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-800 truncate">Pedro Reyes</div>
                            <div class="text-xs text-gray-500">8kg • Heavy</div>
                        </div>
                    </div>
                    <div class="text-right shrink-0 ml-2">
                        <div class="text-xs font-semibold text-orange-600">2:00 PM</div>
                    </div>
                </div>
            </div>

            <!-- Backlog Summary -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center text-xs sm:text-sm">
                    <span class="text-gray-600">Total Backlog:</span>
                    <span class="font-semibold text-orange-600">3 orders</span>
                </div>
            </div>
        </div>

        <!-- Orders Today Card -->
        <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base sm:text-lg font-semibold text-gray-800">Orders Today</h2>
                <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Today</span>
            </div>
            
            <div class="space-y-3">
                <!-- Today's Order 1 -->
                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg border border-purple-100">
                    <div class="flex items-center space-x-2 min-w-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-white text-xs font-bold">#101</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-800 truncate">John Smith</div>
                            <div class="text-xs text-gray-500">4kg • Express</div>
                        </div>
                    </div>
                    <div class="text-right shrink-0 ml-2">
                        <div class="text-xs font-semibold text-purple-600">Processing</div>
                        <div class="text-xs text-gray-500">30 min ago</div>
                    </div>
                </div>

                <!-- Today's Order 2 -->
                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg border border-purple-100">
                    <div class="flex items-center space-x-2 min-w-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-white text-xs font-bold">#102</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-800 truncate">Sarah Johnson</div>
                            <div class="text-xs text-gray-500">2kg • Fold Only</div>
                        </div>
                    </div>
                    <div class="text-right shrink-0 ml-2">
                        <div class="text-xs font-semibold text-yellow-600">Pending</div>
                        <div class="text-xs text-gray-500">1 hour ago</div>
                    </div>
                </div>

                <!-- Today's Order 3 -->
                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg border border-purple-100">
                    <div class="flex items-center space-x-2 min-w-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-white text-xs font-bold">#103</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-800 truncate">Michael Brown</div>
                            <div class="text-xs text-gray-500">6kg • Regular</div>
                        </div>
                    </div>
                    <div class="text-right shrink-0 ml-2">
                        <div class="text-xs font-semibold text-green-600">Completed</div>
                        <div class="text-xs text-gray-500">2 hours ago</div>
                    </div>
                </div>
            </div>

            <!-- Today's Orders Summary -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center text-xs sm:text-sm">
                    <span class="text-gray-600">Total Today:</span>
                    <span class="font-semibold text-purple-600">15 orders</span>
                </div>
                <div class="flex justify-between items-center text-xs text-gray-500 mt-1">
                    <span>Status:</span>
                    <div class="flex space-x-2">
                        <span class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                            10 done
                        </span>
                        <span class="flex items-center">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mr-1"></div>
                            3 processing
                        </span>
                        <span class="flex items-center">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></div>
                            2 pending
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const laundryCompletionCtx = document.getElementById('laundryCompletionChart').getContext('2d');
        const laundryCompletionChart = new Chart(laundryCompletionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed Laundry', 'Unfinished Laundry'],
                datasets: [{
                    data: [9, 3],
                    backgroundColor: [
                        '#3B82F6', // Medium blue
                        '#93C5FD'  // Light blue
                    ],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} orders (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush