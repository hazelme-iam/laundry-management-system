// public/js/order-create.js

// Add-ons pricing configuration
const addOnPrices = {
    'detergent': 16,
    'fabric_conditioner': 14
};

// Pricing rule constants
const BASE_PRICE = 150;
const BASE_WEIGHT_LIMIT = 5;
const EXCESS_PRICE_PER_KG = 30;

// Phone validation function
function validatePhoneLength() {
    const phoneInput = document.getElementById('customer_phone');
    const phoneError = document.getElementById('phone_error');
    
    if (phoneInput && phoneError) {
        if (phoneInput.value.length > 0 && phoneInput.value.length !== 11) {
            phoneError.classList.remove('hidden');
            return false;
        } else {
            phoneError.classList.add('hidden');
            return true;
        }
    }
    return true;
}

function calculateTotal() {
    const weightInput = document.getElementById('weight');
    if (!weightInput) return;
    
    const weight = parseFloat(weightInput.value) || 0;
    
    if (weight < 1) {
        resetCalculations();
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
    
    // Calculate add-ons total
    let addOnsTotal = 0;
    const selectedAddOns = document.querySelectorAll('.add-on-checkbox:checked');
    selectedAddOns.forEach(checkbox => {
        addOnsTotal += addOnPrices[checkbox.value] || 0;
    });

    // Calculate subtotal
    const subtotal = baseAmount + addOnsTotal;

    // Update display elements
    const baseAmountDisplay = document.getElementById('base_amount_display');
    const excessWeightDisplay = document.getElementById('excess_weight_display');
    const addOnsTotalDisplay = document.getElementById('add_ons_total_display');
    const subtotalDisplay = document.getElementById('subtotal_display');
    const subtotalInput = document.getElementById('subtotal');
    
    if (baseAmountDisplay) baseAmountDisplay.textContent = `₱${baseAmount.toFixed(2)}`;
    if (excessWeightDisplay) excessWeightDisplay.textContent = `${excessWeight.toFixed(2)} kg`;
    if (addOnsTotalDisplay) addOnsTotalDisplay.textContent = `₱${addOnsTotal.toFixed(2)}`;
    if (subtotalDisplay) subtotalDisplay.textContent = `₱${subtotal.toFixed(2)}`;
    if (subtotalInput) subtotalInput.value = subtotal.toFixed(2);
    
    // Apply discount if any
    applyDiscount();
}

function applyDiscount() {
    const subtotalInput = document.getElementById('subtotal');
    const discountInput = document.getElementById('discount');
    const totalAmountInput = document.getElementById('total_amount');
    
    if (!subtotalInput || !discountInput || !totalAmountInput) return;
    
    const subtotal = parseFloat(subtotalInput.value) || 0;
    const discount = parseFloat(discountInput.value) || 0;
    
    let totalAmount = subtotal - discount;
    totalAmount = totalAmount > 0 ? totalAmount : 0;

    totalAmountInput.value = totalAmount.toFixed(2);
    
    // Update balance
    updateBalance();
}

function updateBalance() {
    const totalAmountInput = document.getElementById('total_amount');
    const amountPaidInput = document.getElementById('amount_paid');
    const balanceDisplay = document.getElementById('balance_display');
    
    if (!totalAmountInput || !amountPaidInput || !balanceDisplay) return;
    
    const totalAmount = parseFloat(totalAmountInput.value) || 0;
    const amountPaid = parseFloat(amountPaidInput.value) || 0;
    
    const balance = totalAmount - amountPaid;
    
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
    const elements = {
        'base_amount_display': '₱0.00',
        'excess_weight_display': '0 kg',
        'add_ons_total_display': '₱0.00',
        'subtotal_display': '₱0.00',
        'subtotal': '0',
        'total_amount': '0'
    };
    
    Object.keys(elements).forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            if (element.tagName === 'INPUT') {
                element.value = elements[id];
            } else {
                element.textContent = elements[id];
            }
        }
    });
    
    const balanceDisplay = document.getElementById('balance_display');
    if (balanceDisplay) {
        balanceDisplay.textContent = '₱0.00';
        balanceDisplay.className = 'text-lg font-semibold';
    }
}

