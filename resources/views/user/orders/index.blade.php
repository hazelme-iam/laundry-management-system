<x-sidebar-app>
    <div class="py-12 bg-white sm:bg-transparent"> <!-- Added bg-white for mobile -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> <!-- Added px-4 for mobile -->
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">My Orders</h1>
                    <p class="text-gray-600">View and manage your laundry orders</p>
                </div>
                <a href="{{ route('user.orders.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                    <span class="mr-2">+</span> New Order
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Total Orders</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $orders->total() }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Pending</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $orders->where('status', 'pending')->count() }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">In Progress</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $orders->whereIn('status', ['picked_up', 'washing', 'drying', 'folding', 'quality_check'])->count() }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Completed</div>
                    <div class="text-2xl font-bold text-green-600">{{ $orders->where('status', 'completed')->count() }}</div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 sm:p-6 border-b flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="text-lg font-semibold text-gray-900">My Orders</h2>
                    <div class="w-full sm:w-auto">
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-2 sm:mb-0 sm:mr-2 inline-block">Sort by:</label>
                        <select id="sort" onchange="window.location.href = this.value" 
                                class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="{{ route('user.orders.index', ['sort' => 'latest']) }}" {{ request('sort') === 'latest' || !request('sort') ? 'selected' : '' }}>
                                Latest First
                            </option>
                            <option value="{{ route('user.orders.index', ['sort' => 'oldest']) }}" {{ request('sort') === 'oldest' ? 'selected' : '' }}>
                                Oldest First
                            </option>
                            <option value="{{ route('user.orders.index', ['sort' => 'status_pending']) }}" {{ request('sort') === 'status_pending' ? 'selected' : '' }}>
                                Pending Orders
                            </option>
                            <option value="{{ route('user.orders.index', ['sort' => 'status_completed']) }}" {{ request('sort') === 'status_completed' ? 'selected' : '' }}>
                                Completed Orders
                            </option>
                            <option value="{{ route('user.orders.index', ['sort' => 'highest_amount']) }}" {{ request('sort') === 'highest_amount' ? 'selected' : '' }}>
                                Highest Amount
                            </option>
                            <option value="{{ route('user.orders.index', ['sort' => 'lowest_amount']) }}" {{ request('sort') === 'lowest_amount' ? 'selected' : '' }}>
                                Lowest Amount
                            </option>
                        </select>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    @if($orders->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Order ID
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Weight
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Add-ons
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Amount
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pickup Date
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Submitted
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #ORD-{{ $order->id }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                   ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                                   (in_array($order->status, ['picked_up', 'washing', 'drying', 'folding', 'quality_check']) ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $order->weight ? $order->weight . ' kg' : 'To be measured' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($order->add_ons && count($order->add_ons) > 0)
                                                <div class="space-y-1">
                                                    @foreach($order->add_ons as $addOn)
                                                        <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded mr-1">
                                                            {{ ucfirst(str_replace('_', ' ', $addOn)) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-500">None</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ₱{{ number_format($order->total_amount, 2) }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->pickup_date?->format('M d, Y') ?? 'Not set' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('user.orders.show', $order) }}"
                                               class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100"
                                               title="View">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M10 3c-4.5 0-8 4-8 7s3.5 7 8 7 8-4 8-7-3.5-7-8-7zm0 12a5 5 0 110-10 5 5 0 010 10zm0-8a3 3 0 100 6 3 3 0 000-6z" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 mb-4">You haven't created any orders yet.</div>
                            <a href="{{ route('user.orders.create') }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition inline-block">
                                Create Your First Order
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                    <div class="px-4 sm:px-6 py-4 border-t">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-sidebar-app>