<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0 px-4 sm:px-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Order Details</h1>
                    <p class="text-gray-600">Order #{{ $order->id }}</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('user.orders.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-center">
                        Back to Orders
                    </a>
                    <a href="{{ route('user.orders.receipt', $order) }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-center flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.414l4 4v10.172A2 2 0 0114.172 18H6a2 2 0 01-2-2V4z" />
                            <path d="M9 9a1 1 0 100-2 1 1 0 000 2zm0 3a1 1 0 100-2 1 1 0 000 2zm0 3a1 1 0 100-2 1 1 0 000 2z" />
                        </svg>
                        Download Receipt
                    </a>
                    @if($order->status === 'pending')
                        <button type="button" 
                                onclick="openCancelModal()"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Cancel Order
                        </button>
                    @endif
                    @if($order->status === 'pending' || $order->status === 'approved')
                        <a href="{{ route('user.orders.create') }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center">
                            New Order
                        </a>
                    @endif
                </div>
            </div>

            <!-- Order Status Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden mx-4 sm:mx-0 mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-2">Order Status</h2>
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                   (in_array($order->status, ['picked_up', 'washing', 'drying', 'folding', 'quality_check']) ? 'bg-blue-100 text-blue-800' : 
                                   ($order->status === 'ready' ? 'bg-indigo-100 text-indigo-800' : 'bg-yellow-100 text-yellow-800'))) }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </div>
                        <div class="mt-4 sm:mt-0 text-right">
                            <div class="text-sm text-gray-500">Order Date</div>
                            <div class="text-lg font-medium text-gray-900">{{ $order->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Workflow Timeline -->
            <div class="bg-white rounded-lg shadow overflow-hidden mx-4 sm:mx-0 mb-6">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Order Progress</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Pending -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <div class="w-4 h-4 rounded-full {{ in_array($order->status, ['pending', 'approved', 'picked_up', 'washing', 'drying', 'folding', 'quality_check', 'ready', 'completed']) ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Pending Approval</p>
                                <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>

                        <!-- Approved -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <div class="w-4 h-4 rounded-full {{ in_array($order->status, ['approved', 'picked_up', 'washing', 'drying', 'folding', 'quality_check', 'ready', 'completed']) ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Approved</p>
                                <p class="text-xs text-gray-500">Awaiting pickup</p>
                            </div>
                        </div>

                        <!-- Picked Up -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <div class="w-4 h-4 rounded-full {{ in_array($order->status, ['picked_up', 'washing', 'drying', 'folding', 'quality_check', 'ready', 'completed']) ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Picked Up</p>
                                <p class="text-xs text-gray-500">In transit to laundry</p>
                            </div>
                        </div>

                        <!-- Washing -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <div class="w-4 h-4 rounded-full {{ in_array($order->status, ['washing', 'drying', 'folding', 'quality_check', 'ready', 'completed']) ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Washing</p>
                                <p class="text-xs text-gray-500">Being washed</p>
                            </div>
                        </div>

                        <!-- Drying -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <div class="w-4 h-4 rounded-full {{ in_array($order->status, ['drying', 'folding', 'quality_check', 'ready', 'completed']) ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Drying</p>
                                <p class="text-xs text-gray-500">Being dried</p>
                            </div>
                        </div>

                        

                        <!-- Ready -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <div class="w-4 h-4 rounded-full {{ in_array($order->status, ['ready', 'completed']) ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Ready for Pickup</p>
                                <p class="text-xs text-gray-500">Awaiting customer pickup</p>
                            </div>
                        </div>

                        <!-- Completed -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <div class="w-4 h-4 rounded-full {{ $order->status === 'completed' ? 'bg-green-600' : 'bg-gray-300' }}"></div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Completed</p>
                                <p class="text-xs text-gray-500">Order finished</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 px-4 sm:px-0">
                <!-- Customer Information -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="p-6 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Customer Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $order->customer->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $order->customer->email ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $order->customer->phone }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $order->customer->address ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="p-6 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Order Details</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Weight</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $order->weight ?? 'To be measured at shop' }} {{ $order->weight ? 'kg' : '' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Estimated Finish</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $order->estimated_finish?->format('M d, Y') ?? 'Pending admin approval' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Pickup Date</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $order->pickup_date?->format('M d, Y') ?? 'Not set' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Finished Date</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $order->finished_at?->format('M d, Y') ?? 'Not finished yet' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                                    <dd class="text-sm text-gray-900 mt-1">
                                        @if($order->amount_paid >= $order->total_amount)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Cash Paid
                                            </span>
                                        @else
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pending Cash Payment
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Amount Due</dt>
                                    <dd class="text-sm text-gray-900 mt-1">₱{{ number_format($order->total_amount, 2) }}</dd>
                                </div>
                            </dl>

                            <!-- Priority and Service Type -->
                            <div class="mt-6 pt-6 border-t">
                                <h4 class="text-md font-medium text-gray-900 mb-4">Service Information</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Priority</span>
                                        <span class="text-sm text-gray-900">{{ ucfirst($order->priority ?? 'Normal') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Service Type</span>
                                        <span class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->service_type ?? 'Standard')) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing Details -->
                            <div class="mt-6 pt-6 border-t">
                                <h4 class="text-md font-medium text-gray-900 mb-4">Pricing Details</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Subtotal</span>
                                        <span class="text-sm text-gray-900">₱{{ number_format($order->subtotal, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Discount</span>
                                        <span class="text-sm text-gray-900">-₱{{ number_format($order->discount, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between font-medium text-gray-900 pt-2 border-t">
                                        <span>Total Amount</span>
                                        <span>₱{{ number_format($order->total_amount, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Amount Paid</span>
                                        <span class="text-sm text-gray-900">₱{{ number_format($order->amount_paid, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between font-medium {{ $order->amount_paid >= $order->total_amount ? 'text-green-600' : 'text-red-600' }}">
                                        <span>Balance</span>
                                        <span>₱{{ number_format($order->total_amount - $order->amount_paid, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Remarks -->
                            @if($order->remarks)
                                <div class="mt-6 pt-6 border-t">
                                    <h4 class="text-md font-medium text-gray-900 mb-2">Special Instructions</h4>
                                    <p class="text-sm text-gray-600">{{ $order->remarks }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load Information -->
            @if($order->loads && $order->loads->count() > 0)
                <div class="bg-white rounded-lg shadow overflow-hidden mx-4 sm:mx-0 mt-6">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-semibold text-gray-900">Load Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($order->loads as $load)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">Load #{{ $loop->index + 1 }}</h4>
                                            <p class="text-sm text-gray-600">{{ $load->weight }} kg</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $load->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                               ($load->status === 'washing' ? 'bg-blue-100 text-blue-800' : 
                                               ($load->status === 'drying' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                            {{ ucfirst($load->status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        @if($load->washerMachine)
                                            <div>
                                                <span class="text-gray-600">Washer:</span>
                                                <span class="text-gray-900">{{ $load->washerMachine->name }}</span>
                                            </div>
                                        @endif
                                        @if($load->dryerMachine)
                                            <div>
                                                <span class="text-gray-600">Dryer:</span>
                                                <span class="text-gray-900">{{ $load->dryerMachine->name }}</span>
                                            </div>
                                        @endif
                                        @if($load->capacity_utilization)
                                            <div>
                                                <span class="text-gray-600">Utilization:</span>
                                                <span class="text-gray-900">{{ number_format($load->capacity_utilization, 1) }}%</span>
                                            </div>
                                        @endif
                                        @if($load->is_consolidated)
                                            <div>
                                                <span class="text-gray-600">Type:</span>
                                                <span class="text-blue-600 font-medium">Consolidated</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($load->notes)
                                        <div class="mt-2 text-sm text-gray-600">
                                            <strong>Notes:</strong> {{ $load->notes }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow overflow-hidden mx-4 sm:mx-0 mt-6">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Order Timeline</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Order Created -->
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">Order Created</div>
                                <div class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y - h:i A') }}</div>
                            </div>
                        </div>

                        <!-- In Progress -->
                        @if($order->status !== 'pending')
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">Order In Progress</div>
                                    <div class="text-sm text-gray-500">Your order is being processed</div>
                                </div>
                            </div>
                        @endif

                        <!-- Ready -->
                        @if(in_array($order->status, ['ready', 'completed']))
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">Ready for Pickup</div>
                                    <div class="text-sm text-gray-500">Your order is ready for pickup</div>
                                </div>
                            </div>
                        @endif

                        <!-- Completed -->
                        @if($order->status === 'completed')
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">Order Completed</div>
                                    <div class="text-sm text-gray-500">{{ $order->finished_at?->format('M d, Y - h:i A') ?? 'N/A' }}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Cancelled -->
                        @if($order->status === 'cancelled')
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">Order Cancelled</div>
                                    <div class="text-sm text-gray-500">This order has been cancelled</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div id="cancelModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Cancel Order</h3>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
                <p class="text-gray-700 mb-4">Are you sure you want to cancel this order? This action cannot be undone.</p>
                <p class="text-sm text-gray-600">Order #{{ $order->id }} will be marked as cancelled and you won't be able to recover it.</p>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex gap-3 justify-end">
                <button type="button" 
                        onclick="closeCancelModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Keep Order
                </button>
                <form action="{{ route('user.orders.cancel', $order) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PUT')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Yes, Cancel Order
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCancelModal() {
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });
    </script>
</x-app-layout>
