// public/js/order-create.js
// NOTE: This file is served directly by the browser via asset('js/order-create.js').

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

    // Calculate base amount using the pricing rule
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

    // phone
    if (!phone || phone.length !== 11) {
        const phoneError = document.getElementById('phone_error');
        if (phoneError) phoneError.classList.remove('hidden');
        alert('Phone number must be exactly 11 digits');
        document.getElementById('customer_phone')?.focus();
        return false;
    }

    const phonePattern = /^[0-9]{11}$/;
    if (!phonePattern.test(phone)) {
        alert('Phone number must contain only numbers (11 digits)');
        document.getElementById('customer_phone')?.focus();
        return false;
    }

    if (!address) {
        alert('Please enter your address');
        document.getElementById('customer_address')?.focus();
        return false;
    }

    return true;
}

function initializeOrderForm() {
    const weightInput = document.getElementById('weight');

    if (weightInput) {
        weightInput.addEventListener('input', calculateTotal);
        weightInput.addEventListener('blur', function() {
            if (this.value && parseFloat(this.value) < 1) {
                this.setCustomValidity('Weight must be at least 1kg');
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });
    }

    const phoneInput = document.getElementById('customer_phone');
    if (phoneInput) {
        phoneInput.addEventListener('blur', function() {
            validatePhoneLength();
        });
    }

    const addOnCheckboxes = document.querySelectorAll('.add-on-checkbox');
    addOnCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotal);
    });

    const discountInput = document.getElementById('discount');
    const amountPaidInput = document.getElementById('amount_paid');

    if (discountInput) discountInput.addEventListener('input', applyDiscount);
    if (amountPaidInput) amountPaidInput.addEventListener('input', updateBalance);

    // initial calc
    calculateTotal();

    // expose
    window.validatePhoneLength = validatePhoneLength;
    window.calculateTotal = calculateTotal;
    window.applyDiscount = applyDiscount;
    window.updateBalance = updateBalance;
    window.resetCalculations = resetCalculations;
    window.validateOrderForm = validateOrderForm;
}

document.addEventListener('DOMContentLoaded', initializeOrderForm);
