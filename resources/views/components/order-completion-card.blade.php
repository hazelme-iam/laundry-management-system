{{-- resources/views/components/order-completion-card.blade.php --}}
<div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-lg border border-gray-100">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base sm:text-lg font-semibold text-gray-800">Order Completion</h2>
        <span class="text-xs sm:text-sm text-gray-500">Today</span>
    </div>
    
    <div class="flex flex-col items-center">
        <!-- Donut Chart -->
        <div class="relative w-32 h-32 sm:w-40 sm:h-40 mb-4">
            <canvas id="orderCompletionChart"></canvas>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <div class="text-lg sm:text-xl font-bold text-gray-800">
                        {{ $completionPercentage ?? 0 }}%
                    </div>
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
                <span class="text-xs sm:text-sm font-semibold">
                    {{ $completed ?? 0 }}
                </span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-300 rounded-full mr-2"></div>
                    <span class="text-xs sm:text-sm text-gray-600">Unfinished</span>
                </div>
                <span class="text-xs sm:text-sm font-semibold">
                    {{ $unfinished ?? 0 }}
                </span>
            </div>
            <div class="pt-2 border-t border-gray-200">
                <div class="flex justify-between text-xs sm:text-sm">
                    <span class="text-gray-500">Total:</span>
                    <span class="font-semibold">
                        {{ ($completed ?? 0) + ($unfinished ?? 0) }} orders
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('order-completion-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderCompletionCtx = document.getElementById('orderCompletionChart');
        
        if (orderCompletionCtx) {
            const completed = @json($completed ?? 0);
            const unfinished = @json($unfinished ?? 0);
            
            new Chart(orderCompletionCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Completed Laundry', 'Unfinished Laundry'],
                    datasets: [{
                        data: [completed, unfinished],
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
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} orders (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush