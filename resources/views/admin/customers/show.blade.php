<x-sidebar-app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumb Navigation -->
            <x-breadcrumbs :items="[
                'Customers' => route('admin.customers.index'),
                'Customer Details' => null
            ]" />

            <!-- Header with Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 px-4 sm:px-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Customer Details</h1>
                    <p class="text-gray-600">View and manage customer information</p>
                </div>
                <div class="flex space-x-2 mt-4 sm:mt-0">
                    <a href="{{ route('admin.customers.edit', $customer) }}"
                       class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition">
                        Edit Customer
                    </a>
                    <a href="{{ route('admin.customers.index') }}"
                       class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Customer Information Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden mx-4 sm:mx-0">
                <div class="p-6">
                    <!-- Customer Header -->
                    <div class="flex items-center mb-6">
                        <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <div class="ml-6">
                            <h2 class="text-2xl font-bold text-gray-900">{{ $customer->name }}</h2>
                            <div class="flex items-center mt-2 space-x-2">
                                @php
                                    $isVip = isset($customer->customer_type) && $customer->customer_type === 'vip';
                                    $isWalkIn = isset($customer->customer_type) && $customer->customer_type === 'walk-in';
                                    $hasOrders = $customer->orders_count ?? 0 > 0;
                                @endphp

                                @if($isVip)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">VIP Customer</span>
                                @elseif($isWalkIn)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Walk-in Customer</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Regular Customer</span>
                                @endif

                                @if($hasOrders)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $customer->orders_count ?? 0 }} orders
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">No orders yet</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Customer Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Contact Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Contact Information</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Phone Number</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $customer->phone ?? 'Not provided' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Email Address</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $customer->email ?? 'Not provided' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Customer Type</label>
                                <p class="mt-1 text-sm text-gray-900 capitalize">{{ $customer->customer_type ?? 'regular' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Account Created</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $customer->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Address Information</h3>
                            
                            @if($customer->address)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Complete Address</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $customer->address }}</p>
                                </div>
                            @endif

                            @if($customer->barangay)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Barangay</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $customer->barangay }}</p>
                                </div>
                            @endif

                            @if($customer->purok)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Purok/Zone</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $customer->purok }}</p>
                                </div>
                            @endif

                            @if($customer->street)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Street</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $customer->street }}</p>
                                </div>
                            @endif

                            @if(!$customer->address && !$customer->barangay && !$customer->purok && !$customer->street)
                                <div>
                                    <p class="text-sm text-gray-500 italic">No address information provided</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notes Section -->
                    @if($customer->notes)
                        <div class="mt-6 pt-6 border-t">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Notes</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $customer->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Stats Section -->
                    @php
                        $totalSpent = $customer->orders_sum_total_amount ?? 0;
                        $ordersCount = $customer->orders_count ?? 0;
                        $averageOrder = $ordersCount > 0 ? $totalSpent / $ordersCount : 0;
                    @endphp

                    <div class="mt-6 pt-6 border-t">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Statistics</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="text-sm text-blue-600">Total Orders</div>
                                <div class="text-2xl font-bold text-blue-800">{{ $ordersCount }}</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="text-sm text-green-600">Total Spent</div>
                                <div class="text-2xl font-bold text-green-800">₱{{ number_format($totalSpent, 2) }}</div>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4">
                                <div class="text-sm text-purple-600">Average Order Value</div>
                                <div class="text-2xl font-bold text-purple-800">₱{{ number_format($averageOrder, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders (if any) -->
                    @if(isset($customer->orders) && $customer->orders->count() > 0)
                        <div class="mt-6 pt-6 border-t">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Orders</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($customer->orders->take(5) as $order)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm text-gray-900">#{{ $order->id }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $order->created_at->format('M d, Y') }}</td>
                                                <td class="px-4 py-3">
                                                    @php
                                                        $statusColors = [
                                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                                            'processing' => 'bg-blue-100 text-blue-800',
                                                            'ready' => 'bg-green-100 text-green-800',
                                                            'delivered' => 'bg-purple-100 text-purple-800',
                                                            'cancelled' => 'bg-red-100 text-red-800',
                                                        ];
                                                        $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                                    @endphp
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $order->items_count ?? 0 }} items</td>
                                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">₱{{ number_format($order->total_amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($customer->orders->count() > 5)
                                <div class="mt-4 text-center">
                                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View all {{ $customer->orders->count() }} orders →
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="mt-6 pt-6 border-t">
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10v28l12-8 12 8V10H8z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No orders yet</h3>
                                <p class="mt-1 text-sm text-gray-500">This customer hasn't placed any orders yet.</p>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="mt-6 pt-6 border-t flex justify-between">
                        <div class="flex space-x-3">
                            <!-- Add Order Button -->
                            <a href="#"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add New Order
                            </a>
                            
                            <!-- Send Message Button -->
                            <a href="#"
                               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Send Message
                            </a>
                        </div>

                        <!-- Danger Zone -->
                        <div>
                            <button type="button"
                                    onclick="openDeleteModal('{{ $customer->id }}', '{{ addslashes($customer->name) }}')"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring focus:ring-red-300 disabled:opacity-25 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Customer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-confirmationmodal 
        modalId="deleteCustomerModal"
        title="Delete Customer"
        message=""
        confirmText="Delete Customer"
        cancelText="Cancel"
        confirmColor="red"
        :formId="'deleteCustomerForm'"
    />
</x-sidebar-app>

<!-- Hidden Delete Form -->
<form id="deleteCustomerForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
// Function to open delete confirmation modal
function openDeleteModal(customerId, customerName) {
    // Update modal message with customer name
    const modal = document.getElementById('deleteCustomerModal');
    const messageElement = modal.querySelector('.confirmation-message');
    if (messageElement) {
        messageElement.textContent = `Are you sure you want to delete "${customerName}"? This action cannot be undone.`;
    }
    
    // Update form action with the correct route
    const form = document.getElementById('deleteCustomerForm');
    form.action = `/admin/customers/${customerId}`;
    
    // Show the modal
    openModal('deleteCustomerModal');
}

// Handle form submission when confirm button is clicked
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteCustomerModal');
    if (modal) {
        const confirmButton = modal.querySelector('.confirm-button');
        if (confirmButton) {
            // Remove any existing event listeners to prevent duplicates
            const newConfirmButton = confirmButton.cloneNode(true);
            confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);
            
            // Add click event listener
            newConfirmButton.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('deleteCustomerForm').submit();
            });
        }
    }
});
</script>