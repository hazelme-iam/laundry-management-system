{{-- resources/views/admin/orders/show.blade.php --}}
<x-sidebar-app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h2>
    </x-slot>

    <div class="py-12" wire:poll.5s>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumb Navigation -->
            <div class="px-4 sm:px-0 mb-6">
                <x-breadcrumbs :items="[
                    'Orders Management' => route('admin.orders.index'),
                    'Order Details' => null
                ]" />
            </div>

            <!-- Order Info Card -->
            <div class="bg-white shadow-lg rounded-lg mx-4 sm:mx-0">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Customer Info -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Customer</h3>
                            <p class="text-lg font-semibold text-gray-900">{{ $order->customer->name ?? 'Unknown' }}</p>
                            <p class="text-sm text-gray-600">{{ $order->customer->phone ?? 'N/A' }}</p>
                        </div>

                        <!-- Order Details -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Order Details</h3>
                            <p class="text-sm text-gray-900">Weight: <span class="font-semibold">{{ $order->weight }}kg</span></p>
                            <p class="text-sm text-gray-900">Service: <span class="font-semibold">{{ ucfirst($order->service_type ?? 'Standard') }}</span></p>
                            <p class="text-sm text-gray-900">Priority: <span class="font-semibold">{{ ucfirst($order->priority ?? 'Normal') }}</span></p>
                        </div>

                        <!-- Status -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Current Status</h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($order->status === 'completed') bg-green-100 text-green-800
                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif(in_array($order->status, ['washing', 'drying', 'folding', 'quality_check'])) bg-blue-100 text-blue-800
                                @elseif($order->status === 'ready') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">Updated: {{ $order->updated_at->diffForHumans() }}</p>
                        </div>

                        <!-- Amount -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Amount</h3>
                            <p class="text-lg font-semibold text-gray-900">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-600">Paid: Rp{{ number_format($order->amount_paid, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    @if($order->remarks)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Remarks</h3>
                        <p class="text-sm text-gray-700">{{ $order->remarks }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Laundry Workflow Control -->
            <div class="bg-white shadow-lg rounded-lg mx-4 sm:mx-0">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Laundry Workflow</h3>
                    
                    <!-- Workflow Stages -->
                    <div class="space-y-4">
                        <!-- Stage 1: Laundry Received -->
                        <div class="flex items-center justify-between p-4 border rounded-lg
                            @if($order->status === 'picked_up') bg-blue-50 border-blue-200
                            @elseif(in_array($order->status, ['washing', 'drying', 'folding', 'quality_check', 'ready', 'completed'])) bg-green-50 border-green-200
                            @else bg-gray-50 border-gray-200
                            @endif">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    @if($order->status === 'picked_up') bg-blue-500 text-white
                                    @elseif(in_array($order->status, ['washing', 'drying', 'folding', 'quality_check', 'ready', 'completed'])) bg-green-500 text-white
                                    @else bg-gray-300 text-gray-600
                                    @endif">
                                    @if(in_array($order->status, ['picked_up', 'washing', 'drying', 'folding', 'quality_check', 'ready', 'completed'])) ✓
                                    @else 1
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Laundry Received</h4>
                                    <p class="text-sm text-gray-600">Laundry received at shop</p>
                                </div>
                            </div>
                            @if($order->status === 'approved')
                            <button onclick="startPickedUp({{ $order->id }})" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Mark as Laundry Received
                            </button>
                            @endif
                        </div>

                        <!-- Stage 2: Washing -->
                        <div class="flex items-center justify-between p-4 border rounded-lg
                            @if($order->status === 'washing') bg-blue-50 border-blue-200
                            @elseif(in_array($order->status, ['drying', 'folding', 'quality_check', 'ready', 'completed'])) bg-green-50 border-green-200
                            @else bg-gray-50 border-gray-200
                            @endif">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    @if($order->status === 'washing') bg-blue-500 text-white
                                    @elseif(in_array($order->status, ['drying', 'folding', 'quality_check', 'ready', 'completed'])) bg-green-500 text-white
                                    @else bg-gray-300 text-gray-600
                                    @endif">
                                    @if(in_array($order->status, ['washing', 'drying', 'folding', 'quality_check', 'ready', 'completed'])) ✓
                                    @else 2
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Washing</h4>
                                    <p class="text-sm text-gray-600">37-38 minutes cycle</p>
                                    @if($order->status === 'washing' && $order->assigned_washer_id)
                                        <div class="mt-2">
                                            <div class="text-sm font-medium text-blue-600">
                                                Machine: {{ $order->assignedWasher->name ?? 'Unknown' }}
                                            </div>
                                            <div class="text-sm text-blue-600">
                                                Time remaining: <span id="washing-timer">{{ $order->washing_end ? now()->diffInSeconds($order->washing_end, false) > 0 ? ceil(now()->diffInSeconds($order->washing_end, false) / 60) . ' mins' : 'Completed' : 'Calculating...' }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                                <div id="washing-progress" class="bg-blue-500 h-2 rounded-full transition-all duration-1000" style="width: {{ $order->washing_end ? (($order->washing_end->diffInSeconds(now()) / ($order->washing_end->diffInSeconds($order->washing_start))) * 100) : 0 }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if($order->status === 'picked_up' || $order->loads()->where('status', 'pending')->count() > 0)
                                <div class="flex items-center space-x-2">
                                    <form method="POST" action="{{ route('machines.assign-washer', $order->id) }}" class="flex items-center space-x-2" id="washer-form">
                                        @csrf
                                        <select name="washer_id" required class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select Washer</option>
                                            @foreach(\App\Models\Machine::washers()->idle()->get() as $washer)
                                                <option value="{{ $washer->id }}">{{ $washer->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                            Assign Washer
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <!-- Stage 3: Drying -->
                        <div class="flex items-center justify-between p-4 border rounded-lg
                            @if($order->status === 'drying') bg-blue-50 border-blue-200
                            @elseif(in_array($order->status, ['folding', 'quality_check', 'ready', 'completed'])) bg-green-50 border-green-200
                            @else bg-gray-50 border-gray-200
                            @endif">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    @if($order->status === 'drying') bg-blue-500 text-white
                                    @elseif(in_array($order->status, ['folding', 'quality_check', 'ready', 'completed'])) bg-green-500 text-white
                                    @else bg-gray-300 text-gray-600
                                    @endif">
                                    @if(in_array($order->status, ['drying', 'folding', 'quality_check', 'ready', 'completed'])) ✓
                                    @else 3
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Drying</h4>
                                    <p class="text-sm text-gray-600">30 minutes cycle</p>
                                    @if($order->status === 'drying' && $order->assigned_dryer_id)
                                        <div class="mt-2">
                                            <div class="text-sm font-medium text-green-600">
                                                Machine: {{ $order->assignedDryer->name ?? 'Unknown' }}
                                            </div>
                                            <div class="text-sm text-green-600">
                                                Time remaining: <span id="drying-timer">{{ $order->drying_end ? now()->diffInSeconds($order->drying_end, false) > 0 ? ceil(now()->diffInSeconds($order->drying_end, false) / 60) . ' mins' : 'Completed' : 'Calculating...' }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                                <div id="drying-progress" class="bg-green-500 h-2 rounded-full transition-all duration-1000" style="width: {{ $order->drying_end ? (($order->drying_end->diffInSeconds(now()) / ($order->drying_end->diffInSeconds($order->drying_start))) * 100) : 0 }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if($order->loads()->where('status', 'drying')->whereNull('dryer_machine_id')->count() > 0)
                                <div class="flex items-center space-x-2">
                                    <form method="POST" action="{{ route('machines.assign-dryer', $order->id) }}" class="flex items-center space-x-2" id="dryer-form">
                                        @csrf
                                        <select name="dryer_id" required class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                            <option value="">Select Dryer</option>
                                            @foreach(\App\Models\Machine::dryers()->idle()->get() as $dryer)
                                                <option value="{{ $dryer->id }}">{{ $dryer->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                            Assign Dryer
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <!-- Stage 4: Folding -->
                        <div class="flex items-center justify-between p-4 border rounded-lg
                            @if($order->status === 'folding') bg-blue-50 border-blue-200
                            @elseif(in_array($order->status, ['quality_check', 'ready', 'completed'])) bg-green-50 border-green-200
                            @else bg-gray-50 border-gray-200
                            @endif">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    @if($order->status === 'folding') bg-blue-500 text-white
                                    @elseif(in_array($order->status, ['quality_check', 'ready', 'completed'])) bg-green-500 text-white
                                    @else bg-gray-300 text-gray-600
                                    @endif">
                                    @if(in_array($order->status, ['folding', 'quality_check', 'ready', 'completed'])) ✓
                                    @else 4
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Folding</h4>
                                    <p class="text-sm text-gray-600">15-20 minutes</p>
                                </div>
                            </div>
                            @if($order->status === 'drying')
                            <button onclick="startFolding({{ $order->id }})" 
                                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                Start Folding
                            </button>
                            @endif
                        </div>

                        <!-- Stage 5: Quality Check -->
                        <div class="flex items-center justify-between p-4 border rounded-lg
                            @if($order->status === 'quality_check') bg-blue-50 border-blue-200
                            @elseif(in_array($order->status, ['ready', 'completed'])) bg-green-50 border-green-200
                            @else bg-gray-50 border-gray-200
                            @endif">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    @if($order->status === 'quality_check') bg-blue-500 text-white
                                    @elseif(in_array($order->status, ['ready', 'completed'])) bg-green-500 text-white
                                    @else bg-gray-300 text-gray-600
                                    @endif">
                                    @if(in_array($order->status, ['quality_check', 'ready', 'completed'])) ✓
                                    @else 5
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Quality Check</h4>
                                    <p class="text-sm text-gray-600">5-10 minutes inspection</p>
                                </div>
                            </div>
                            @if($order->status === 'folding')
                            <button onclick="startQualityCheck({{ $order->id }})" 
                                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                                Start Quality Check
                            </button>
                            @endif
                        </div>

                        <!-- Stage 6: Ready -->
                        <div class="flex items-center justify-between p-4 border rounded-lg
                            @if($order->status === 'ready') bg-blue-50 border-blue-200
                            @elseif($order->status === 'completed') bg-green-50 border-green-200
                            @else bg-gray-50 border-gray-200
                            @endif">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    @if($order->status === 'ready') bg-blue-500 text-white
                                    @elseif($order->status === 'completed') bg-green-500 text-white
                                    @else bg-gray-300 text-gray-600
                                    @endif">
                                    @if(in_array($order->status, ['ready', 'completed'])) ✓
                                    @else 6
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Ready for Pickup/Delivery</h4>
                                    <p class="text-sm text-gray-600">Order completed and ready</p>
                                </div>
                            </div>
                            @if($order->status === 'quality_check')
                            <button onclick="markAsReady({{ $order->id }})" 
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                Mark as Ready
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load Management -->
            @if($order->loads->count() > 0)
            <div class="bg-white shadow-lg rounded-lg mx-4 sm:mx-0">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Load Management</h3>
                    <div class="space-y-3">
                        @foreach($order->loads as $load)
                        <div class="flex items-center justify-between p-3 border rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold text-sm">{{ $loop->index + 1 }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Load {{ $loop->index + 1 }}</p>
                                    <p class="text-xs text-gray-600">{{ $load->weight }}kg 
                                        @if($load->washerMachine) • Washer: {{ $load->washerMachine->name }} @endif
                                        @if($load->dryerMachine) • Dryer: {{ $load->dryerMachine->name }} @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($load->status === 'completed') bg-green-100 text-green-800
                                    @elseif($load->status === 'washing') bg-blue-100 text-blue-800
                                    @elseif($load->status === 'drying') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($load->status) }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $load->capacity_utilization }}% utilized</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        let timers = {};
        let intervals = {};

        function startPickedUp(orderId) {
            fetch(`/orders/${orderId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: 'picked_up' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status');
            });
        }

        function startWashing(orderId) {
            fetch(`/orders/${orderId}/start-washing`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    startTimer('washing', data.duration * 60, orderId);
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error starting washing');
            });
        }

        function startDrying(orderId) {
            fetch(`/orders/${orderId}/start-drying`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    startTimer('drying', data.duration * 60, orderId);
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error starting drying');
            });
        }

        function startFolding(orderId) {
            fetch(`/orders/${orderId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: 'folding' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error starting folding');
            });
        }

        function startQualityCheck(orderId) {
            fetch(`/orders/${orderId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: 'quality_check' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error starting quality check');
            });
        }

        function markAsReady(orderId) {
            fetch(`/orders/${orderId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: 'ready_for_pickup' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error marking as ready');
            });
        }

        function startTimer(type, durationInSeconds, orderId) {
            const timerElement = document.getElementById(`${type}-timer`);
            const progressElement = document.getElementById(`${type}-progress`);
            
            if (!timerElement || !progressElement) return;

            let timeRemaining = durationInSeconds;
            const totalTime = durationInSeconds;

            // Clear any existing interval
            if (intervals[type]) {
                clearInterval(intervals[type]);
            }

            intervals[type] = setInterval(() => {
                timeRemaining--;
                
                // Update timer display
                const minutes = Math.floor(timeRemaining / 60);
                const seconds = timeRemaining % 60;
                timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                // Update progress bar
                const progress = ((totalTime - timeRemaining) / totalTime) * 100;
                progressElement.style.width = `${progress}%`;
                
                // Timer completed
                if (timeRemaining <= 0) {
                    clearInterval(intervals[type]);
                    timerElement.textContent = 'Completed';
                    progressElement.style.width = '100%';
                    
                    // Check for completed machines
                    fetch('{{ route("machines.check-completed") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Machine completion checked:', data);
                        // Show notification
                        showNotification(`${type.charAt(0).toUpperCase() + type.slice(1)} cycle completed!`);
                        
                        // Auto-refresh after a delay to show updated status
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    })
                    .catch(error => {
                        console.error('Error checking completed machines:', error);
                        // Still show notification and refresh
                        showNotification(`${type.charAt(0).toUpperCase() + type.slice(1)} cycle completed!`);
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    });
                }
            }, 1000);
        }

        function showNotification(message) {
            // Create a simple notification (you can replace this with a better notification system)
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }

        // Initialize any active timers on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check for completed machines every 5 seconds
            setInterval(() => {
                fetch('{{ route("machines.check-completed") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.completed_washing_loads > 0 || data.completed_drying_loads > 0) {
                        // Auto-refresh page to show updated status
                        location.reload();
                    }
                })
                .catch(error => console.log('Error checking completed machines:', error));
            }, 5000);

        // Handle washer form submission with AJAX
        document.getElementById('washer-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Start timer
                    startTimer('washing', data.duration * 60, {{ $order->id }});
                    // Show success message
                    alert(data.message);
                    // Reload page to update UI
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error assigning washer');
            });
        });

        // Handle dryer form submission with AJAX
        document.getElementById('dryer-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Start timer
                    startTimer('drying', data.duration * 60, {{ $order->id }});
                    // Show success message
                    alert(data.message);
                    // Reload page to update UI
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error assigning dryer');
            });
        });
        });
    </script>
</x-sidebar-app>