// Form validation function
function validateOrderForm() {
    const phone = document.getElementById('customer_phone')?.value.trim();
    const address = document.getElementById('customer_address')?.value.trim();
    const weight = document.getElementById('weight')?.value.trim();
    const estimatedFinish = document.getElementById('estimated_finish')?.value.trim();
    
    // Phone validation - exactly 11 digits
    if (!phone || phone.length !== 11) {
        const phoneError = document.getElementById('phone_error');
        if (phoneError) phoneError.classList.remove('hidden');
        alert('Phone number must be exactly 11 digits');
        document.getElementById('customer_phone')?.focus();
        return false;
    }
    
    // Phone validation - only numbers
    const phonePattern = /^[0-9]{11}$/;
    if (!phonePattern.test(phone)) {
        alert('Phone number must contain only numbers (11 digits)');
        document.getElementById('customer_phone')?.focus();
        return false;
    }
    
    // Address validation
    if (!address) {
        alert('Please enter your address');
        document.getElementById('customer_address')?.focus();
        return false;
    }
    
    // Weight validation (minimum 1kg)
    if (!weight || parseFloat(weight) < 1) {
        alert('Weight must be at least 1kg');
        document.getElementById('weight')?.focus();
        return false;
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
    
    // Calculate order summary for confirmation message
    const subtotal = parseFloat(document.getElementById('subtotal')?.value) || 0;
    const discount = parseFloat(document.getElementById('discount')?.value) || 0;
    const totalAmount = parseFloat(document.getElementById('total_amount')?.value) || 0;
    const amountPaid = parseFloat(document.getElementById('amount_paid')?.value) || 0;
    const balance = totalAmount - amountPaid;
    
    // Update modal message with order summary
    const modal = document.getElementById('createOrderModal');
    const messageElement = modal?.querySelector('.confirmation-message');
    if (messageElement) {
        messageElement.innerHTML = `
            <div class="space-y-2">
                <p>Are you sure you want to create this new order?</p>
                <div class="bg-gray-50 p-3 rounded-md mt-3">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="font-medium">Weight:</div>
                        <div>${weight} kg</div>
                        
                        <div class="font-medium">Subtotal:</div>
                        <div>₱${subtotal.toFixed(2)}</div>
                        
                        ${discount > 0 ? `
                            <div class="font-medium">Discount:</div>
                            <div>-₱${discount.toFixed(2)}</div>
                        ` : ''}
                        
                        <div class="font-medium">Total Amount:</div>
                        <div class="font-semibold">₱${totalAmount.toFixed(2)}</div>
                        
                        <div class="font-medium">Amount Paid:</div>
                        <div>₱${amountPaid.toFixed(2)}</div>
                        
                        <div class="font-medium">Balance:</div>
                        <div class="${balance === 0 ? 'text-green-600' : balance > 0 ? 'text-orange-600' : 'text-red-600'} font-semibold">
                            ₱${balance.toFixed(2)}
                        </div>
                    </div>
                </div>
                <p class="mt-3 text-sm text-gray-600">This action will submit your laundry request.</p>
            </div>
        `;
    }
    
    // Show the confirmation modal
    if (typeof openModal === 'function') {
        openModal('createOrderModal');
    }
    return true;
}

// Initialize the order form
function initializeOrderForm() {
    // Set minimum dates
    const today = new Date().toISOString().split('T')[0];
    const now = new Date().toISOString().slice(0, 16);
    
    const pickupDateInput = document.getElementById('pickup_date');
    const estimatedFinishInput = document.getElementById('estimated_finish');
    
    if (pickupDateInput) pickupDateInput.min = today;
    if (estimatedFinishInput) estimatedFinishInput.min = now;
    
    // Initial calculation if there's existing weight value
    const weightInput = document.getElementById('weight');
    if (weightInput && weightInput.value) {
        calculateTotal();
    }
    
    // Validate phone on load if there's a value
    validatePhoneLength();
    
    // Event listeners
    if (weightInput) {
        // Real-time validation for weight
        weightInput.addEventListener('blur', function() {
            if (this.value && parseFloat(this.value) < 1) {
                this.setCustomValidity('Weight must be at least 1kg');
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Show pricing examples when weight input is focused
        weightInput.addEventListener('focus', function() {
            console.log('📦 Pricing Examples:');
            console.log('1 kg → ₱150');
            console.log('3 kg → ₱150'); 
            console.log('5 kg → ₱150');
            console.log('6 kg → ₱150 + (1 × 30) = ₱180');
            console.log('10 kg → ₱150 + (5 × 30) = ₱300');
        });
    }
    
    // Real-time phone validation
    const phoneInput = document.getElementById('customer_phone');
    if (phoneInput) {
        phoneInput.addEventListener('blur', function() {
            validatePhoneLength();
        });
    }
    
    // Add event listeners to add-on checkboxes
    const addOnCheckboxes = document.querySelectorAll('.add-on-checkbox');
    addOnCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotal);
    });
    
    // Event listeners for discount and amount paid
    const discountInput = document.getElementById('discount');
    const amountPaidInput = document.getElementById('amount_paid');
    
    if (discountInput) {
        discountInput.addEventListener('input', applyDiscount);
    }
    if (amountPaidInput) {
        amountPaidInput.addEventListener('input', updateBalance);
    }
    
    // Make functions available globally
    window.validatePhoneLength = validatePhoneLength;
    window.calculateTotal = calculateTotal;
    window.applyDiscount = applyDiscount;
    window.updateBalance = updateBalance;
    window.resetCalculations = resetCalculations;
    window.validateOrderForm = validateOrderForm;
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeOrderForm);