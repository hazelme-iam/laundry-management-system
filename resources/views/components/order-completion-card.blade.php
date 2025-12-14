{{-- resources/views/components/order-completion-card.blade.php --}}
<div class="bg-white rounded-lg xs:rounded-xl sm:rounded-2xl p-3 xs:p-4 sm:p-5 md:p-6 shadow-md xs:shadow-lg border border-gray-100">
    <div class="flex flex-wrap xs:flex-nowrap items-center justify-between mb-3 xs:mb-4 gap-2 xs:gap-0">
        <h2 class="text-sm xs:text-base sm:text-lg font-semibold text-gray-800 truncate flex-1 min-w-0">Laundry Completion</h2>
        <span class="text-xs xs:text-sm text-gray-500 bg-gray-50 px-2 py-1 rounded-full whitespace-nowrap">Today</span>
    </div>
    
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 xs:gap-5 sm:gap-6">
        <!-- Donut Chart -->
        <div class="flex-1 flex justify-center lg:justify-start">
            <div class="relative w-28 h-28 xs:w-32 xs:h-32 sm:w-36 sm:h-36 md:w-40 md:h-40 lg:w-44 lg:h-44">
                <canvas id="orderCompletionChart" class="w-full h-full"></canvas>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-base xs:text-lg sm:text-xl md:text-2xl font-bold text-gray-800">
                            {{ $completionPercentage ?? 0 }}%
                        </div>
                        <div class="text-[10px] xs:text-xs sm:text-sm text-gray-500 mt-0.5">Complete</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Legend -->
        <div class="flex-1 min-w-0 max-w-full lg:max-w-xs">
            <div class="space-y-2 xs:space-y-3 w-full">
                <div class="flex items-center justify-between p-2 xs:p-3 bg-blue-50/50 rounded-lg">
                    <div class="flex items-center min-w-0">
                        <div class="w-2.5 h-2.5 xs:w-3 xs:h-3 bg-blue-500 rounded-full mr-2 xs:mr-3 shrink-0"></div>
                        <span class="text-xs xs:text-sm text-gray-700 truncate">Completed</span>
                    </div>
                    <div class="flex items-center ml-2 shrink-0">
                        <span class="text-xs xs:text-sm font-semibold text-blue-700 bg-blue-100 px-2 py-1 rounded-full min-w-[2.5rem] text-center">
                            {{ $completed ?? 0 }}
                        </span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-2 xs:p-3 bg-blue-50/30 rounded-lg">
                    <div class="flex items-center min-w-0">
                        <div class="w-2.5 h-2.5 xs:w-3 xs:h-3 bg-blue-300 rounded-full mr-2 xs:mr-3 shrink-0"></div>
                        <span class="text-xs xs:text-sm text-gray-700 truncate">Unfinished</span>
                    </div>
                    <div class="flex items-center ml-2 shrink-0">
                        <span class="text-xs xs:text-sm font-semibold text-blue-500 bg-blue-50 px-2 py-1 rounded-full min-w-[2.5rem] text-center">
                            {{ $unfinished ?? 0 }}
                        </span>
                    </div>
                </div>
                
                <div class="pt-2 xs:pt-3 border-t border-gray-200 mt-2 xs:mt-3">
                    <div class="flex justify-between items-center text-xs xs:text-sm p-1">
                        <span class="text-gray-600 font-medium">Total Laundry:</span>
                        <span class="font-semibold text-gray-800 bg-gray-100 px-3 py-1.5 rounded-lg">
                            {{ ($completed ?? 0) + ($unfinished ?? 0) }}
                        </span>
                    </div>
                </div>
                
                <!-- Progress Info -->
                @if(($completed ?? 0) > 0 && ($unfinished ?? 0) > 0)
                <div class="mt-2 xs:mt-3 pt-2 xs:pt-3 border-t border-gray-200">
                    <div class="text-[10px] xs:text-xs text-gray-500">
                        <div class="flex justify-between mb-1">
                            <span>Progress:</span>
                            <span>{{ $completionPercentage ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-500" 
                                 style="width: {{ $completionPercentage ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
                @endif
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
            
            // Adjust border width based on screen size
            const isMobile = window.innerWidth < 640;
            const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
            
            const chart = new Chart(orderCompletionCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Completed Laundry', 'Unfinished Laundry'],
                    datasets: [{
                        data: [completed, unfinished],
                        backgroundColor: [
                            '#3B82F6', // Medium blue
                            '#93C5FD'  // Light blue
                        ],
                        borderColor: '#ffffff',
                        borderWidth: isMobile ? 2 : (isTablet ? 3 : 4),
                        borderAlign: 'inner',
                        cutout: isMobile ? '65%' : (isTablet ? '68%' : '70%')
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            titleColor: '#1f2937',
                            bodyColor: '#4b5563',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            cornerRadius: 8,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} order${value !== 1 ? 's' : ''} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            });
            
            // Handle chart resize on window resize
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    chart.resize();
                }, 250);
            });
        }
    });
</script>
@endpush