<x-sidebar-app>
    <!-- Mobile header offset for fixed sidebar -->
    <div class="pt-16 sm:pt-0 bg-white sm:bg-transparent">
        <div class="py-4 sm:py-6 lg:py-8">
            <div class="max-w-7xl mx-auto px-3 sm:px-5 lg:px-8">
                
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 space-y-3 sm:space-y-0">
                    <div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">My Laundry</h1>
                        <p class="text-sm sm:text-base text-gray-600 mt-1">View and manage your laundry</p>
                    </div>
                    <a href="{{ route('user.orders.create') }}" 
                       class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 sm:py-2 rounded-lg transition flex items-center justify-center sm:justify-start active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm sm:text-base">Add New Laundry</span>
                    </a>
                </div>

                <!-- Stats Cards - Mobile Optimized -->
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <!-- Total Laundry -->
                    <div class="bg-white rounded-lg p-3 sm:p-4 lg:p-6 shadow-sm border border-gray-100">
                        <div class="text-xs sm:text-sm text-gray-500 mb-1">Total Laundry</div>
                        <div class="text-xl sm:text-2xl font-bold text-gray-900">{{ $orders->total() }}</div>
                    </div>
                    
                    <!-- Pending -->
                    <div class="bg-white rounded-lg p-3 sm:p-4 lg:p-6 shadow-sm border border-gray-100">
                        <div class="text-xs sm:text-sm text-gray-500 mb-1">Pending</div>
                        <div class="text-xl sm:text-2xl font-bold text-yellow-600">{{ $orders->where('status', 'pending')->count() }}</div>
                    </div>
                    
                    <!-- In Progress -->
                    <div class="bg-white rounded-lg p-3 sm:p-4 lg:p-6 shadow-sm border border-gray-100">
                        <div class="text-xs sm:text-sm text-gray-500 mb-1">In Progress</div>
                        <div class="text-xl sm:text-2xl font-bold text-blue-600">{{ $orders->whereIn('status', ['picked_up', 'washing', 'drying', 'folding', 'quality_check'])->count() }}</div>
                    </div>
                    
                    <!-- Completed -->
                    <div class="bg-white rounded-lg p-3 sm:p-4 lg:p-6 shadow-sm border border-gray-100">
                        <div class="text-xs sm:text-sm text-gray-500 mb-1">Completed</div>
                        <div class="text-xl sm:text-2xl font-bold text-green-600">{{ $orders->where('status', 'completed')->count() }}</div>
                    </div>
                </div>

                <!-- Orders Table Container -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                    
                    <!-- Table Header with Filters -->
                    <div class="p-4 sm:p-5 lg:p-6 border-b border-gray-200">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">My Laundry</h2>
                            
                            <!-- Filters - Mobile Optimized -->
                            <form method="GET" action="{{ route('user.orders.index') }}" 
                                  class="w-full lg:w-auto space-y-3 sm:space-y-0 sm:flex sm:gap-3">
                                
                                <!-- Sort by -->
                                <div class="w-full sm:w-48">
                                    <label for="sort" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Sort by:</label>
                                    <select id="sort" name="sort" onchange="this.form.submit()"
                                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white appearance-none">
                                        <option value="latest" {{ request('sort') === 'latest' || !request('sort') ? 'selected' : '' }}>Latest First</option>
                                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                        <option value="status_pending" {{ request('sort') === 'status_pending' ? 'selected' : '' }}>Pending Orders</option>
                                        <option value="status_completed" {{ request('sort') === 'status_completed' ? 'selected' : '' }}>Completed Orders</option>
                                        <option value="highest_amount" {{ request('sort') === 'highest_amount' ? 'selected' : '' }}>Highest Amount</option>
                                        <option value="lowest_amount" {{ request('sort') === 'lowest_amount' ? 'selected' : '' }}>Lowest Amount</option>
                                    </select>
                                </div>
                                
                                <!-- Status Filter -->
                                <div class="w-full sm:w-48">
                                    <label for="status" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status:</label>
                                    <select id="status" name="status" onchange="this.form.submit()"
                                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white appearance-none">
                                        <option value="">All Statuses</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="picked_up" {{ request('status') == 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                                        <option value="washing" {{ request('status') == 'washing' ? 'selected' : '' }}>Washing</option>
                                        <option value="drying" {{ request('status') == 'drying' ? 'selected' : '' }}>Drying</option>
                                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                                        <option value="delivery_pending" {{ request('status') == 'delivery_pending' ? 'selected' : '' }}>Delivery Pending</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Orders Table -->
                    @if($orders->count() > 0)
                        <!-- Desktop Table -->
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laundry ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Weight</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Add-ons</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pickup Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($orders as $order)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD-{{ $order->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                       ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                                       (in_array($order->status, ['picked_up', 'washing', 'drying', 'folding', 'quality_check']) ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $order->weight ? $order->weight . ' kg' : 'To be measured' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($order->add_ons && count($order->add_ons) > 0)
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($order->add_ons as $addOn)
                                                            <span class="inline-block px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded border border-blue-100">
                                                                {{ ucfirst(str_replace('_', ' ', $addOn)) }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500">None</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ₱{{ number_format($order->total_amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $order->pickup_date?->format('M d, Y') ?? 'Not set' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $order->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('user.orders.show', $order) }}"
                                                   class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                                                   title="View Details">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 3c-4.5 0-8 4-8 7s3.5 7 8 7 8-4 8-7-3.5-7-8-7zm0 12a5 5 0 110-10 5 5 0 010 10zm0-8a3 3 0 100 6 3 3 0 000-6z"/>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards List -->
                        <div class="lg:hidden divide-y divide-gray-100">
                            @foreach($orders as $order)
                                <div class="p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <div class="font-semibold text-gray-900">#ORD-{{ $order->id }}</div>
                                            <div class="text-sm text-gray-500 mt-1">
                                                Submitted: {{ $order->created_at->format('M d, Y') }}
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                               ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                               (in_array($order->status, ['picked_up', 'washing', 'drying', 'folding', 'quality_check']) ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <div class="text-xs text-gray-500">Weight</div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $order->weight ? $order->weight . ' kg' : 'To be measured' }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500">Amount</div>
                                            <div class="text-sm font-medium text-gray-900">
                                                ₱{{ number_format($order->total_amount, 2) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500">Pickup Date</div>
                                            <div class="text-sm font-medium text-gray-500">
                                                {{ $order->pickup_date?->format('M d, Y') ?? 'Not set' }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500">Add-ons</div>
                                            <div class="text-sm">
                                                @if($order->add_ons && count($order->add_ons) > 0)
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @foreach(array_slice($order->add_ons, 0, 2) as $addOn)
                                                            <span class="inline-block px-1.5 py-0.5 text-xs bg-blue-50 text-blue-700 rounded">
                                                                {{ ucfirst(str_replace('_', ' ', $addOn)) }}
                                                            </span>
                                                        @endforeach
                                                        @if(count($order->add_ons) > 2)
                                                            <span class="inline-block px-1.5 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">
                                                                +{{ count($order->add_ons) - 2 }} more
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-gray-500">None</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-end">
                                        <a href="{{ route('user.orders.show', $order) }}"
                                           class="inline-flex items-center px-4 py-2 text-sm bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 3c-4.5 0-8 4-8 7s3.5 7 8 7 8-4 8-7-3.5-7-8-7zm0 12a5 5 0 110-10 5 5 0 010 10zm0-8a3 3 0 100 6 3 3 0 000-6z"/>
                                            </svg>
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-10 px-4">
                            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No laundry orders yet</h3>
                            <p class="text-gray-500 mb-6 max-w-sm mx-auto">You haven't created any laundry orders. Get started by creating your first order.</p>
                            <a href="{{ route('user.orders.create') }}" 
                               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg transition active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Create Your First Order
                            </a>
                        </div>
                    @endif

                    <!-- Pagination -->
                    @if($orders->hasPages())
                        <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-gray-700">
                                    Showing <span class="font-medium">{{ $orders->firstItem() }}</span> to 
                                    <span class="font-medium">{{ $orders->lastItem() }}</span> of 
                                    <span class="font-medium">{{ $orders->total() }}</span> results
                                </div>
                                <div class="flex items-center space-x-1">
                                    {{ $orders->onEachSide(1)->links('pagination::tailwind') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Mobile FAB for quick action -->
                <a href="{{ route('user.orders.create') }}" 
                   class="lg:hidden fixed bottom-6 right-6 w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg flex items-center justify-center transition-transform hover:scale-105 active:scale-95 z-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</x-sidebar-app>