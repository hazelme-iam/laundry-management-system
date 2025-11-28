<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0 px-4 sm:px-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">My Dashboard</h1>
                    <p class="text-gray-600">Manage your laundry orders and account</p>
                </div>
            </div>

            <!-- User Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 px-4 sm:px-0">
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Total Orders</div>
                    <div class="text-2xl font-bold text-gray-800">{{ auth()->user()->orders()->count() ?? 0 }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Active Orders</div>
                    <div class="text-2xl font-bold text-blue-600">{{ auth()->user()->orders()->whereIn('status', ['pending', 'in_progress'])->count() ?? 0 }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Ready for Pickup</div>
                    <div class="text-2xl font-bold text-green-600">{{ auth()->user()->orders()->where('status', 'ready')->count() ?? 0 }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Total Spent</div>
                    <div class="text-2xl font-bold text-purple-600">₱{{ number_format(auth()->user()->orders()->sum('total_amount') ?? 0, 2) }}</div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow overflow-hidden mx-4 sm:mx-0">
                <div class="p-4 sm:p-6 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
                </div>
                
                <div class="overflow-x-auto">
                    @if(auth()->user()->orders()->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Order ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach(auth()->user()->orders()->latest()->take(5) as $order)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ $order->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                   ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                                   ($order->status === 'ready' ? 'bg-indigo-100 text-indigo-800' : 
                                                   ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'))) }}">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ₱{{ number_format($order->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('user.orders.show', $order) }}" 
                                               class="text-blue-600 hover:text-blue-900">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 mb-4">You haven't placed any orders yet.</div>
                            <a href="{{ route('user.orders.create') }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition inline-block">
                                Place Your First Order
                            </a>
                        </div>
                    @endif
                </div>

                @if(auth()->user()->orders()->count() > 5)
                    <div class="px-6 py-4 border-t">
                        <a href="{{ route('user.orders.index') }}" 
                           class="text-blue-600 hover:text-blue-900 font-medium">
                            View All Orders →
                        </a>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 px-4 sm:px-0">
                <div class="bg-white rounded-lg p-6 shadow border text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">New Order</h3>
                    <p class="text-gray-600 text-sm mb-4">Place a new laundry order</p>
                    <a href="{{ route('user.orders.create') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition inline-block">
                        Create Order
                    </a>
                </div>

                <div class="bg-white rounded-lg p-6 shadow border text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Order History</h3>
                    <p class="text-gray-600 text-sm mb-4">View all your past orders</p>
                    <a href="{{ route('user.orders.index') }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition inline-block">
                        View History
                    </a>
                </div>

                <div class="bg-white rounded-lg p-6 shadow border text-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">My Profile</h3>
                    <p class="text-gray-600 text-sm mb-4">Update your account information</p>
                    <a href="{{ route('profile.show') }}" 
                       class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition inline-block">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
