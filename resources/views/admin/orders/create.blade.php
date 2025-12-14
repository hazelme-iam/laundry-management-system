{{-- resources/views/admin/orders/create.blade.php --}}
<x-sidebar-app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumb Navigation -->
            <x-breadcrumbs :items="[
                'Laundry Management' => route('admin.orders.index'),
                'New Laundry' => null
            ]" />
            
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">New Laundry</h1>

                    <form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Customer Selection -->
                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer </label>
                                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                        id="customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->phone }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Weight -->
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700">Weight (KG) </label>
                                <input type="number" step="0.01" min="0.1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                       id="weight" name="weight" value="{{ old('weight') }}" required 
                                       oninput="calculateTotal()">
                                @error('weight')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                <div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-4">Add-ons</label>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Detergent Card -->
        <div class="border border-gray-200 rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-start mb-4">
                <div class="w-10 h-10 flex items-center justify-center bg-blue-50 rounded-lg mr-3">
                    <span class="text-xl">🧼</span>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Detergent</h3>
                    <p class="text-xs text-gray-600">₱16 per unit</p>
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                    <button type="button" 
                            onclick="decreaseQuantity('detergent')" 
                            class="px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        −
                    </button>
                    <input type="number" 
                           id="detergent_qty" 
                           name="detergent_qty" 
                           value="0" 
                           min="0" 
                           class="w-14 text-center bg-white border-x border-gray-200 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500 font-medium" 
                           readonly>
                    <button type="button" 
                            onclick="increaseQuantity('detergent')" 
                            class="px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        +
                    </button>
                </div>
                
                <div class="text-right">
                    <p class="text-xs text-gray-500">Subtotal</p>
                    <p id="detergent_subtotal" class="text-sm font-bold text-blue-600">₱0.00</p>
                </div>
            </div>
        </div>

        <!-- Fabric Conditioner Card -->
        <div class="border border-gray-200 rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-start mb-4">
                <div class="w-10 h-10 flex items-center justify-center bg-purple-50 rounded-lg mr-3">
                    <span class="text-xl">✨</span>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Fabric Conditioner</h3>
                    <p class="text-xs text-gray-600">₱14 per unit</p>
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                    <button type="button" 
                            onclick="decreaseQuantity('fabric_conditioner')" 
                            class="px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        −
                    </button>
                    <input type="number" 
                           id="fabric_conditioner_qty" 
                           name="fabric_conditioner_qty" 
                           value="0" 
                           min="0" 
                           class="w-14 text-center bg-white border-x border-gray-200 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500 font-medium" 
                           readonly>
                    <button type="button" 
                            onclick="increaseQuantity('fabric_conditioner')" 
                            class="px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        +
                    </button>
                </div>
                
                <div class="text-right">
                    <p class="text-xs text-gray-500">Subtotal</p>
                    <p id="fabric_conditioner_subtotal" class="text-sm font-bold text-purple-600">₱0.00</p>
                </div>
            </div>
        </div>
    </div>
