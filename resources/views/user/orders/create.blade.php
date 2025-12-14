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
                                <!-- Phone Number -->
                                <div>
                                    <label for="customer_phone" class="block text-sm font-medium text-gray-700">Phone Number *</label>
                                    <div class="relative">
                                        <input type="tel" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10" 
                                               id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $customer->phone) }}" 
                                               placeholder="09XXXXXXXXX" required minlength="11" maxlength="11" pattern="[0-9]{11}"
                                               onblur="validatePhoneField()">
                                        <span id="phone_status" class="absolute right-3 top-3 hidden">
                                            <svg id="phone_check" class="w-5 h-5 text-green-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <svg id="phone_x" class="w-5 h-5 text-red-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div id="phone_error" class="mt-1 text-sm text-red-600 hidden flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                                        <span id="phone_error_text">Phone number must be exactly 11 digits</span>
                                    </div>
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
                                    <div class="relative">
                                        <input type="number" step="0.01" min="0.1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10" 
                                               id="weight" name="weight" value="{{ old('weight') }}"
                                               placeholder="e.g., 5.5"
                                               onblur="validateWeightField()">
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
                                    function increaseQuantity(addOnType) {
                                        const input = document.getElementById(`${addOnType}_qty`);
                                        if (input) {
                                            input.value = parseInt(input.value) + 1;
                                            updateAddOnSubtotal(addOnType);
                                            calculateTotalPrice();
                                        }
                                    }

                                    function decreaseQuantity(addOnType) {
                                        const input = document.getElementById(`${addOnType}_qty`);
                                        if (input && parseInt(input.value) > 0) {
                                            input.value = parseInt(input.value) - 1;
                                            updateAddOnSubtotal(addOnType);
                                            calculateTotalPrice();
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

                                    // Calculate total price
                                    function calculateTotalPrice() {
                                        const weightOption = document.querySelector('input[name="weight_option"]:checked')?.value;
                                        const weight = parseFloat(document.getElementById('weight').value) || 0;
                                        
                                        // Calculate add-ons total based on quantities
                                        let addOnsTotal = 0;
                                        const detergentQty = parseInt(document.getElementById('detergent_qty')?.value) || 0;
                                        const fabricConditionerQty = parseInt(document.getElementById('fabric_conditioner_qty')?.value) || 0;
                                        
                                        addOnsTotal += detergentQty * (addOnPrices['detergent'] || 0);
                                        addOnsTotal += fabricConditionerQty * (addOnPrices['fabric_conditioner'] || 0);

                                        // If "measure at shop" is selected, only calculate add-ons
                                        if (weightOption === 'measure_at_shop') {
                                            const subtotal = addOnsTotal;
                                            
                                            document.getElementById('base_amount_display').textContent = `₱0.00`;
                                            document.getElementById('excess_weight_display').textContent = `0 kg`;
                                            document.getElementById('add_ons_total_display').textContent = `₱${addOnsTotal.toFixed(2)}`;
                                            document.getElementById('subtotal_display').textContent = `₱${subtotal.toFixed(2)}`;
                                            
                                            document.getElementById('subtotal').value = subtotal.toFixed(2);
                                            document.getElementById('total_amount').value = subtotal.toFixed(2);
                                            return;
                                        }
                                        
                                        // For manual weight, require weight input
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

                                    // Show blank field notice inline
                                    function showBlankFieldNotice(fieldName, fieldId, errorElementId) {
                                        const field = document.getElementById(fieldId);
                                        const errorElement = document.getElementById(errorElementId);
                                        if (field) {
                                            field.classList.add('border-red-500', 'border-2');
                                            field.focus();
                                        }
                                        if (errorElement) {
                                            errorElement.textContent = `${fieldName} is required. Please fill it out.`;
                                            errorElement.classList.remove('hidden');
                                        }
                                    }
                                    
                                    // Clear blank field notice
                                    function clearBlankFieldNotice(fieldId, errorElementId) {
                                        const field = document.getElementById(fieldId);
                                        const errorElement = document.getElementById(errorElementId);
                                        if (field) {
                                            field.classList.remove('border-red-500', 'border-2');
                                        }
                                        if (errorElement) {
                                            errorElement.classList.add('hidden');
                                        }
                                    }

                                    // Form validation function
                                    function validateOrderForm() {
                                        const weightOption = document.querySelector('input[name="weight_option"]:checked')?.value;
                                        const phone = document.getElementById('customer_phone')?.value.trim();
                                        const address = document.getElementById('customer_address')?.value.trim();
                                        const weight = document.getElementById('weight')?.value.trim();
                                        const detergentQty = parseInt(document.getElementById('detergent_qty')?.value) || 0;
                                        const fabricConditionerQty = parseInt(document.getElementById('fabric_conditioner_qty')?.value) || 0;
                                        
                                        // Weight option validation
                                        if (!weightOption) {
                                            alert('Please select how you will provide the weight');
                                            return false;
                                        }
                                        
                                        // Phone validation
                                        if (!phone) {
                                            showBlankFieldNotice('Phone Number', 'customer_phone', 'phone_error');
                                            return false;
                                        } else {
                                            clearBlankFieldNotice('customer_phone', 'phone_error');
                                        }
                                        
                                        if (phone.length !== 11) {
                                            const phoneErrorText = document.getElementById('phone_error_text');
                                            if (phoneErrorText) phoneErrorText.textContent = 'Phone number must be exactly 11 digits';
                                            document.getElementById('phone_error')?.classList.remove('hidden');
                                            document.getElementById('customer_phone')?.focus();
                                            return false;
                                        }
                                        
                                        const phonePattern = /^[0-9]{11}$/;
                                        if (!phonePattern.test(phone)) {
                                            const phoneErrorText = document.getElementById('phone_error_text');
                                            if (phoneErrorText) phoneErrorText.textContent = 'Phone number must contain only numbers';
                                            document.getElementById('phone_error')?.classList.remove('hidden');
                                            document.getElementById('customer_phone')?.focus();
                                            return false;
                                        }
                                        
                                        // Address validation
                                        if (!address) {
                                            showBlankFieldNotice('Complete Address', 'customer_address', 'address_error');
                                            return false;
                                        } else {
                                            clearBlankFieldNotice('customer_address', 'address_error');
                                        }
                                        
                                        // Weight validation (only if manual_weight selected)
                                        if (weightOption === 'manual_weight') {
                                            if (!weight) {
                                                showBlankFieldNotice('Weight', 'weight', 'weight_error');
                                                return false;
                                            } else {
                                                clearBlankFieldNotice('weight', 'weight_error');
                                            }
                                            if (parseFloat(weight) < 1) {
                                                const weightErrorText = document.getElementById('weight_error');
                                                if (weightErrorText) {
                                                    weightErrorText.textContent = 'Weight must be at least 1kg';
                                                    weightErrorText.classList.remove('hidden');
                                                }
                                                document.getElementById('weight')?.focus();
                                                return false;
                                            }
                                        }
                                        
                                        // Subtotal validation - allow if add-ons are selected OR weight is provided OR measure at shop is selected
                                        const subtotal = parseFloat(document.getElementById('subtotal')?.value) || 0;
                                        const hasAddOns = detergentQty > 0 || fabricConditionerQty > 0;
                                        const hasWeight = weightOption === 'manual_weight' && parseFloat(weight) >= 1;
                                        const isMeasureAtShop = weightOption === 'measure_at_shop';
                                        
                                        if (subtotal <= 0 && !hasAddOns && !hasWeight && !isMeasureAtShop) {
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

                                    // Submit order form
                                    function submitOrderForm() {
                                        const form = document.getElementById('orderForm');
                                        if (form) {
                                            form.submit();
                                        } else {
                                            console.error('Order form not found');
                                        }
                                    }

                                    function confirmCreateOrderSubmit() {
                                        if (typeof window.showPageLoader === 'function') window.showPageLoader();
                                        if (typeof window.showButtonLoader === 'function') window.showButtonLoader('create-order-btn');
                                        submitOrderForm();
                                    }

                                    window.confirmCreateOrderSubmit = confirmCreateOrderSubmit;

                                    function handleCreateOrderClick() {
                                        if (typeof validateOrderForm === 'function') {
                                            if (validateOrderForm()) {
                                                if (typeof window.showPageLoader === 'function') window.showPageLoader();
                                                if (typeof window.showButtonLoader === 'function') window.showButtonLoader('create-order-btn');
                                                if (typeof submitOrderForm === 'function') submitOrderForm();
                                            }
                                        }
                                    }

                                    window.handleCreateOrderClick = handleCreateOrderClick;

                                    // Real-time validation functions
                                    function validatePhoneField() {
                                        const phone = document.getElementById('customer_phone')?.value.trim() || '';
                                        const phoneCheck = document.getElementById('phone_check');
                                        const phoneX = document.getElementById('phone_x');
                                        const phoneError = document.getElementById('phone_error');
                                        const phoneErrorText = document.getElementById('phone_error_text');
                                        const phoneStatus = document.getElementById('phone_status');
                                        
                                        if (phone.length === 11 && /^[0-9]{11}$/.test(phone)) {
                                            phoneCheck?.classList.remove('hidden');
                                            phoneX?.classList.add('hidden');
                                            phoneError?.classList.add('hidden');
                                            phoneStatus?.classList.remove('hidden');
                                        } else if (phone.length === 0) {
                                            phoneStatus?.classList.add('hidden');
                                            phoneError?.classList.add('hidden');
                                        } else if (phone.length > 0 && phone.length < 11) {
                                            phoneCheck?.classList.add('hidden');
                                            phoneX?.classList.remove('hidden');
                                            phoneErrorText.textContent = 'Phone number must be 11 digits (currently ' + phone.length + ' digits)';
                                            phoneError?.classList.remove('hidden');
                                            phoneStatus?.classList.remove('hidden');
                                        } else {
                                            phoneCheck?.classList.add('hidden');
                                            phoneX?.classList.remove('hidden');
                                            phoneErrorText.textContent = 'Phone number must be exactly 11 digits';
                                            phoneError?.classList.remove('hidden');
                                            phoneStatus?.classList.remove('hidden');
                                        }
                                    }

                                    function validateWeightField() {
                                        const weight = parseFloat(document.getElementById('weight')?.value) || 0;
                                        const weightCheck = document.getElementById('weight_check');
                                        const weightX = document.getElementById('weight_x');
                                        const weightError = document.getElementById('weight_error');
                                        const weightStatus = document.getElementById('weight_status');
                                        
                                        if (weight >= 0.1) {
                                            weightCheck?.classList.remove('hidden');
                                            weightX?.classList.add('hidden');
                                            weightError?.classList.add('hidden');
                                            weightStatus?.classList.remove('hidden');
                                        } else if (document.getElementById('weight')?.value) {
                                            weightCheck?.classList.add('hidden');
                                            weightX?.classList.remove('hidden');
                                            weightError?.classList.remove('hidden');
                                            weightStatus?.classList.remove('hidden');
                                        } else {
                                            weightStatus?.classList.add('hidden');
                                            weightError?.classList.add('hidden');
                                        }
                                    }

                                    function validateAddressField() {
                                        const address = document.getElementById('customer_address')?.value.trim() || '';
                                        const addressCheck = document.getElementById('address_check');
                                        const addressX = document.getElementById('address_x');
                                        const addressError = document.getElementById('address_error');
                                        const addressErrorText = document.getElementById('address_error_text');
                                        const addressStatus = document.getElementById('address_status');
                                        
                                        if (address.length > 0) {
                                            addressCheck?.classList.remove('hidden');
                                            addressX?.classList.add('hidden');
                                            addressError?.classList.add('hidden');
                                            addressStatus?.classList.remove('hidden');
                                        } else {
                                            addressStatus?.classList.add('hidden');
                                            addressError?.classList.add('hidden');
                                        }
                                    }

                                    // Show confirmation modal with order details
                                    function showOrderConfirmationModal(weightOption, weight, subtotal) {
                                        const weightDisplay = weightOption === 'manual_weight' ? `${weight} kg` : 'To be measured at shop';
                                        
                                        // Build add-ons display
                                        const detergentQty = parseInt(document.getElementById('detergent_qty')?.value) || 0;
                                        const fabricConditionerQty = parseInt(document.getElementById('fabric_conditioner_qty')?.value) || 0;
                                        
                                        let addOnsDisplay = '';
                                        if (detergentQty > 0) {
                                            addOnsDisplay += `Detergent x${detergentQty} (₱${(detergentQty * 16).toFixed(2)})<br>`;
                                        }
                                        if (fabricConditionerQty > 0) {
                                            addOnsDisplay += `Fabric Conditioner x${fabricConditionerQty} (₱${(fabricConditionerQty * 14).toFixed(2)})<br>`;
                                        }
                                        if (!addOnsDisplay) {
                                            addOnsDisplay = 'None';
                                        }
                                        
                                        const modalContent = `
                                            <div class="space-y-3 text-left">
                                                <p class="text-gray-700"><strong>Weight:</strong> ${weightDisplay}</p>
                                                <p class="text-gray-700"><strong>Add-ons:</strong><br>${addOnsDisplay}</p>
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
                                        <label for="barangay" class="block text-sm font-medium text-gray-700">Barangay *</label>
                                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                id="barangay" name="barangay" required>
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
                                        <label for="purok" class="block text-sm font-medium text-gray-700">Purok/Zone *</label>
                                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                id="purok" name="purok" required>
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
                                        <label for="street" class="block text-sm font-medium text-gray-700">Street *</label>
                                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                id="street" name="street" required>
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
                                    <div class="relative">
                                        <textarea class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                  id="customer_address" name="customer_address" rows="3" 
                                                  placeholder="e.g., House #, Landmarks, Additional directions" required
                                                  onblur="validateAddressField()">{{ old('customer_address', $customer->address) }}</textarea>
                                        <span id="address_status" class="absolute right-3 top-3 hidden">
                                            <svg id="address_check" class="w-5 h-5 text-green-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <svg id="address_x" class="w-5 h-5 text-red-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div id="address_error" class="mt-1 text-sm text-red-600 hidden flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                                        <span id="address_error_text">Please enter your complete address</span>
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
                                            <button type="button" onclick="decreaseQuantity('detergent')" class="px-2 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">−</button>
                                            <input type="number" id="detergent_qty" name="detergent_qty" value="0" min="0" class="w-12 text-center border border-gray-300 rounded" readonly>
                                            <button type="button" onclick="increaseQuantity('detergent')" class="px-2 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">+</button>
                                            <span id="detergent_subtotal" class="w-16 text-right font-medium text-gray-900">₱0.00</span>
                                        </div>
                                    </div>

                                    <!-- Fabric Conditioner -->
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div class="flex-1">
                                            <label class="text-sm font-medium text-gray-700">Fabric Conditioner</label>
                                            <p class="text-xs text-gray-500">₱14 each</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" onclick="decreaseQuantity('fabric_conditioner')" class="px-2 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">−</button>
                                            <input type="number" id="fabric_conditioner_qty" name="fabric_conditioner_qty" value="0" min="0" class="w-12 text-center border border-gray-300 rounded" readonly>
                                            <button type="button" onclick="increaseQuantity('fabric_conditioner')" class="px-2 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">+</button>
                                            <span id="fabric_conditioner_subtotal" class="w-16 text-right font-medium text-gray-900">₱0.00</span>
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

                           <!-- Subtotal and Pickup Date Side by Side -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Subtotal (Admin will handle discount, total amount, and payment) -->
    <div>
        <label for="subtotal" class="block text-sm font-medium text-gray-700">Subtotal (₱) *</label>
        <input type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
               id="subtotal" name="subtotal" value="{{ old('subtotal') }}" required readonly>
        @error('subtotal')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-500">Discount and final payment will be handled by admin upon approval</p>
    </div>

    <!-- Pickup Date -->
    <div>
        <label for="pickup_date" class="block text-sm font-medium text-gray-700">Pickup Date (Optional)</label>
        <input type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
               id="pickup_date" name="pickup_date" value="{{ old('pickup_date') }}">
        @error('pickup_date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-500">When would you like to pick up your laundry? (Admin will set estimated finish date)</p>
    </div>
</div>

<!-- Hidden fields for form submission (set by admin) -->
<input type="hidden" id="discount" name="discount" value="0">
<input type="hidden" id="total_amount" name="total_amount" value="0">
<input type="hidden" id="amount_paid" name="amount_paid" value="0">



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
                    <button type="button" id="create-order-btn" onclick="confirmCreateOrderSubmit();" class="w-full sm:w-auto px-6 py-3 rounded-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all" data-original-text="Create Order">
                        <span data-loader-text>Create Order</span>
                        <span data-loader-spinner class="hidden ml-2 inline-block">
                            <svg class="animate-spin h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
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
        // Handle create order button click with loader
        function handleCreateOrderClick() {
            if (typeof validateOrderForm === 'function') {
                if (validateOrderForm()) {
                    if (typeof showPageLoader === 'function') showPageLoader();
                    if (typeof showButtonLoader === 'function') showButtonLoader('create-order-btn');
                    if (typeof submitOrderForm === 'function') submitOrderForm();
                }
            } else {
                console.error('validateOrderForm function not found');
            }
        }

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