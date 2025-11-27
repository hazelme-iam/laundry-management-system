{{-- resources/views/admin/orders/index.blade.php --}}
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Orders</h1>
                    <p class="text-gray-600">Manage your laundry orders</p>
                </div>
                <a href="{{ route('admin.orders.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                    <span class="mr-2">+</span> Add New Order
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg p-4 shadow border">
                    <div class="text-sm text-gray-500">Total Orders</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $orders->total() }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow border">
                    <div class="text-sm text-gray-500">Pending</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow border">
                    <div class="text-sm text-gray-500">In Progress</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $inProgressCount }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow border">
                    <div class="text-sm text-gray-500">Completed</div>
                    <div class="text-2xl font-bold text-green-600">{{ $completedCount }}</div>
                </div>
            </div>

            <!-- Orders Table -->
            <x-table 
                :headers="['Order ID', 'Customer', 'Status', 'Total Amount', 'Pickup Date', 'Actions']"
                :emptyMessage="'No orders found.'"
                :emptyAction="'<a href=\'' . route('admin.orders.create') . '\' class=\'text-blue-600 hover:underline\'>Add your first order</a>'"
            >
                <x-slot name="filters">
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                        <input type="text" 
                               placeholder="Search orders..." 
                               class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <select class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>All Statuses</option>
                            <option>Pending</option>
                            <option>In Progress</option>
                            <option>Ready</option>
                            <option>Completed</option>
                            <option>Cancelled</option>
                        </select>
                    </div>
                </x-slot>

                @foreach($orders as $order)
                    <x-table-row>
                        <x-table-cell>
                            #{{ $order->id }}
                        </x-table-cell>

                        <x-table-cell>
                            {{ $order->customer->name ?? 'N/A' }}
                        </x-table-cell>

                        <x-table-cell>
                            <x-status-dropdown :order="$order" />
                        </x-table-cell>

                        <x-table-cell>
                            ₱{{ number_format($order->total_amount, 2) }}
                        </x-table-cell>

                        <x-table-cell>
                            {{ $order->pickup_date?->format('M d, Y') ?? 'N/A' }}
                        </x-table-cell>

                        <x-table-cell>
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.orders.show', $order) }}" 
                                   class="text-blue-600 hover:text-blue-900">View</a>
                                <a href="{{ route('admin.orders.edit', $order) }}" 
                                   class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Are you sure you want to delete this order?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </x-table-cell>
                    </x-table-row>
                @endforeach

                <x-slot name="pagination">
                    {{ $orders->links() }}
                </x-slot>
            </x-table>
        </div>
    </div>
</x-app-layout>