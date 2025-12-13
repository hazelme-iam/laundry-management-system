{{-- resources/views/admin/orders/index.blade.php --}}
<x-sidebar-app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumb Navigation -->
            <x-breadcrumbs :items="['Laundry Management' => null]" />
            
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0 px-4 sm:px-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Laundry Management</h1>
                    <p class="text-gray-600">Manage customer laundry and track progress</p>
                </div>
                <a href="{{ route('admin.orders.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                    <span class="mr-2">+</span> New Laundry
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 px-4 sm:px-0">
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Total Laundry</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $orders->total() }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border cursor-pointer hover:shadow-md transition-shadow" 
                     onclick="window.location.href='{{ route('admin.orders.pending') }}'">
                    <div class="text-sm text-gray-500">Pending Orders</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</div>
                    <div class="text-xs text-blue-600 mt-1">Click to view →</div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">In Progress</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $inProgressCount }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Completed</div>
                    <div class="text-2xl font-bold text-green-600">{{ $completedCount }}</div>
                </div>
            </div>

            <!-- Error/Success Messages -->
            @if(session('error'))
                <div class="mx-4 sm:mx-0 mb-4">
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="mx-4 sm:mx-0 mb-4">
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Orders Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden mx-4 sm:mx-0">
                <!-- Search / Filters -->
                <!-- resources/views/admin/orders/index.blade.php -->
<!-- Replace the existing filters block with this form -->
<div class="p-4 border-b">
    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
        <input type="text"
               placeholder="Search orders..."
               class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
            <!-- Status filter -->
            <select name="status" onchange="this.form.submit()"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="picked_up" {{ request('status') == 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                <option value="washing" {{ request('status') == 'washing' ? 'selected' : '' }}>Washing</option>
                <option value="drying" {{ request('status') == 'drying' ? 'selected' : '' }}>Drying</option>
                <option value="folding" {{ request('status') == 'folding' ? 'selected' : '' }}>Folding</option>
                <option value="quality_check" {{ request('status') == 'quality_check' ? 'selected' : '' }}>Quality Check</option>
                <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                <option value="delivery_pending" {{ request('status') == 'delivery_pending' ? 'selected' : '' }}>Delivery Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <!-- Source filter -->
            <select name="source" onchange="this.form.submit()"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Sources</option>
                <option value="walk_in" {{ request('source') == 'walk_in' ? 'selected' : '' }}>Walk-in (Admin created)</option>
                <option value="online" {{ request('source') == 'online' ? 'selected' : '' }}>Placed Online</option>
            </select>
        </form>
    </div>
</div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pickup Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $order->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($order->customer)
                                        {{ $order->customer->name }}
                                    @else
                                        Customer #{{ $order->customer_id }} (Missing)
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $isOnline = $order->customer && $order->customer->user_id === $order->created_by;
                                @endphp
                                @if($isOnline)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Online
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                        Walk-in
                                    </span>
                                @endif
                            </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($order->status == 'completed') bg-green-100 text-green-800
                                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                        @elseif($order->status == 'rejected') bg-red-100 text-red-800
                                        @elseif(in_array($order->status, ['picked_up', 'washing', 'drying', 'folding', 'quality_check'])) bg-blue-100 text-blue-800
                                        @elseif($order->status == 'ready') bg-indigo-100 text-indigo-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₱{{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->pickup_date?->format('M d, Y') ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
    <div class="flex items-center space-x-2">
        <!-- View -->
        <a href="{{ route('admin.orders.show', $order) }}"
           class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100"
           title="View">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10 3c-4.5 0-8 4-8 7s3.5 7 8 7 8-4 8-7-3.5-7-8-7zm0 12a5 5 0 110-10 5 5 0 010 10zm0-8a3 3 0 100 6 3 3 0 000-6z" />
            </svg>
        </a>

        @if($order->status === 'pending')
            <!-- Approve -->
            <button type="button"
                    onclick="openModal('approveOrderModal{{ $order->id }}')"
                    class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-green-50 text-green-600 hover:bg-green-100"
                    title="Approve">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.172 7.707 8.879A1 1 0 106.293 10.293l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Decline -->
            <button type="button"
                    onclick="openModal('declineOrderModal{{ $order->id }}')"
                    class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-red-50 text-red-600 hover:bg-red-100"
                    title="Decline">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.293 7.293a1 1 0 011.414 0L10 7.586l.293-.293a1 1 0 111.414 1.414L11.414 9l.293.293a1 1 0 01-1.414 1.414L10 10.414l-.293.293a1 1 0 01-1.414-1.414L8.586 9l-.293-.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>

        @elseif($order->status === 'approved')
            <!-- Laundry Received -->
            <button onclick="markAsPickedUp({{ $order->id }})"
                    class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100"
                    title="Laundry Received">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                </svg>
            </button>
        @else
            <!-- Edit -->
            <a href="{{ route('admin.orders.edit', $order) }}"
               class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-100"
               title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-8.5 8.5A2 2 0 016.5 16H4a1 1 0 01-1-1v-2.5a2 2 0 01.586-1.414l8.5-8.5z" />
                </svg>
            </a>
        @endif

        @if($order->status !== 'approved')
            <!-- Delete -->
            <button type="button"
                    onclick="openModal('deleteOrderModal{{ $order->id }}')"
                    class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200"
                    title="Delete">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 100 2h12a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM5 8a1 1 0 011-1h8a1 1 0 011 1v7a3 3 0 01-3 3H8a3 3 0 01-3-3V8z" clip-rule="evenodd" />
                </svg>
            </button>
        @endif
    </div>
</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No orders found. <a href="{{ route('admin.orders.create') }}" class="text-blue-600 hover:underline">Add your first order</a>.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Confirmation Modals for each order -->
                @foreach($orders as $order)
                    @if($order->status === 'pending')
                        <!-- Approve Modal -->
                        <x-confirmationmodal 
                            :modalId="'approveOrderModal' . $order->id"
                            title="Confirm Order Approval"
                            message="Are you sure you want to approve this order? This action will move the order to the next stage."
                            confirmText="Approve"
                            cancelText="Cancel"
                            confirmColor="green"
                            :confirmAction="route('admin.orders.approve', $order)"
                            method="POST"
                            :showFooter="true"
                            :showIcon="true"
                        />

                        <!-- Decline Modal -->
                        <x-confirmationmodal 
                            :modalId="'declineOrderModal' . $order->id"
                            title="Confirm Order Decline"
                            message="Are you sure you want to decline this order? This action cannot be undone."
                            confirmText="Decline"
                            cancelText="Cancel"
                            confirmColor="red"
                            :confirmAction="route('admin.orders.decline', $order)"
                            method="POST"
                            :showFooter="true"
                            :showIcon="true"
                        />
                    @endif

                    @if($order->status !== 'approved')
                        <!-- Delete Modal -->
                        <x-confirmationmodal 
                            :modalId="'deleteOrderModal' . $order->id"
                            title="Confirm Order Deletion"
                            message="Are you sure you want to delete this order? This action cannot be undone."
                            confirmText="Delete"
                            cancelText="Cancel"
                            confirmColor="red"
                            :confirmAction="route('admin.orders.destroy', $order)"
                            method="DELETE"
                            :showFooter="true"
                            :showIcon="true"
                        />
                    @endif
                @endforeach

                <!-- Pagination -->
                @if($orders->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function markAsPickedUp(orderId) {
            if (confirm('Mark this order as laundry received?')) {
                fetch(`/admin/orders/${orderId}/update-status`, {
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
                    alert('An error occurred. Please try again.');
                });
            }
        }
    </script>
</x-sidebar-app>