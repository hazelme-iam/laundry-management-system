<x-sidebar-app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Laundry Requests</h1>
                    <p class="text-gray-600">Manage your laundry requests</p>
                </div>
                
            </div>

            

            <!-- Order Requests Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <!-- Search / Filters -->
                <div class="p-4 border-b">
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                        <input type="text" 
                               placeholder="Search requests..." 
                               class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <select class="px-8 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>All Statuses</option>
                            <option>Pending</option>
                            <option>Approved</option>
                            <option>Rejected</option>
                            <option>In Progress</option>
                            <option>Ready</option>
                            <option>Completed</option>
                            <option>Cancelled</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Request ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer
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
                            @forelse($requests as $req)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    #{{ $req->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $req->customer->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($req->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($req->status == 'approved') bg-green-100 text-green-800
                                        @elseif($req->status == 'rejected') bg-red-100 text-red-800
                                        @elseif($req->status == 'in_progress') bg-blue-100 text-blue-800
                                        @elseif($req->status == 'ready') bg-indigo-100 text-indigo-800
                                        @elseif($req->status == 'completed') bg-green-100 text-green-800
                                        @elseif($req->status == 'cancelled') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₱{{ number_format($req->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $req->pickup_date?->format('M d, Y') ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <!-- View link -->
                                        <a href="{{ route('admin.laundry_request.show', $req->id) }}" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        
                                        @if($req->status === 'pending')
                                            <!-- Approve button (checkmark) -->
                                            <form action="{{ route('admin.laundry_request.approve', $req->id) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Approve this request and convert it to an order?')">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-green-600 hover:text-green-900 font-bold text-lg"
                                                        title="Approve Request">
                                                    ✓
                                                </button>
                                            </form>
                                            
                                            <!-- Decline button (X) -->
                                            <form action="{{ route('admin.laundry_request.decline', $req->id) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Decline this request?')">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 font-bold text-lg"
                                                        title="Decline Request">
                                                    ✗
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <!-- Edit link for non-pending requests -->
                                        @if($req->status !== 'pending')
                                            <a href="{{ route('admin.laundry_request.edit', $req->id) }}" 
                                               class="text-gray-600 hover:text-gray-900">Edit</a>
                                        @endif
                                        
                                        <!-- Delete link -->
                                        <form action="{{ route('admin.laundry_request.destroy', $req->id) }}" 
                                              method="POST" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Are you sure you want to delete this request?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No laundry requests found. <a href="{{ route('admin.laundry_request.create') }}" class="text-blue-600 hover:underline">Add your first request</a>.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</x-sidebar-app>