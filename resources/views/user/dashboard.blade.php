<x-sidebar-app>
    <!-- Mobile header offset for fixed sidebar -->
    <div class="pt-16 sm:pt-0 min-h-screen bg-gray-50">
        <div class="py-4 sm:py-6 lg:py-8">
            <div class="max-w-7xl mx-auto px-3 sm:px-5 lg:px-8">
                
                <!-- Header Card - Mobile Optimized -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4 sm:mb-6">
                    <div class="p-4 sm:p-5 lg:p-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4">
                            <div>
                                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">My Dashboard</h1>
                                <p class="text-sm sm:text-base text-gray-600 mt-1 sm:mt-2">Manage your laundry and account</p>
                            </div>
                            <!-- Optional quick stats for mobile -->
                            <div class="md:hidden flex items-center gap-2 pt-2">
                                <div class="text-xs px-2 py-1 bg-blue-50 text-blue-700 rounded-full font-medium">
                                    {{ auth()->user()->orders()->count() ?? 0 }} orders
                                </div>
                                <div class="text-xs px-2 py-1 bg-green-50 text-green-700 rounded-full font-medium">
                                    ₱{{ number_format(auth()->user()->orders()->sum('total_amount') ?? 0, 0) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Stats Cards - Mobile Grid -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
                    <!-- Total Laundry -->
                    <div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-xs font-medium text-gray-500 mb-1">Total Laundry</div>
                                <div class="text-xl font-bold text-gray-900">
                                    {{ auth()->user()->orders()->count() ?? 0 }}
                                </div>
                            </div>
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Active Laundry -->
                    <div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-xs font-medium text-gray-500 mb-1">Active</div>
                                <div class="text-xl font-bold text-blue-600">
                                    {{ auth()->user()->orders()->whereIn('status', ['pending', 'in_progress'])->count() ?? 0 }}
                                </div>
                            </div>
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ready for Pickup -->
                    <div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-xs font-medium text-gray-500 mb-1">Ready</div>
                                <div class="text-xl font-bold text-green-600">
                                    {{ auth()->user()->orders()->where('status', 'ready')->count() ?? 0 }}
                                </div>
                            </div>
                            <div class="p-2 bg-green-50 rounded-lg">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total Spent -->
                    <div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-xs font-medium text-gray-500 mb-1">Total Spent</div>
                                <div class="text-xl font-bold text-purple-600">
                                    ₱{{ number_format(auth()->user()->orders()->sum('total_amount') ?? 0, 0) }}
                                </div>
                            </div>
                            <div class="p-2 bg-purple-50 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 sm:mb-8 overflow-hidden">
                    <!-- Card Header -->
                    <div class="p-5 sm:p-6 border-b border-gray-100">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Recent Laundry</h2>
                            @if(auth()->user()->orders()->count() > 5)
                                <a href="{{ route('user.orders.index') }}" 
                                   class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center">
                                    View All
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Table Content -->
                    <div class="p-4 sm:p-0">
                        @if(auth()->user()->orders()->count() > 0)
                            <!-- Desktop Table -->
                            <div class="hidden sm:block overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laundry ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach(auth()->user()->orders()->latest()->take(5) as $order)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    #{{ $order->id }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                           ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                                           ($order->status === 'ready' ? 'bg-indigo-100 text-indigo-800' : 
                                                           ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'))) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    ₱{{ number_format($order->total_amount, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $order->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('user.orders.show', $order) }}" 
                                                       class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-sm font-medium transition-colors">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Cards List -->
                            <div class="sm:hidden space-y-3">
                                @foreach(auth()->user()->orders()->latest()->take(5) as $order)
                                    <div class="bg-white border border-gray-100 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <div class="font-medium text-gray-900">#{{ $order->id }}</div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $order->created_at->format('M d, Y') }}
                                                </div>
                                            </div>
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                   ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                                   ($order->status === 'ready' ? 'bg-indigo-100 text-indigo-800' : 
                                                   ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'))) }}">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-900">
                                                    ₱{{ number_format($order->total_amount, 2) }}
                                                </span>
                                            </div>
                                            <a href="{{ route('user.orders.show', $order) }}" 
                                               class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center">
                                                View Details
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-8 px-4">
                                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">No laundry orders yet</h3>
                                <p class="text-gray-500 text-sm mb-4 max-w-sm mx-auto">You haven't placed any laundry orders yet.</p>
                                <a href="{{ route('user.orders.create') }}" 
                                   class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition active:scale-95">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Place Your First Order
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions Grid - MOBILE OPTIMIZED -->
                <div class="space-y-4 sm:space-y-0 sm:grid sm:grid-cols-2 lg:grid-cols-3 sm:gap-4 lg:gap-6">
                    <!-- New Laundry - Horizontal Card for Mobile -->
                    <a href="{{ route('user.orders.create') }}" 
                       class="block sm:hidden bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-2xl p-5 hover:shadow-md active:scale-[0.98] transition-all duration-200">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-sm">
                                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 mb-1">New Laundry</h3>
                                <p class="text-sm text-gray-600 mb-3">Place a new laundry order quickly</p>
                                <div class="flex items-center text-blue-700 font-medium text-sm">
                                    <span>Add Now</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Laundry History - Horizontal Card for Mobile -->
                    <a href="{{ route('user.orders.index') }}" 
                       class="block sm:hidden bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-2xl p-5 hover:shadow-md active:scale-[0.98] transition-all duration-200">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-sm">
                                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Laundry History</h3>
                                <p class="text-sm text-gray-600 mb-3">View all your past laundry orders</p>
                                <div class="flex items-center text-green-700 font-medium text-sm">
                                    <span>View History</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- My Profile - Horizontal Card for Mobile -->
                    <a href="{{ route('profile.show') }}" 
                       class="block sm:hidden bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-2xl p-5 hover:shadow-md active:scale-[0.98] transition-all duration-200">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-sm">
                                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 mb-1">My Profile</h3>
                                <p class="text-sm text-gray-600 mb-3">Update your account information</p>
                                <div class="flex items-center text-purple-700 font-medium text-sm">
                                    <span>Edit Profile</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Desktop/Tablet Cards (Hidden on Mobile) -->
                    <!-- New Laundry - Desktop -->
                    <div class="hidden sm:block bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow overflow-hidden">
                        <div class="p-6 text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">New Laundry</h3>
                            <p class="text-sm text-gray-600 mb-4">Place a new laundry order</p>
                            <a href="{{ route('user.orders.create') }}" 
                               class="inline-flex items-center justify-center w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-3 rounded-lg text-sm font-medium transition-all active:scale-95 shadow-sm hover:shadow">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add New Laundry
                            </a>
                        </div>
                    </div>

                    <!-- Laundry History - Desktop -->
                    <div class="hidden sm:block bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow overflow-hidden">
                        <div class="p-6 text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Laundry History</h3>
                            <p class="text-sm text-gray-600 mb-4">View all your past laundry orders</p>
                            <a href="{{ route('user.orders.index') }}" 
                               class="inline-flex items-center justify-center w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-3 rounded-lg text-sm font-medium transition-all active:scale-95 shadow-sm hover:shadow">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                View History
                            </a>
                        </div>
                    </div>

                    <!-- My Profile - Desktop -->
                    <div class="hidden sm:block bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow overflow-hidden">
                        <div class="p-6 text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">My Profile</h3>
                            <p class="text-sm text-gray-600 mb-4">Update your account information</p>
                            <a href="{{ route('profile.show') }}" 
                               class="inline-flex items-center justify-center w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-4 py-3 rounded-lg text-sm font-medium transition-all active:scale-95 shadow-sm hover:shadow">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Edit Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile FAB - Only show when scrolled -->
                <a href="{{ route('user.orders.create') }}" 
                   class="lg:hidden fixed bottom-6 right-6 w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full shadow-xl flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95 z-50">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </a>

            </div>
        </div>
    </div>
</x-sidebar-app>