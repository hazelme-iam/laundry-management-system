<x-sidebar-app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumb Navigation -->
            <x-breadcrumbs :items="['Customers' => null]" />

            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0 px-4 sm:px-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
                    <p class="text-gray-600">Manage your laundry customers</p>
                </div>
                <a href="{{ route('admin.customers.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                    <span class="mr-2">+</span> Add New Customer
                </a>
            </div>

            <!-- Success and Error Messages -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 px-4 sm:px-0">
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Total Customers</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $customers->total() }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Active Today</div>
                    <div class="text-2xl font-bold text-green-600">
                        {{ request('filter') === 'active' ? $customers->total() : '—' }}
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">New This Month</div>
                    <div class="text-2xl font-bold text-blue-600">—</div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Returning Rate</div>
                    <div class="text-2xl font-bold text-purple-600">—</div>
                </div>
            </div>

            <!-- Customers Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden mx-4 sm:mx-0">
                <!-- Search and Filters -->
                <div class="p-4 sm:p-6 border-b">
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                        <input type="text"
                               name="q"
                               value="{{ request('q') }}"
                               placeholder="Search customers..."
                               class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               form="customerFilters">
                        <form id="customerFilters" method="GET" action="{{ route('admin.customers.index') }}" class="flex-1 flex gap-2">
                            <select name="filter" onchange="this.form.submit()"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Customers</option>
                                <option value="new" {{ request('filter') == 'new' ? 'selected' : '' }}>New Customers</option>
                                <option value="returning" {{ request('filter') == 'returning' ? 'selected' : '' }}>Returning Customers</option>
                                <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Active Today</option>
                            </select>

                            @if(request('q'))
                                <input type="hidden" name="q" value="{{ request('q') }}">
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($customers as $customer)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $customer->email ?: 'No email' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $customer->phone ?: '—' }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $customer->address ? Str::limit($customer->address, 30) : 'No address' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            // Priority: VIP label if explicitly set
                                            $isVip = isset($customer->customer_type) && $customer->customer_type === 'vip';
                                            $isWalkInExplicit = isset($customer->customer_type) && $customer->customer_type === 'walk-in';

                                            // Derived from counts (no N+1 queries)
                                            $online = ($customer->online_orders_count ?? 0) > 0;
                                            $walkin = ($customer->walkin_orders_count ?? 0) > 0;
                                        @endphp

                                        @if($isVip)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">VIP</span>
                                        @elseif($isWalkInExplicit || ($walkin && !$online))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Walk-in</span>
                                        @elseif($online)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Online Customer</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Online Customer</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $customer->orders_count ?? ($customer->total_orders ?? 0) }} orders
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₱{{ number_format($customer->orders_sum_total_amount ?? ($customer->total_spent ?? 0), 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if(!empty($customer->last_order_at))
                                            {{ \Illuminate\Support\Carbon::parse($customer->last_order_at)->format('M d, Y') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            @if(isset($customer->is_virtual) && $customer->is_virtual)
                                                <a href="{{ route('admin.customers.show-user', $customer->user_id) }}"
                                                   class="text-blue-600 hover:text-blue-900">View</a>
                                                <span class="text-gray-400">Edit</span>
                                                <span class="text-gray-400">Delete</span>
                                            @else
                                                <!-- View -->
                                                <a href="{{ route('admin.customers.show', $customer) }}"
                                                   class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100"
                                                   title="View customer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path d="M10 3c-4.5 0-8 4-8 7s3.5 7 8 7 8-4 8-7-3.5-7-8-7zm0 12a5 5 0 110-10 5 5 0 010 10zm0-8a3 3 0 100 6 3 3 0 000-6z" />
                                                    </svg>
                                                </a>

                                                <!-- Edit -->
                                                <a href="{{ route('admin.customers.edit', $customer) }}"
                                                   class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-100"
                                                   title="Edit customer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-8.5 8.5A2 2 0 016.5 16H4a1 1 0 01-1-1v-2.5a2 2 0 01.586-1.414l8.5-8.5z" />
                                                    </svg>
                                                </a>

                                                <!-- Delete -->
                                                <button type="button"
                                                        onclick="openDeleteModal('{{ $customer->id }}', '{{ addslashes($customer->name) }}')"
                                                        class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200"
                                                        title="Delete customer">
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
                                        No customers found. <a href="{{ route('admin.customers.create') }}" class="text-blue-600 hover:underline">Add your first customer</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t">
                    {{ $customers->withQueryString()->links() }}
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
    
    // Debug: Check the URL
    console.log('Deleting customer ID:', customerId);
    
    // Update form action with the correct route
    const form = document.getElementById('deleteCustomerForm');
    
    // Use the correct URL format based on your routes
    // If you're using Laravel resource controllers, the URL should be:
    // /admin/customers/{customer}
    form.action = `/admin/customers/${customerId}`;
    
    console.log('Form action set to:', form.action);
    
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
                console.log('Submitting delete form...');
                document.getElementById('deleteCustomerForm').submit();
            });
        }
    }
    
    // Debug helper: Check all customer delete URLs
    const deleteButtons = document.querySelectorAll('button[onclick*="openDeleteModal"]');
    deleteButtons.forEach(button => {
        const onclickAttr = button.getAttribute('onclick');
        console.log('Delete button onclick:', onclickAttr);
    });
});
</script>