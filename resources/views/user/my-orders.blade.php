<x-sidebar-app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0 px-4 sm:px-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Laundry History</h1>
                    <p class="text-gray-600 mt-1">View all your laundry orders and their status</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <input type="text" placeholder="Search orders..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>
            </div>

            <!-- Orders List -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <!-- Table Header -->
                <div class="grid grid-cols-12 bg-gray-50 px-6 py-3 text-sm font-medium text-gray-700 border-b">
                    <div class="col-span-3">Order ID & Date</div>
                    <div class="col-span-2">Items</div>
                    <div class="col-span-2">Pickup/Delivery</div>
                    <div class="col-span-2">Amount</div>
                    <div class="col-span-2">Status</div>
                    <div class="col-span-1 text-right">Actions</div>
                </div>

                <!-- Order Items -->
                <div class="divide-y divide-gray-200">
                    <!-- Order 1 -->
                    <div class="grid grid-cols-12 items-center px-6 py-4 hover:bg-gray-50">
                        <div class="col-span-3">
                            <p class="font-medium text-gray-900">#ORD-78945</p>
                            <p class="text-sm text-gray-500">Jan 15, 2024</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-900">5 Items</p>
                            <p class="text-sm text-gray-500">3 Shirts, 2 Pants</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm">Pickup: Jan 16</p>
                            <p class="text-sm">Delivery: Jan 18</p>
                        </div>
                        <div class="col-span-2">
                            <p class="font-medium text-gray-900">$45.50</p>
                        </div>
                        <div class="col-span-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                                Delivered
                            </span>
                        </div>
                        <div class="col-span-1 text-right">
                            <button class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                View
                            </button>
                        </div>
                    </div>

                    <!-- Order 2 -->
                    <div class="grid grid-cols-12 items-center px-6 py-4 hover:bg-gray-50">
                        <div class="col-span-3">
                            <p class="font-medium text-gray-900">#ORD-78944</p>
                            <p class="text-sm text-gray-500">Jan 10, 2024</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-900">8 Items</p>
                            <p class="text-sm text-gray-500">5 Shirts, 2 Jeans, 1 Jacket</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm">Pickup: Jan 11</p>
                            <p class="text-sm">Delivery: Jan 13</p>
                        </div>
                        <div class="col-span-2">
                            <p class="font-medium text-gray-900">$68.75</p>
                        </div>
                        <div class="col-span-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                                Delivered
                            </span>
                        </div>
                        <div class="col-span-1 text-right">
                            <button class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                View
                            </button>
                        </div>
                    </div>

                    <!-- Order 3 -->
                    <div class="grid grid-cols-12 items-center px-6 py-4 hover:bg-gray-50">
                        <div class="col-span-3">
                            <p class="font-medium text-gray-900">#ORD-78943</p>
                            <p class="text-sm text-gray-500">Jan 5, 2024</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-900">3 Items</p>
                            <p class="text-sm text-gray-500">2 Bed Sheets, 1 Towel</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm">Pickup: Jan 6</p>
                            <p class="text-sm">Delivery: Jan 8</p>
                        </div>
                        <div class="col-span-2">
                            <p class="font-medium text-gray-900">$32.25</p>
                        </div>
                        <div class="col-span-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                                Delivered
                            </span>
                        </div>
                        <div class="col-span-1 text-right">
                            <button class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                View
                            </button>
                        </div>
                    </div>

                    <!-- Order 4 (Processing) -->
                    <div class="grid grid-cols-12 items-center px-6 py-4 hover:bg-gray-50">
                        <div class="col-span-3">
                            <p class="font-medium text-gray-900">#ORD-78946</p>
                            <p class="text-sm text-gray-500">Today</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-900">6 Items</p>
                            <p class="text-sm text-gray-500">4 Shirts, 1 Pant, 1 Curtain</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm">Pickup: Today</p>
                            <p class="text-sm">Delivery: Jan 20</p>
                        </div>
                        <div class="col-span-2">
                            <p class="font-medium text-gray-900">$52.00</p>
                        </div>
                        <div class="col-span-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span>
                                Processing
                            </span>
                        </div>
                        <div class="col-span-1 text-right">
                            <button class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                Track
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="bg-gray-50 px-6 py-4 border-t">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">4</span> of <span class="font-medium">12</span> orders
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Previous
                            </button>
                            <button class="px-3 py-1 bg-blue-600 border border-blue-600 rounded-md text-sm font-medium text-white hover:bg-blue-700">
                                1
                            </button>
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                2
                            </button>
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                3
                            </button>
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Summary -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900">47</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Completed</p>
                            <p class="text-2xl font-bold text-gray-900">43</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">In Progress</p>
                            <p class="text-2xl font-bold text-gray-900">3</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Spent</p>
                            <p class="text-2xl font-bold text-gray-900">$1,847.50</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-sidebar-app>