</div>

                            <!-- Price Breakdown -->
                            <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Price Breakdown</h3>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Base Price:</span>
                                        <span id="base_amount_display" class="font-medium ml-2">₱0.00</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Excess Weight:</span>
                                        <span id="excess_weight_display" class="font-medium ml-2">0 kg</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Add-ons Total:</span>
                                        <span id="add_ons_total_display" class="font-medium ml-2">₱0.00</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Subtotal:</span>
                                        <span id="subtotal_display" class="font-medium ml-2 text-blue-600">₱0.00</span>
                                    </div>
                                </div>
                                <div class="mt-2 text-xs text-gray-500">
                                    <p>📝 <strong>Pricing Rule:</strong> ₱150 for up to 5kg + ₱30 per additional kg</p>
                                </div>
                            </div>

                            <!-- Financial Information -->
                            <div>
                                <label for="subtotal" class="block text-sm font-medium text-gray-700">Subtotal (₱) *</label>
                                <input type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                                       id="subtotal" name="subtotal" value="{{ old('subtotal') }}" required readonly>
                                @error('subtotal')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="total_amount" class="block text-sm font-medium text-gray-700">Total Amount (₱) *</label>
                                <input type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                                       id="total_amount" name="total_amount" value="{{ old('total_amount') }}" required readonly>
                                @error('total_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method *</label>
                                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                        id="payment_method" name="payment_method" required onchange="handlePaymentMethod()">
                                    <option value="">Select Payment Method</option>
                                    <option value="pay_now" {{ old('payment_method') === 'pay_now' ? 'selected' : '' }}>Pay Now</option>
                                    <option value="pay_later" {{ old('payment_method') === 'pay_later' ? 'selected' : '' }}>Pay Later</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="amount_paid" class="block text-sm font-medium text-gray-700">Amount Paid (₱)</label>
                                <input type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                       id="amount_paid" name="amount_paid" value="{{ old('amount_paid', 0) }}"
                                       oninput="updateBalance()">
                                @error('amount_paid')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Balance Display -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Balance (₱)</label>
                                <div class="mt-1 p-2 bg-gray-100 rounded-md">
                                    <span id="balance_display" class="text-lg font-semibold">₱0.00</span>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div>
                                <label for="pickup_date" class="block text-sm font-medium text-gray-700">Pickup Date</label>
                                <input type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                       id="pickup_date" name="pickup_date" value="{{ old('pickup_date') }}"
                                       min="{{ date('Y-m-d') }}">
                                @error('pickup_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="estimated_finish" class="block text-sm font-medium text-gray-700">Estimated Finish *</label>
                                <input type="datetime-local" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                       id="estimated_finish" name="estimated_finish" value="{{ old('estimated_finish') }}" required
                                       min="{{ date('Y-m-d\TH:i') }}">
                                @error('estimated_finish')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="mt-6">
                            <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                            <textarea class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                      id="remarks" name="remarks" rows="3">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="mt-6 flex space-x-3">
                            <button type="button" onclick="openModal('createOrderModal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                Add New Laundry
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

    <!-- Confirmation Modal -->
    <x-confirmationmodal 
        :modalId="'createOrderModal'"
        title="Confirm Adding New Laundry"
        message="Are you sure you want to add this laundry? Please review all details before confirming."
        confirmText="Add New Laundry"
        cancelText="Cancel"
        confirmColor="blue"
        formId="orderForm"
        method="POST"
        :showFooter="true"
        :showIcon="true"
    />

    <script>
        // Handle form submission from modal
        document.addEventListener('DOMContentLoaded', function() {
            const orderForm = document.getElementById('orderForm');
            if (orderForm) {
                orderForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Submit the form normally (not via fetch)
                    // This allows Laravel to handle validation and redirects properly
                    this.submit();
                });
            }
        });

        // Add-ons pricing configuration
        const addOnPrices = {
            'detergent': 16,
            'fabric_conditioner': 14
        };

        // Pricing rule constants
        const BASE_PRICE = 150;
        const BASE_WEIGHT_LIMIT = 5;
        const EXCESS_PRICE_PER_KG = 30;

        // Quantity control functions
        function increaseQuantity(addOnType) {
            const input = document.getElementById(`${addOnType}_qty`);
            if (input) {
                input.value = parseInt(input.value) + 1;
                updateAddOnSubtotal(addOnType);
                calculateTotal();
            }
        }

        function decreaseQuantity(addOnType) {
            const input = document.getElementById(`${addOnType}_qty`);
            if (input && parseInt(input.value) > 0) {
                input.value = parseInt(input.value) - 1;
                updateAddOnSubtotal(addOnType);
                calculateTotal();
            }
        }

        function updateAddOnSubtotal(addOnType) {
            const qty = parseInt(document.getElementById(`${addOnType}_qty`)?.value) || 0;
            const price = addOnPrices[addOnType] || 0;
            const subtotal = qty * price;
            const subtotalElement = document.getElementById(`${addOnType}_subtotal`);
            if (subtotalElement) {
                subtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
            }
        }

        function calculateTotal() {
            const weight = parseFloat(document.getElementById('weight').value) || 0;
            
            // Calculate add-ons total based on quantities (always calculate)
            let addOnsTotal = 0;
            const detergentQty = parseInt(document.getElementById('detergent_qty')?.value) || 0;
            const fabricConditionerQty = parseInt(document.getElementById('fabric_conditioner_qty')?.value) || 0;
            
            addOnsTotal += detergentQty * (addOnPrices['detergent'] || 0);
            addOnsTotal += fabricConditionerQty * (addOnPrices['fabric_conditioner'] || 0);

            // Update add-ons display
            document.getElementById('add_ons_total_display').textContent = `₱${addOnsTotal.toFixed(2)}`;
            
            // If weight is not valid, only show add-ons
            if (weight < 1) {
                document.getElementById('base_amount_display').textContent = '₱0.00';
                document.getElementById('excess_weight_display').textContent = '0 kg';
                document.getElementById('subtotal_display').textContent = `₱${addOnsTotal.toFixed(2)}`;
                document.getElementById('subtotal').value = addOnsTotal.toFixed(2);
                document.getElementById('total_amount').value = addOnsTotal.toFixed(2);
                updateBalance();
                return;
            }

            // Calculate base amount using the new pricing rule
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
            document.getElementById('subtotal_display').textContent = `₱${subtotal.toFixed(2)}`;

            // Update form inputs (subtotal = total_amount, no discount)
            document.getElementById('subtotal').value = subtotal.toFixed(2);
            document.getElementById('total_amount').value = subtotal.toFixed(2);
            
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

        function resetCalculations() {
            document.getElementById('base_amount_display').textContent = '₱0.00';
            document.getElementById('excess_weight_display').textContent = '0 kg';
            document.getElementById('add_ons_total_display').textContent = '₱0.00';
            document.getElementById('subtotal_display').textContent = '₱0.00';
            document.getElementById('subtotal').value = '0';
            document.getElementById('total_amount').value = '0';
            document.getElementById('balance_display').textContent = '₱0.00';
            document.getElementById('balance_display').className = 'text-lg font-semibold';
        }

        function handlePaymentMethod() {
            const paymentMethod = document.getElementById('payment_method').value;
            const amountPaidInput = document.getElementById('amount_paid');
            const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
            
            if (paymentMethod === 'pay_now') {
                // Pay Now: Set amount_paid to total_amount
                amountPaidInput.value = totalAmount.toFixed(2);
            } else if (paymentMethod === 'pay_later') {
                // Pay Later: Set amount_paid to 0
                amountPaidInput.value = '0';
            }
            
            // Update balance display
            updateBalance();
        }

        // Initialize calculations when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Set minimum dates
            const today = new Date().toISOString().split('T')[0];
            const now = new Date().toISOString().slice(0, 16);
            
            document.getElementById('pickup_date').min = today;
            document.getElementById('estimated_finish').min = now;
            
            // Initial calculation if there's existing weight value
            const weightInput = document.getElementById('weight');
            if (weightInput && weightInput.value) {
                calculateTotal();
            }
        });

        // Real-time validation for weight
        document.getElementById('weight').addEventListener('blur', function() {
            if (this.value && parseFloat(this.value) < 1) {
                this.setCustomValidity('Weight must be at least 1kg');
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });

        // Show pricing examples when weight input is focused
        document.getElementById('weight').addEventListener('focus', function() {
            console.log('📦 Pricing Examples:');
            console.log('1 kg → ₱150');
            console.log('3 kg → ₱150'); 
            console.log('5 kg → ₱150');
            console.log('6 kg → ₱150 + (1 × 30) = ₱180');
            console.log('10 kg → ₱150 + (5 × 30) = ₱300');
        });
    </script>

    <style>
        input[readonly] {
            background-color: #f9fafb;
            cursor: not-allowed;
        }
        
        .bg-gray-50 {
            background-color: #f9fafb;
        }
    </style>
</x-sidebar-app>