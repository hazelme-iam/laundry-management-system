<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 px-4 sm:px-0">
                <h1 class="text-2xl font-bold text-gray-900">Create New Order</h1>
                <p class="text-gray-600">Fill in the details to place a new laundry order</p>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden mx-4 sm:mx-0">
                <form action="{{ route('user.orders.store') }}" method="POST" class="p-6 sm:p-8">
                    @csrf

                    <!-- Customer Information -->
                    <div class="bg-blue-50 rounded-lg p-4 mb-6">
                        <h3 class="text-sm font-medium text-blue-900 mb-3">Your Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <div class="text-sm text-gray-900">{{ auth()->user()->name }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <div class="text-sm text-gray-900">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="customer_phone" name="customer_phone" required
                                   value="{{ old('customer_phone', $customer->phone) }}"
                                   placeholder="Enter your phone number"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('customer_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-2">
                                Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="customer_address" name="customer_address" required
                                   value="{{ old('customer_address', $customer->address) }}"
                                   placeholder="Enter your address"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('customer_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Order Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Weight -->
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                                Weight (kg) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="weight" name="weight" step="0.1" min="0.1" required
                                   value="{{ old('weight') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('weight')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estimated Finish -->
                        <div>
                            <label for="estimated_finish" class="block text-sm font-medium text-gray-700 mb-2">
                                Estimated Finish Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="estimated_finish" name="estimated_finish" required
                                   value="{{ old('estimated_finish') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('estimated_finish')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Pickup Date -->
                    <div class="mb-6">
                        <label for="pickup_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Pickup Date (Optional)
                        </label>
                        <input type="date" id="pickup_date" name="pickup_date"
                               value="{{ old('pickup_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('pickup_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pricing Section -->
                    <div class="border-t pt-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing Details</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Subtotal -->
                            <div>
                                <label for="subtotal" class="block text-sm font-medium text-gray-700 mb-2">
                                    Subtotal <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="subtotal" name="subtotal" step="0.01" min="0" required
                                       value="{{ old('subtotal') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('subtotal')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Discount -->
                            <div>
                                <label for="discount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Discount
                                </label>
                                <input type="number" id="discount" name="discount" step="0.01" min="0"
                                       value="{{ old('discount', 0) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('discount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Amount -->
                            <div>
                                <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Total Amount <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="total_amount" name="total_amount" step="0.01" min="0" required
                                       value="{{ old('total_amount') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('total_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount Paid -->
                            <div>
                                <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-2">
                                    Amount Paid <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="amount_paid" name="amount_paid" step="0.01" min="0" required
                                       value="{{ old('amount_paid') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('amount_paid')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="mb-6">
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                            Special Instructions / Remarks
                        </label>
                        <textarea id="remarks" name="remarks" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('user.orders.index') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Create Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
