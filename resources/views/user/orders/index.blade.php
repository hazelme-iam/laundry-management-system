<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0 px-4 sm:px-0">
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 px-4 sm:px-0">
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Total Requests</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $laundryRequests->total() }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Pending Approval</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $laundryRequests->where('status', 'pending')->count() }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Approved</div>
                    <div class="text-2xl font-bold text-green-600">{{ $laundryRequests->where('status', 'approved')->count() }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 sm:p-6 shadow border">
                    <div class="text-sm text-gray-500">Rejected</div>
                    <div class="text-2xl font-bold text-red-600">{{ $laundryRequests->where('status', 'rejected')->count() }}</div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden mx-4 sm:mx-0">
                <div class="p-4 sm:p-6 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">Laundry Requests</h2>
                </div>
                
                <div class="overflow-x-auto">
                    @if($laundryRequests->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Request ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Weight
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Amount
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pickup Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Submitted
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($laundryRequests as $request)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #REQ-{{ $request->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                                   ($request->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $request->weight }} kg
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ₱{{ number_format($request->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $request->pickup_date?->format('M d, Y') ?? 'Not set' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $request->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('user.orders.show', $request) }}" 
                                               class="text-blue-600 hover:text-blue-900">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 mb-4">You haven't submitted any laundry requests yet.</div>
                            <a href="{{ route('user.orders.create') }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition inline-block">
                                Submit Your First Request
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($laundryRequests->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $laundryRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
