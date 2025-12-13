{{-- resources/views/admin/orders/edit.blade.php --}}
<x-sidebar-app>
    <!-- Add the gray background wrapper -->
    <div class="min-h-screen bg-gray-100">
        <div class="py-6 md:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Breadcrumb Navigation -->
                <div class="mb-6">
                    <x-breadcrumbs :items="[
                        'Laundry Management' => route('admin.orders.index'),
                        'Edit Order #' . $order->id => null
                    ]" />
                </div>
                
                <!-- Header Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Edit Order #{{ $order->id }}</h1>
                                <p class="text-gray-600 mt-1">Update order details and payment information</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                    @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'ready_for_pickup') bg-purple-100 text-purple-800
                                    @elseif($order->status == 'completed') bg-green-100 text-green-800
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <form action="{{ route('admin.orders.update', $order) }}" method="POST" id="orderForm">
                            @csrf
                            @method('PUT')

                            <!-- Customer Information Card -->
                            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                                <h3 class="text-sm font-medium text-blue-900 mb-3">Customer Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                                        <div class="text-sm text-gray-900">
                                            <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                    id="customer_id" name="customer_id" required>
                                                <option value="">Select Customer</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}" {{ old('customer_id', $order->customer_id) == $customer->id ? 'selected' : '' }}>
                                                        {{ $customer->name }} - {{ $customer->phone }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('customer_id')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <div class="text-sm text-gray-900" id="customer_phone_display">
                                            {{ $order->customer->phone ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <div class="text-sm text-gray-900" id="customer_email_display">
                                            {{ $order->customer->email ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Details Section -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Weight -->
                                <div>
                                    <label for="weight" class="block text-sm font-medium text-gray-700">Weight (KG) *</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" min="0.1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10" 
                                               id="weight" name="weight" value="{{ old('weight', $order->weight) }}" required 
                                               oninput="calculateTotal()">
                                        <span id="weight_status" class="absolute right-3 top-3 hidden">
                                            <svg id="weight_check" class="w-5 h-5 text-green-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <svg id="weight_x" class="w-5 h-5 text-red-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div id="weight_error" class="mt-1 text-sm text-red-600 hidden flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                                        Weight must be at least 0.1 kg
                                    </div>
                                    @error('weight')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Order Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">Order Status *</label>
                                    <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                            id="status" name="status" required>
                                        <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="ready_for_pickup" {{ old('status', $order->status) == 'ready_for_pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                                        <option value="completed" {{ old('status', $order->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Address Details Card -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Address Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <!-- Barangay Dropdown -->
                                    <div>
                                        <label for="barangay" class="block text-sm font-medium text-gray-700">Barangay *</label>
                                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                id="barangay" name="barangay" required>
                                            <option value="">Select Barangay</option>
                                            <option value="Poblacion" {{ old('barangay', $order->barangay) == 'Poblacion' ? 'selected' : '' }}>Poblacion</option>
                                            <option value="Baluarte" {{ old('barangay', $order->barangay) == 'Baluarte' ? 'selected' : '' }}>Baluarte</option>
                                            <option value="Binuangan" {{ old('barangay', $order->barangay) == 'Binuangan' ? 'selected' : '' }}>Binuangan</option>
                                            <option value="Gracia" {{ old('barangay', $order->barangay) == 'Gracia' ? 'selected' : '' }}>Gracia</option>
                                            <option value="Mohon" {{ old('barangay', $order->barangay) == 'Mohon' ? 'selected' : '' }}>Mohon</option>
                                            <option value="Rosario" {{ old('barangay', $order->barangay) == 'Rosario' ? 'selected' : '' }}>Rosario</option>
                                            <option value="Santa Ana" {{ old('barangay', $order->barangay) == 'Santa Ana' ? 'selected' : '' }}>Santa Ana</option>
                                            <option value="Santo Niño" {{ old('barangay', $order->barangay) == 'Santo Niño' ? 'selected' : '' }}>Santo Niño</option>
                                            <option value="Sugbongcogon" {{ old('barangay', $order->barangay) == 'Sugbongcogon' ? 'selected' : '' }}>Sugbongcogon</option>
                                        </select>
                                        @error('barangay')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Purok Dropdown -->
                                    <div>
                                        <label for="purok" class="block text-sm font-medium text-gray-700">Purok/Zone *</label>
                                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                id="purok" name="purok" required>
                                            <option value="">Select Purok</option>
                                            <option value="Purok 1" {{ old('purok', $order->purok) == 'Purok 1' ? 'selected' : '' }}>Purok 1</option>
                                            <option value="Purok 2" {{ old('purok', $order->purok) == 'Purok 2' ? 'selected' : '' }}>Purok 2</option>
                                            <option value="Purok 3" {{ old('purok', $order->purok) == 'Purok 3' ? 'selected' : '' }}>Purok 3</option>
                                            <option value="Purok 4" {{ old('purok', $order->purok) == 'Purok 4' ? 'selected' : '' }}>Purok 4</option>
                                            <option value="Purok 5" {{ old('purok', $order->purok) == 'Purok 5' ? 'selected' : '' }}>Purok 5</option>
                                            <option value="Purok 6" {{ old('purok', $order->purok) == 'Purok 6' ? 'selected' : '' }}>Purok 6</option>
                                            <option value="Purok 7" {{ old('purok', $order->purok) == 'Purok 7' ? 'selected' : '' }}>Purok 7</option>
                                            <option value="Purok 8" {{ old('purok', $order->purok) == 'Purok 8' ? 'selected' : '' }}>Purok 8</option>
                                        </select>
                                        @error('purok')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Street Dropdown -->
                                    <div>
                                        <label for="street" class="block text-sm font-medium text-gray-700">Street *</label>
                                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                id="street" name="street" required>
                                            <option value="">Select Street</option>
                                            <option value="Rizal Street" {{ old('street', $order->street) == 'Rizal Street' ? 'selected' : '' }}>Rizal Street</option>
                                            <option value="Mabini Street" {{ old('street', $order->street) == 'Mabini Street' ? 'selected' : '' }}>Mabini Street</option>
                                            <option value="Bonifacio Street" {{ old('street', $order->street) == 'Bonifacio Street' ? 'selected' : '' }}>Bonifacio Street</option>
                                            <option value="Luna Street" {{ old('street', $order->street) == 'Luna Street' ? 'selected' : '' }}>Luna Street</option>
                                            <option value="Burgos Street" {{ old('street', $order->street) == 'Burgos Street' ? 'selected' : '' }}>Burgos Street</option>
                                            <option value="Del Pilar Street" {{ old('street', $order->street) == 'Del Pilar Street' ? 'selected' : '' }}>Del Pilar Street</option>
                                            <option value="Aguinaldo Street" {{ old('street', $order->street) == 'Aguinaldo Street' ? 'selected' : '' }}>Aguinaldo Street</option>
                                        </select>
                                        @error('street')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Full Address -->
                                <div>
                                    <label for="customer_address" class="block text-sm font-medium text-gray-700">Complete Address Details *</label>
                                    <div class="relative">
                                        <textarea class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                  id="customer_address" name="customer_address" rows="3" 
                                                  placeholder="e.g., House #, Landmarks, Additional directions" required>{{ old('customer_address', $order->customer_address) }}</textarea>
                                        <span id="address_status" class="absolute right-3 top-3 hidden">
                                            <svg id="address_check" class="w-5 h-5 text-green-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <svg id="address_x" class="w-5 h-5 text-red-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                    @error('customer_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Add-ons Section with Quantity -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Add-ons</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- Detergent -->
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div class="flex-1">
                                            <label class="text-sm font-medium text-gray-700">Detergent</label>
                                            <p class="text-xs text-gray-500">₱16 each</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" id="detergent_minus" class="px-2 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm add-on-btn">−</button>
                                            <input type="number" id="detergent_qty" name="detergent_qty" 
                                                   value="{{ old('detergent_qty', $order->detergent_qty ?? 0) }}" 
                                                   min="0" class="w-12 text-center border border-gray-300 rounded add-on-input" readonly>
                                            <button type="button" id="detergent_plus" class="px-2 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm add-on-btn">+</button>
                                            <span id="detergent_subtotal" class="w-16 text-right font-medium text-gray-900">
                                                ₱{{ number_format(($order->detergent_qty ?? 0) * 16, 2) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Fabric Conditioner -->
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div class="flex-1">
                                            <label class="text-sm font-medium text-gray-700">Fabric Conditioner</label>
                                            <p class="text-xs text-gray-500">₱14 each</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" id="fabric_conditioner_minus" class="px-2 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm add-on-btn">−</button>
                                            <input type="number" id="fabric_conditioner_qty" name="fabric_conditioner_qty" 
                                                   value="{{ old('fabric_conditioner_qty', $order->fabric_conditioner_qty ?? 0) }}" 
                                                   min="0" class="w-12 text-center border border-gray-300 rounded add-on-input" readonly>
                                            <button type="button" id="fabric_conditioner_plus" class="px-2 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm add-on-btn">+</button>
                                            <span id="fabric_conditioner_subtotal" class="w-16 text-right font-medium text-gray-900">
                                                ₱{{ number_format(($order->fabric_conditioner_qty ?? 0) * 14, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Price Breakdown Card -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Price Breakdown</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm mb-3">
                                    <div>
                                        <span class="text-gray-600">Base Price:</span>
                                        <span id="base_amount_display" class="font-medium ml-2">
                                            ₱{{ number_format($order->subtotal - (($order->detergent_qty ?? 0) * 16 + ($order->fabric_conditioner_qty ?? 0) * 14), 2) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Excess Weight:</span>
                                        <span id="excess_weight_display" class="font-medium ml-2">{{ max(0, $order->weight - 5) }} kg</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Add-ons Total:</span>
                                        <span id="add_ons_total_display" class="font-medium ml-2">
                                            ₱{{ number_format(($order->detergent_qty ?? 0) * 16 + ($order->fabric_conditioner_qty ?? 0) * 14, 2) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Subtotal:</span>
                                        <span id="subtotal_display" class="font-medium ml-2 text-blue-600">₱{{ number_format($order->subtotal, 2) }}</span>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <p>📝 <strong>Pricing Rule:</strong> ₱150 for up to 5kg + ₱30 per additional kg</p>
                                </div>
                            </div>

                            <!-- Financial Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                                <!-- Subtotal -->
                                <div>
                                    <label for="subtotal" class="block text-sm font-medium text-gray-700">Subtotal (₱) *</label>
                                    <input type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                                           id="subtotal" name="subtotal" value="{{ old('subtotal', $order->subtotal) }}" required readonly>
                                    @error('subtotal')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Discount -->
                                <div>
                                    <label for="discount" class="block text-sm font-medium text-gray-700">Discount (₱)</label>
                                    <input type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                           id="discount" name="discount" value="{{ old('discount', $order->discount) }}" 
                                           oninput="applyDiscount()">
                                    @error('discount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Total Amount -->
                                <div>
                                    <label for="total_amount" class="block text-sm font-medium text-gray-700">Total Amount (₱) *</label>
                                    <input type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                                           id="total_amount" name="total_amount" value="{{ old('total_amount', $order->total_amount) }}" required readonly>
                                    @error('total_amount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Amount Paid -->
                                <div>
                                    <label for="amount_paid" class="block text-sm font-medium text-gray-700">Amount Paid (₱)</label>
                                    <input type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                           id="amount_paid" name="amount_paid" value="{{ old('amount_paid', $order->amount_paid) }}"
                                           oninput="updateBalance()">
                                    @error('amount_paid')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Payment Information -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <!-- Payment Method (Cash Only) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                                    <div class="mt-1 p-2 bg-gray-50 border border-gray-300 rounded-md">
                                        <span class="text-sm text-gray-900 font-medium">Cash</span>
                                    </div>
                                    <input type="hidden" name="payment_method" value="cash">
                                </div>

                                <!-- Payment Status -->
                                <div>
                                    <label for="cash_payment_status" class="block text-sm font-medium text-gray-700">Payment Status</label>
                                    <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                            id="cash_payment_status" name="cash_payment_status">
                                        <option value="not_paid" {{ old('cash_payment_status', $order->cash_payment_status) == 'not_paid' ? 'selected' : '' }}>Not Paid</option>
                                        <option value="paid" {{ old('cash_payment_status', $order->cash_payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    </select>
                                    @error('cash_payment_status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Balance Display -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Balance (₱)</label>
                                    <div class="mt-1 p-2 bg-gray-100 rounded-md">
                                        @php
                                            $balance = $order->total_amount - $order->amount_paid;
                                        @endphp
                                        <span id="balance_display" class="text-lg font-semibold 
                                            @if($balance == 0) text-green-600
                                            @elseif($balance > 0) text-orange-600
                                            @else text-red-600 @endif">
                                            ₱{{ number_format($balance, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Dates Section -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <!-- Pickup Date -->
                                <div>
                                    <label for="pickup_date" class="block text-sm font-medium text-gray-700">Pickup Date</label>
                                    <input type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                           id="pickup_date" name="pickup_date" value="{{ old('pickup_date', $order->pickup_date ? $order->pickup_date->format('Y-m-d') : '') }}"
                                           min="{{ date('Y-m-d') }}">
                                    @error('pickup_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Estimated Finish -->
                                <div>
                                    <label for="estimated_finish" class="block text-sm font-medium text-gray-700">Estimated Finish *</label>
                                    <input type="datetime-local" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                           id="estimated_finish" name="estimated_finish" value="{{ old('estimated_finish', $order->estimated_finish ? $order->estimated_finish->format('Y-m-d\TH:i') : '') }}" required
                                           min="{{ date('Y-m-d\TH:i') }}">
                                    @error('estimated_finish')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Payment Date -->
                                <div>
                                    <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date</label>
                                    <input type="datetime-local" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                           id="payment_date" name="payment_date" value="{{ old('payment_date', $order->payment_date ? $order->payment_date->format('Y-m-d\TH:i') : '') }}">
                                    @error('payment_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="mb-6">
                                <label for="remarks" class="block text-sm font-medium text-gray-700">Special Instructions / Remarks</label>
                                <textarea class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                          id="remarks" name="remarks" rows="3">{{ old('remarks', $order->remarks) }}</textarea>
                                @error('remarks')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Buttons -->
                            <div class="flex space-x-3">
                                <button type="button" onclick="validateOrderForm()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                    Update Order
                                </button>
                                <a href="{{ route('admin.orders.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="updateOrderModal" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" onclick="closeModal('updateOrderModal')"></div>

        <!-- Modal Container -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Modal Panel -->
            <div class="relative inline-block align-middle bg-white rounded-2xl shadow-2xl transform transition-all w-full sm:max-w-md overflow-hidden" @click.stop>
                
                <!-- Close Button -->
                <button type="button" onclick="closeModal('updateOrderModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none rounded-full p-1.5 hover:bg-gray-100">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Modal Content -->
                <div class="px-8 pt-10 pb-8">
                    <!-- Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-6">
                        <svg class="h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3 text-center">Update Order</h3>

                    <!-- Message -->
                    <div class="mt-4">
                        <p class="text-gray-600 text-center leading-relaxed">Are you sure you want to update this order? Please review all details before confirming.</p>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200 sm:flex sm:flex-row-reverse sm:gap-3">
                    <button type="button" onclick="submitOrderForm()" class="w-full sm:w-auto px-6 py-3 rounded-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        Update Order
                    </button>
                    <button type="button" onclick="closeModal('updateOrderModal')" class="mt-3 w-full sm:w-auto px-6 py-3 rounded-lg font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-sidebar-app>

@push('scripts')
<script>
    // Pricing configuration
    const addOnPrices = {
        'detergent': 16,
        'fabric_conditioner': 14
    };
    const BASE_PRICE = 150;
    const BASE_WEIGHT_LIMIT = 5;
    const EXCESS_PRICE_PER_KG = 30;

    // Quantity control functions
    function updateAddOnSubtotal(addOnType) {
        const qty = parseInt(document.getElementById(`${addOnType}_qty`)?.value) || 0;
        const price = addOnPrices[addOnType] || 0;
        const subtotal = qty * price;
        const subtotalElement = document.getElementById(`${addOnType}_subtotal`);
        if (subtotalElement) {
            subtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
        }
    }

    function increaseQuantity(addOnType) {
        const input = document.getElementById(`${addOnType}_qty`);
        if (input) {
            const currentValue = parseInt(input.value) || 0;
            input.value = currentValue + 1;
            updateAddOnSubtotal(addOnType);
            calculateTotal();
        }
    }

    function decreaseQuantity(addOnType) {
        const input = document.getElementById(`${addOnType}_qty`);
        if (input) {
            const currentValue = parseInt(input.value) || 0;
            if (currentValue > 0) {
                input.value = currentValue - 1;
                updateAddOnSubtotal(addOnType);
                calculateTotal();
            }
        }
    }

    // Calculate total price - FIXED VERSION
    function calculateTotal() {
        const weight = parseFloat(document.getElementById('weight').value) || 0;
        
        // Calculate add-ons total based on quantities
        let addOnsTotal = 0;
        const detergentQty = parseInt(document.getElementById('detergent_qty')?.value) || 0;
        const fabricConditionerQty = parseInt(document.getElementById('fabric_conditioner_qty')?.value) || 0;
        
        addOnsTotal += detergentQty * (addOnPrices['detergent'] || 0);
        addOnsTotal += fabricConditionerQty * (addOnPrices['fabric_conditioner'] || 0);

        // Calculate base amount using the pricing rule
        let baseAmount, excessWeight;
        
        if (weight <= BASE_WEIGHT_LIMIT) {
            // Weight ≤ 5 kg → ₱150
            baseAmount = BASE_PRICE;
            excessWeight = 0;
        } else {
            // Weight > 5 kg → ₱150 + (excess kg × 30)
            excessWeight = weight - BASE_WEIGHT_LIMIT;
            baseAmount = BASE_PRICE + (excessWeight * EXCESS_PRICE_PER_KG);
        }
        
        // Calculate subtotal
        const subtotal = baseAmount + addOnsTotal;

        // Update display elements
        document.getElementById('base_amount_display').textContent = `₱${baseAmount.toFixed(2)}`;
        document.getElementById('excess_weight_display').textContent = `${excessWeight.toFixed(2)} kg`;
        document.getElementById('add_ons_total_display').textContent = `₱${addOnsTotal.toFixed(2)}`;
        document.getElementById('subtotal_display').textContent = `₱${subtotal.toFixed(2)}`;

        // Update form inputs
        document.getElementById('subtotal').value = subtotal.toFixed(2);
        
        // Apply discount if any
        applyDiscount();
    }

    function applyDiscount() {
        const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
        const discount = parseFloat(document.getElementById('discount').value) || 0;
        
        let totalAmount = subtotal - discount;
        totalAmount = totalAmount > 0 ? totalAmount : 0;

        document.getElementById('total_amount').value = totalAmount.toFixed(2);
        
        // Update balance
        updateBalance();
    }

    function updateBalance() {
        const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
        const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
        
        const balance = totalAmount - amountPaid;
        const balanceDisplay = document.getElementById('balance_display');
        
        balanceDisplay.textContent = `₱${balance.toFixed(2)}`;
        
        // Color coding for balance
        if (balance === 0) {
            balanceDisplay.className = 'text-lg font-semibold text-green-600';
        } else if (balance > 0) {
            balanceDisplay.className = 'text-lg font-semibold text-orange-600';
        } else {
            balanceDisplay.className = 'text-lg font-semibold text-red-600';
        }
    }

    // Payment method functionality
    function handlePaymentMethodChange() {
        const paymentStatus = document.getElementById('cash_payment_status').value;
        const amountPaid = document.getElementById('amount_paid');
        const paymentDate = document.getElementById('payment_date');
        
        if (paymentStatus === 'paid') {
            // If payment status is paid, set payment date to now if not already set
            if (!paymentDate.value) {
                const now = new Date();
                const dateStr = now.toISOString().slice(0, 16);
                paymentDate.value = dateStr;
            }
            
            // Ensure amount paid matches total amount
            const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
            if (parseFloat(amountPaid.value) < totalAmount) {
                amountPaid.value = totalAmount.toFixed(2);
            }
        } else {
            // If not paid, clear payment date
            paymentDate.value = '';
        }
        
        updateBalance();
    }

    // Form validation function
    function validateOrderForm() {
        const weight = parseFloat(document.getElementById('weight').value) || 0;
        const customerId = document.getElementById('customer_id').value;
        const status = document.getElementById('status').value;
        const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
        const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
        const estimatedFinish = document.getElementById('estimated_finish').value;
        const paymentStatus = document.getElementById('cash_payment_status').value;
        
        // Basic validations
        if (!customerId) {
            alert('Please select a customer');
            document.getElementById('customer_id').focus();
            return false;
        }
        
        if (weight < 0.1) {
            alert('Weight must be at least 0.1 kg');
            document.getElementById('weight').focus();
            return false;
        }
        
        if (!status) {
            alert('Please select order status');
            document.getElementById('status').focus();
            return false;
        }
        
        if (subtotal <= 0) {
            alert('Subtotal must be greater than 0');
            return false;
        }
        
        if (totalAmount < 0) {
            alert('Total amount cannot be negative');
            return false;
        }
        
        if (!estimatedFinish) {
            alert('Please set estimated finish date and time');
            document.getElementById('estimated_finish').focus();
            return false;
        }
        
        if (!paymentStatus) {
            alert('Please select payment status');
            document.getElementById('cash_payment_status').focus();
            return false;
        }
        
        // Payment method validation
        const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
        const totalAmountValue = parseFloat(document.getElementById('total_amount').value) || 0;
        
        if (paymentStatus === 'paid' && amountPaid < totalAmountValue) {
            alert('Amount paid must be equal to total amount when payment status is "Paid"');
            document.getElementById('amount_paid').focus();
            return false;
        }
        
        // Show confirmation modal
        openModal('updateOrderModal');
        return false;
    }

    // Open modal function
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    // Close modal function
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    // Submit order form
    function submitOrderForm() {
        const form = document.getElementById('orderForm');
        if (form) {
            form.submit();
        } else {
            console.error('Order form not found');
        }
    }

    // Real-time validation functions
    function validateWeightField() {
        const weight = parseFloat(document.getElementById('weight').value) || 0;
        const weightCheck = document.getElementById('weight_check');
        const weightX = document.getElementById('weight_x');
        const weightError = document.getElementById('weight_error');
        const weightStatus = document.getElementById('weight_status');
        
        if (weight >= 0.1) {
            if (weightCheck) weightCheck.classList.remove('hidden');
            if (weightX) weightX.classList.add('hidden');
            if (weightError) weightError.classList.add('hidden');
            if (weightStatus) weightStatus.classList.remove('hidden');
        } else if (document.getElementById('weight')?.value) {
            if (weightCheck) weightCheck.classList.add('hidden');
            if (weightX) weightX.classList.remove('hidden');
            if (weightError) weightError.classList.remove('hidden');
            if (weightStatus) weightStatus.classList.remove('hidden');
        } else {
            if (weightStatus) weightStatus.classList.add('hidden');
            if (weightError) weightError.classList.add('hidden');
        }
    }

    // Update customer information when customer is selected
    document.getElementById('customer_id').addEventListener('change', function() {
        const customerId = this.value;
        if (customerId) {
            // In a real application, you would fetch customer details via AJAX
            // For now, we'll just update from the selected option text
            const selectedOption = this.options[this.selectedIndex];
            const text = selectedOption.text;
            // Extract phone from option text (assuming format: "Name - Phone")
            const phoneMatch = text.match(/-\s*([0-9]+)/);
            if (phoneMatch) {
                document.getElementById('customer_phone_display').textContent = phoneMatch[1];
            }
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initializing edit order form...');
        
        // Set minimum dates
        const today = new Date().toISOString().split('T')[0];
        const now = new Date().toISOString().slice(0, 16);
        
        document.getElementById('pickup_date').min = today;
        document.getElementById('estimated_finish').min = now;
        
        // Attach weight input listener
        const weightInput = document.getElementById('weight');
        if (weightInput) {
            weightInput.addEventListener('input', calculateTotal);
            weightInput.addEventListener('blur', validateWeightField);
        }
        
        // Attach discount input listener
        const discountInput = document.getElementById('discount');
        if (discountInput) {
            discountInput.addEventListener('input', applyDiscount);
        }
        
        // Attach amount paid listener
        const amountPaidInput = document.getElementById('amount_paid');
        if (amountPaidInput) {
            amountPaidInput.addEventListener('input', updateBalance);
        }
        
        // Attach payment status listener
        const paymentStatusInput = document.getElementById('cash_payment_status');
        if (paymentStatusInput) {
            paymentStatusInput.addEventListener('change', handlePaymentMethodChange);
        }
        
        // Initial add-ons subtotal update
        updateAddOnSubtotal('detergent');
        updateAddOnSubtotal('fabric_conditioner');
        
        // Setup add-ons button event listeners
        console.log('Setting up add-on button listeners...');
        
        // Detergent buttons
        const detergentPlusBtn = document.getElementById('detergent_plus');
        const detergentMinusBtn = document.getElementById('detergent_minus');
        
        if (detergentPlusBtn) {
            detergentPlusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                increaseQuantity('detergent');
            });
        }
        
        if (detergentMinusBtn) {
            detergentMinusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                decreaseQuantity('detergent');
            });
        }
        
        // Fabric conditioner buttons
        const fabricPlusBtn = document.getElementById('fabric_conditioner_plus');
        const fabricMinusBtn = document.getElementById('fabric_conditioner_minus');
        
        if (fabricPlusBtn) {
            fabricPlusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                increaseQuantity('fabric_conditioner');
            });
        }
        
        if (fabricMinusBtn) {
            fabricMinusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                decreaseQuantity('fabric_conditioner');
            });
        }
        
        // Calculate initial totals with current order data
        if (weightInput && weightInput.value) {
            console.log('Performing initial calculation with weight:', weightInput.value);
            calculateTotal();
        } else {
            console.log('No weight value found for initial calculation');
        }
        
        // Update balance with existing values
        updateBalance();
    });
</script>
@endpush

@push('styles')
    <style>
        input[readonly] {
            background-color: #f9fafb;
            cursor: not-allowed;
        }
        
        .bg-gray-50 {
            background-color: #f9fafb;
        }
        
        .add-on-btn {
            min-width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .add-on-input {
            background-color: #f9fafb;
        }
        
        .add-on-btn:hover {
            background-color: #e5e7eb !important;
        }
    </style>
@endpush