<x-sidebar-app>
    <!-- Add the gray background wrapper -->
    <div class="min-h-screen bg-gray-100">
        <div class="py-6 md:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Breadcrumb Navigation -->
                <div class="mb-6">
                    <x-breadcrumbs :items="[
                        'My Orders' => route('user.orders.index'),
                        'Create New Order' => null
                    ]" />
                </div>
                
                <!-- Header Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Create New Order</h1>
                                <p class="text-gray-600 mt-1">Submit a new laundry request</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <form id="orderForm" action="{{ route('user.orders.store') }}" method="POST">
                            @csrf

                            <!-- Customer Information Card -->
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

                            <!-- Contact & Address Section -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Contact Information -->
                                <div>
                                    <label for="customer_phone" class="block text-sm font-medium text-gray-700">
                                        Phone Number (11 digits) *
                                    </label>
                                    <input type="tel" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                           id="customer_phone" name="customer_phone" required
                                           value="{{ old('customer_phone', $customer->phone) }}"
                                           placeholder="09XXXXXXXXX"
                                           maxlength="11">
                                    <div id="phone_error" class="mt-1 text-sm text-red-600 hidden">
                                        Phone number must be exactly 11 digits
                                    </div>
                                    @error('customer_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Format: 09XXXXXXXXX (11 digits total)</p>
                                </div>

                                <!-- Weight Measurement Option -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">How will you provide the weight? *</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="weight_option" value="measure_at_shop" 
                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                                   {{ old('weight_option', 'measure_at_shop') == 'measure_at_shop' ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-700">Measure at shop</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="weight_option" value="manual_weight"
                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                                   {{ old('weight_option') == 'manual_weight' ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-700">I know the exact weight</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Weight Input (conditional) -->
                                <div id="weight_input_container" class="hidden">
                                    <label for="weight" class="block text-sm font-medium text-gray-700">Weight (KG) *</label>
                                    <input type="number" step="0.01" min="0.1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                           id="weight" name="weight" value="{{ old('weight') }}">
                                    @error('weight')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <script>
                                    // Pricing configuration
                                    const addOnPrices = {
                                        'detergent': 16,
                                        'fabric_conditioner': 14
                                    };
                                    const BASE_PRICE = 150;
                                    const BASE_WEIGHT_LIMIT = 5;
                                    const EXCESS_PRICE_PER_KG = 30;

                                    // Calculate total price
                                    function calculateTotalPrice() {
                                        const weight = parseFloat(document.getElementById('weight').value) || 0;
                                        
                                        if (weight < 1) {
                                            resetCalculations();
                                            return;
                                        }

                                        // Calculate base amount
                                        let baseAmount, excessWeight;
                                        
                                        if (weight <= BASE_WEIGHT_LIMIT) {
                                            baseAmount = BASE_PRICE;
                                            excessWeight = 0;
                                        } else {
                                            excessWeight = weight - BASE_WEIGHT_LIMIT;
                                            baseAmount = BASE_PRICE + (excessWeight * EXCESS_PRICE_PER_KG);
                                        }
                                        
                                        // Calculate add-ons total
                                        let addOnsTotal = 0;
                                        const selectedAddOns = document.querySelectorAll('.add-on-checkbox:checked');
                                        selectedAddOns.forEach(checkbox => {
                                            addOnsTotal += addOnPrices[checkbox.value] || 0;
                                        });

                                        // Calculate subtotal
                                        const subtotal = baseAmount + addOnsTotal;

                                        // Update display elements
                                        document.getElementById('base_amount_display').textContent = `₱${baseAmount.toFixed(2)}`;
                                        document.getElementById('excess_weight_display').textContent = `${excessWeight.toFixed(2)} kg`;
                                        document.getElementById('add_ons_total_display').textContent = `₱${addOnsTotal.toFixed(2)}`;
                                        document.getElementById('subtotal_display').textContent = `₱${subtotal.toFixed(2)}`;

                                        // Update form input
                                        document.getElementById('subtotal').value = subtotal.toFixed(2);
                                        document.getElementById('total_amount').value = subtotal.toFixed(2);
                                    }

                                    function resetCalculations() {
                                        const elements = {
                                            'base_amount_display': '₱0.00',
                                            'excess_weight_display': '0 kg',
                                            'add_ons_total_display': '₱0.00',
                                            'subtotal_display': '₱0.00'
                                        };
                                        
                                        Object.keys(elements).forEach(id => {
                                            const element = document.getElementById(id);
                                            if (element) {
                                                element.textContent = elements[id];
                                            }
                                        });
                                        
                                        const subtotal = document.getElementById('subtotal');
                                        const totalAmount = document.getElementById('total_amount');
                                        if (subtotal) subtotal.value = '0';
                                        if (totalAmount) totalAmount.value = '0';
                                    }

                                    // Toggle weight input visibility
                                    function toggleWeightInputNow() {
                                        const weightOption = document.querySelector('input[name="weight_option"]:checked');
                                        const weightContainer = document.getElementById('weight_input_container');
                                        const weightInput = document.getElementById('weight');
                                        
                                        if (!weightOption || !weightContainer || !weightInput) return;
                                        
                                        if (weightOption.value === 'manual_weight') {
                                            weightContainer.classList.remove('hidden');
                                            weightInput.setAttribute('required', 'required');
                                            calculateTotalPrice();
                                        } else {
                                            weightContainer.classList.add('hidden');
                                            weightInput.removeAttribute('required');
                                            weightInput.value = '';
                                            resetCalculations();
                                        }
                                    }
                                    
                                    // Attach listeners
                                    const weightOptions = document.querySelectorAll('input[name="weight_option"]');
                                    weightOptions.forEach(option => {
                                        option.addEventListener('change', toggleWeightInputNow);
                                    });
                                    
                                    // Weight input listener
                                    const weightInput = document.getElementById('weight');
                                    if (weightInput) {
                                        weightInput.addEventListener('input', calculateTotalPrice);
                                    }

                                    // Add-ons listener
                                    const addOnCheckboxes = document.querySelectorAll('.add-on-checkbox');
                                    addOnCheckboxes.forEach(checkbox => {
                                        checkbox.addEventListener('change', calculateTotalPrice);
                                    });

                                    // Form validation function
                                    function validateOrderForm() {
                                        const weightOption = document.querySelector('input[name="weight_option"]:checked')?.value;
                                        const phone = document.getElementById('customer_phone')?.value.trim();
                                        const address = document.getElementById('customer_address')?.value.trim();
                                        const weight = document.getElementById('weight')?.value.trim();
                                        const estimatedFinish = document.getElementById('estimated_finish')?.value.trim();
                                        
                                        // Weight option validation
                                        if (!weightOption) {
                                            alert('Please select how you will provide the weight');
                                            return false;
                                        }
                                        
                                        // Phone validation
                                        if (!phone || phone.length !== 11) {
                                            alert('Phone number must be exactly 11 digits');
                                            document.getElementById('customer_phone')?.focus();
                                            return false;
                                        }
                                        
                                        const phonePattern = /^[0-9]{11}$/;
                                        if (!phonePattern.test(phone)) {
                                            alert('Phone number must contain only numbers');
                                            document.getElementById('customer_phone')?.focus();
                                            return false;
                                        }
                                        
                                        // Address validation
                                        if (!address) {
                                            alert('Please enter your complete address');
                                            document.getElementById('customer_address')?.focus();
                                            return false;
                                        }
                                        
                                        // Weight validation (only if manual_weight selected)
                                        if (weightOption === 'manual_weight') {
                                            if (!weight || parseFloat(weight) < 1) {
                                                alert('Weight must be at least 1kg');
                                                document.getElementById('weight')?.focus();
                                                return false;
                                            }
                                        }
                                        
                                        // Estimated finish validation
                                        if (!estimatedFinish) {
                                            alert('Please select estimated finish date and time');
                                            document.getElementById('estimated_finish')?.focus();
                                            return false;
                                        }
                                        
                                        // Check if estimated finish is in the future
                                        const estimatedFinishDate = new Date(estimatedFinish);
                                        const now = new Date();
                                        if (estimatedFinishDate <= now) {
                                            alert('Estimated finish must be in the future');
                                            document.getElementById('estimated_finish')?.focus();
                                            return false;
                                        }
                                        
                                        // Subtotal validation
                                        const subtotal = parseFloat(document.getElementById('subtotal')?.value) || 0;
                                        if (subtotal <= 0) {
                                            alert('Please enter weight or select add-ons to calculate subtotal');
                                            return false;
                                        }
                                        
                                        // Show confirmation modal with order summary
                                        showOrderConfirmationModal(weightOption, weight, subtotal);
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

                                    // Show confirmation modal with order details
                                    function showOrderConfirmationModal(weightOption, weight, subtotal) {
                                        const weightDisplay = weightOption === 'manual_weight' ? `${weight} kg` : 'To be measured at shop';
                                        const addOnsDisplay = document.querySelectorAll('.add-on-checkbox:checked').length > 0 
                                            ? Array.from(document.querySelectorAll('.add-on-checkbox:checked')).map(cb => cb.nextElementSibling.textContent.trim()).join(', ')
                                            : 'None';
                                        
                                        const modalContent = `
                                            <div class="space-y-3 text-left">
                                                <p class="text-gray-700"><strong>Weight:</strong> ${weightDisplay}</p>
                                                <p class="text-gray-700"><strong>Add-ons:</strong> ${addOnsDisplay}</p>
                                                <p class="text-gray-700"><strong>Subtotal:</strong> ₱${parseFloat(subtotal).toFixed(2)}</p>
                                                <hr class="my-3">
                                                <p class="text-sm text-gray-600">Your order will be submitted for admin approval. Discount and final payment will be handled by the admin.</p>
                                            </div>
                                        `;
                                        
                                        const modal = document.getElementById('createOrderModal');
                                        if (modal) {
                                            const messageElement = modal.querySelector('.confirmation-message');
                                            if (messageElement) {
                                                messageElement.innerHTML = modalContent;
                                            }
                                            openModal('createOrderModal');
                                        }
                                    }
                                    
                                    // Initial state
                                    toggleWeightInputNow();
                                </script>
                            </div>

                            <!-- Address Details Card -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Address Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <!-- Barangay Dropdown -->
                                    <div>
                                        <label for="barangay" class="block text-sm font-medium text-gray-700">Barangay</label>
                                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                id="barangay" name="barangay">
                                            <option value="">Select Barangay</option>
                                            <option value="Poblacion" {{ old('barangay') == 'Poblacion' ? 'selected' : '' }}>Poblacion</option>
                                            <option value="Baluarte" {{ old('barangay') == 'Baluarte' ? 'selected' : '' }}>Baluarte</option>
                                            <option value="Binuangan" {{ old('barangay') == 'Binuangan' ? 'selected' : '' }}>Binuangan</option>
                                            <option value="Gracia" {{ old('barangay') == 'Gracia' ? 'selected' : '' }}>Gracia</option>
                                            <option value="Mohon" {{ old('barangay') == 'Mohon' ? 'selected' : '' }}>Mohon</option>
                                            <option value="Rosario" {{ old('barangay') == 'Rosario' ? 'selected' : '' }}>Rosario</option>
                                            <option value="Santa Ana" {{ old('barangay') == 'Santa Ana' ? 'selected' : '' }}>Santa Ana</option>
                                            <option value="Santo Niño" {{ old('barangay') == 'Santo Niño' ? 'selected' : '' }}>Santo Niño</option>
                                            <option value="Sugbongcogon" {{ old('barangay') == 'Sugbongcogon' ? 'selected' : '' }}>Sugbongcogon</option>
                                        </select>
                                        @error('barangay')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Purok Dropdown -->
                                    <div>
                                        <label for="purok" class="block text-sm font-medium text-gray-700">Purok/Zone</label>
                                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                id="purok" name="purok">
                                            <option value="">Select Purok</option>
                                            <option value="Purok 1" {{ old('purok') == 'Purok 1' ? 'selected' : '' }}>Purok 1</option>
                                            <option value="Purok 2" {{ old('purok') == 'Purok 2' ? 'selected' : '' }}>Purok 2</option>
                                            <option value="Purok 3" {{ old('purok') == 'Purok 3' ? 'selected' : '' }}>Purok 3</option>
                                            <option value="Purok 4" {{ old('purok') == 'Purok 4' ? 'selected' : '' }}>Purok 4</option>
                                            <option value="Purok 5" {{ old('purok') == 'Purok 5' ? 'selected' : '' }}>Purok 5</option>
                                            <option value="Purok 6" {{ old('purok') == 'Purok 6' ? 'selected' : '' }}>Purok 6</option>
                                            <option value="Purok 7" {{ old('purok') == 'Purok 7' ? 'selected' : '' }}>Purok 7</option>
                                            <option value="Purok 8" {{ old('purok') == 'Purok 8' ? 'selected' : '' }}>Purok 8</option>
                                        </select>
                                        @error('purok')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Street Dropdown -->
                                    <div>
                                        <label for="street" class="block text-sm font-medium text-gray-700">Street</label>
                                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                id="street" name="street">
                                            <option value="">Select Street</option>
                                            <option value="Rizal Street" {{ old('street') == 'Rizal Street' ? 'selected' : '' }}>Rizal Street</option>
                                            <option value="Mabini Street" {{ old('street') == 'Mabini Street' ? 'selected' : '' }}>Mabini Street</option>
                                            <option value="Bonifacio Street" {{ old('street') == 'Bonifacio Street' ? 'selected' : '' }}>Bonifacio Street</option>
                                            <option value="Luna Street" {{ old('street') == 'Luna Street' ? 'selected' : '' }}>Luna Street</option>
                                            <option value="Burgos Street" {{ old('street') == 'Burgos Street' ? 'selected' : '' }}>Burgos Street</option>
                                            <option value="Del Pilar Street" {{ old('street') == 'Del Pilar Street' ? 'selected' : '' }}>Del Pilar Street</option>
                                            <option value="Aguinaldo Street" {{ old('street') == 'Aguinaldo Street' ? 'selected' : '' }}>Aguinaldo Street</option>
                                        </select>
                                        @error('street')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Full Address -->
                                <div>
                                    <label for="customer_address" class="block text-sm font-medium text-gray-700">Complete Address Details *</label>
                                    <textarea class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                              id="customer_address" name="customer_address" rows="3" 
                                              placeholder="e.g., House #, Landmarks, Additional directions" required>{{ old('customer_address', $customer->address) }}</textarea>
                                    @error('customer_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Add-ons Section -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Add-ons</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                                    @php
                                        $addOns = [
                                            'detergent' => 'Detergent (+₱16)',
                                            'fabric_conditioner' => 'Fabric Conditioner (+₱14)'
                                        ];
                                    @endphp
                                    @foreach($addOns as $key => $label)
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="add_ons[]" value="{{ $key }}" 
                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 add-on-checkbox">
                                            <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('add_ons')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price Breakdown Card -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Price Breakdown</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm mb-3">
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
                                <div class="text-xs text-gray-500">
                                    <p>📝 <strong>Pricing Rule:</strong> ₱150 for up to 5kg + ₱30 per additional kg</p>
                                </div>
                            </div>

                            <!-- Subtotal (Admin will handle discount, total amount, and payment) -->
                            <div class="mb-6">
                                <label for="subtotal" class="block text-sm font-medium text-gray-700">Subtotal (₱) *</label>
                                <input type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                                       id="subtotal" name="subtotal" value="{{ old('subtotal') }}" required readonly>
                                @error('subtotal')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Discount and final payment will be handled by admin upon approval</p>
                            </div>

                            <!-- Hidden fields for form submission (set by admin) -->
                            <input type="hidden" id="discount" name="discount" value="0">
                            <input type="hidden" id="total_amount" name="total_amount" value="0">
                            <input type="hidden" id="amount_paid" name="amount_paid" value="0">

                            <!-- Dates Section -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="pickup_date" class="block text-sm font-medium text-gray-700">Pickup Date</label>
                                    <input type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                           id="pickup_date" name="pickup_date" value="{{ old('pickup_date') }}">
                                    @error('pickup_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="estimated_finish" class="block text-sm font-medium text-gray-700">Estimated Finish *</label>
                                    <input type="datetime-local" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                           id="estimated_finish" name="estimated_finish" value="{{ old('estimated_finish') }}" required>
                                    @error('estimated_finish')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Priority and Service Type -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                                    <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                            id="priority" name="priority">
                                        <option value="low">Low Priority</option>
                                        <option value="normal" selected>Normal</option>
                                        <option value="high">High Priority</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                    @error('priority')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="service_type" class="block text-sm font-medium text-gray-700">Service Type</label>
                                    <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                            id="service_type" name="service_type">
                                        <option value="standard" selected>Standard Service</option>
                                        <option value="express">Express Service</option>
                                        <option value="premium">Premium Service</option>
                                    </select>
                                    @error('service_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="mb-6">
                                <label for="remarks" class="block text-sm font-medium text-gray-700">Special Instructions / Remarks</label>
                                <textarea class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                          id="remarks" name="remarks" rows="3">{{ old('remarks') }}</textarea>
                                @error('remarks')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Buttons -->
                            <div class="flex space-x-3">
                                <button type="button" 
                                        onclick="validateOrderForm()"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                    Create Order
                                </button>
                                <a href="{{ route('user.orders.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
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
    <div id="createOrderModal" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" onclick="closeModal('createOrderModal')"></div>

        <!-- Modal Container -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Modal Panel -->
            <div class="relative inline-block align-middle bg-white rounded-2xl shadow-2xl transform transition-all w-full sm:max-w-md overflow-hidden" @click.stop>
                
                <!-- Close Button -->
                <button type="button" onclick="closeModal('createOrderModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none rounded-full p-1.5 hover:bg-gray-100">
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
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3 text-center">Create New Order</h3>

                    <!-- Message -->
                    <div class="mt-4 confirmation-message">
                        <p class="text-gray-600 text-center leading-relaxed">Are you sure you want to create this new order? This action will submit your laundry request.</p>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200 sm:flex sm:flex-row-reverse sm:gap-3">
                    <button type="button" onclick="document.getElementById('orderForm').submit();" class="w-full sm:w-auto px-6 py-3 rounded-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        Create Order
                    </button>
                    <button type="button" onclick="closeModal('createOrderModal')" class="mt-3 w-full sm:w-auto px-6 py-3 rounded-lg font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-sidebar-app>

<!-- Include the external JavaScript file -->
@push('scripts')
    <script src="{{ asset('js/order-create.js') }}"></script>
    
    <script>
        // Toggle weight input visibility based on selection
        function toggleWeightInput() {
            const weightOption = document.querySelector('input[name="weight_option"]:checked');
            const weightContainer = document.getElementById('weight_input_container');
            const weightInput = document.getElementById('weight');
            
            if (!weightOption) return;
            
            if (weightOption.value === 'manual_weight') {
                weightContainer.classList.remove('hidden');
                weightInput.setAttribute('required', 'required');
            } else {
                weightContainer.classList.add('hidden');
                weightInput.removeAttribute('required');
                weightInput.value = '';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Attach change listeners to radio buttons
            const weightOptions = document.querySelectorAll('input[name="weight_option"]');
            weightOptions.forEach(option => {
                option.addEventListener('change', toggleWeightInput);
            });
            
            // Initial toggle
            toggleWeightInput();
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
        
        #phone_error {
            display: none;
        }
    </style>
@endpush