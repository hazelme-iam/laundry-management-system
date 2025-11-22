<div class="bg-white rounded-lg p-6 shadow">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Total Customers</h2>
    <div class="relative h-64">
        <canvas id="totalCustomersChart"></canvas>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalCustomersCtx = document.getElementById('totalCustomersChart').getContext('2d');
        const totalCustomersChart = new Chart(totalCustomersCtx, {
            type: 'pie',
            data: {
                labels: ['New Customers', 'Returning Customers'],
                datasets: [{
                    data: [12, 28],
                    backgroundColor: [
                        '#3B82F6', // Blue for new customers
                        '#8B5CF6'  // Purple for returning customers
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush