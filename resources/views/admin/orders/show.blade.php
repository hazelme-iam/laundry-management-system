{{-- resources/views/admin/orders/show.blade.php --}}
<x-sidebar-app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h2>
    </x-slot>

    <div class="py-12" wire:poll.5s>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumb Navigation -->
            <div class="px-4 sm:px-0 mb-6 flex items-center justify-between">
                <x-breadcrumbs :items="[
                    'Laundry Management' => route('admin.orders.index'),
                    'Laundry Details' => null
                ]" />
                <a href="{{ route('user.orders.receipt', $order->id) }}" target="_blank" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                    📥 Download Receipt
                </a>
            </div>

            <!-- Order Info Card -->
            <div class="bg-white shadow-lg rounded-lg mx-4 sm:mx-0">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Customer Info -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Customer</h3>
                            <p class="text-lg font-semibold text-gray-900">{{ $order->customer->name ?? 'Unknown' }}</p>
                            <p class="text-sm text-gray-600">{{ $order->customer->phone ?? 'N/A' }}</p>
                        </div>

                        <!-- Weight Info -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Weight</h3>
                            @if($order->isWeightConfirmed())
                                <div class="space-y-1">
                                    <p class="text-sm text-gray-900">
                                        Confirmed: <span class="font-semibold text-green-600">{{ $order->confirmed_weight }} kg</span>
                                    </p>
                                    @if($order->weight)
                                        <p class="text-xs text-gray-600">Declared: {{ $order->weight }} kg</p>
                                    @endif
                                    <p class="text-xs text-gray-500">By: {{ $order->weightConfirmedBy->name ?? 'N/A' }}</p>
                                </div>
                            @else
                                <div class="space-y-1">
                                    @if($order->weight)
                                        <p class="text-sm text-gray-900">Declared: <span class="font-semibold">{{ $order->weight }} kg</span></p>
                                    @else
                                        <p class="text-sm text-amber-600 font-semibold">To be measured at shop</p>
                                    @endif
                                    <p class="text-xs text-red-500">⚠ Awaiting confirmation</p>
                                </div>
                            @endif
                        </div>

                        <!-- Status -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Current Status</h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($order->status === 'completed') bg-green-100 text-green-800
                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif(in_array($order->status, ['washing', 'drying', 'folding', 'quality_check'])) bg-blue-100 text-blue-800
                                @elseif($order->status === 'ready') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">Updated: {{ $order->updated_at->diffForHumans() }}</p>
                        </div>

                        <!-- Amount -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Amount</h3>
                            <p class="text-lg font-semibold text-gray-900">₱{{ number_format($order->total_amount, 2) }}</p>
                            <p class="text-sm text-gray-600">Paid: ₱{{ number_format($order->amount_paid, 2) }}</p>
                        </div>
                    </div>

                    @if($order->add_ons || $order->remarks)
                    <div class="mt-4 pt-4 border-t border-gray-200 space-y-4">
                        @if($order->add_ons)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Add-ons</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($order->add_ons as $key => $value)
                                    @php
                                        $addOn = is_int($key) ? $value : $key;
                                        $qty = is_int($key) ? 1 : (int) $value;
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        @if($addOn === 'detergent')
                                            🧼 Detergent
                                        @elseif($addOn === 'fabric_conditioner')
                                            ✨ Fabric Conditioner
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $addOn)) }}
                                        @endif
                                        @if($qty > 1)
                                            <span class="ml-1 font-semibold">x{{ $qty }}</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($order->remarks)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Remarks</h3>
                            <p class="text-sm text-gray-700">{{ $order->remarks }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Recording Card -->
            <div class="bg-white shadow-lg rounded-lg mx-4 sm:mx-0">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Payment Summary -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Summary</h3>
                            <div class="space-y-3 bg-gray-50 p-4 rounded-lg">
                                @php
                                    // Calculate add-ons total
                                    $addOnsTotal = 0;
                                    if ($order->add_ons && count($order->add_ons) > 0) {
                                        $addOnPrices = [
                                            'detergent' => 16,
                                            'fabric_conditioner' => 14,
                                        ];
                                        foreach ($order->add_ons as $key => $value) {
                                            $addOn = is_int($key) ? $value : $key;
                                            $qty = is_int($key) ? 1 : (int) $value;
                                            if (isset($addOnPrices[$addOn])) {
                                                $addOnsTotal += $addOnPrices[$addOn] * $qty;
                                            }
                                        }
                                    }
                                    $serviceTotal = $order->total_amount - $addOnsTotal;
                                @endphp
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Service:</span>
                                    <span class="font-semibold text-gray-900">₱{{ number_format($serviceTotal, 2) }}</span>
                                </div>
                                @if($addOnsTotal > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Add-ons:</span>
                                    <span class="font-semibold text-blue-600">₱{{ number_format($addOnsTotal, 2) }}</span>
                                </div>
                                @endif
                                <div class="border-t border-gray-200 pt-2 flex justify-between">
                                    <span class="text-gray-600 font-medium">Total Amount:</span>
                                    <span class="font-bold text-gray-900">₱{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Amount Paid:</span>
                                    <span class="font-semibold text-gray-900">₱{{ number_format($order->amount_paid, 2) }}</span>
                                </div>
                                <div class="border-t border-gray-200 pt-3 flex justify-between">
                                    <span class="text-gray-600 font-medium">Balance Due:</span>
                                    <span class="font-bold text-lg {{ $order->isFullyPaid() ? 'text-green-600' : 'text-red-600' }}">
                                        ₱{{ number_format($order->total_amount - $order->amount_paid, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Record Payment Form -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Record Payment</h3>
                            @if($order->total_amount - $order->amount_paid > 0 || $order->status === 'pending')
                            <div class="space-y-3">
                                <div>
                                    <label for="payment_amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (₱)</label>
                                    <input type="number" step="0.01" min="0.01" max="{{ $order->total_amount - $order->amount_paid }}" 
                                           id="payment_amount" placeholder="Enter payment amount" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           oninput="calculateCashAndChange()">
                                </div>
                                <div>
                                    <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date & Time</label>
                                    <input type="datetime-local" id="payment_date" 
                                           value="{{ now()->format('Y-m-d\TH:i') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                                    <textarea id="payment_notes" rows="2" placeholder="e.g., Cash payment, partial payment..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                                <button type="button" onclick="recordPaymentClick({{ $order->id }})" 
                                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                    Record Payment
                                </button>
                            </div>
                            @elseif($order->isFullyPaid())
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <p class="text-green-800 font-medium">✓ Order is fully paid</p>
                            </div>
                            @endif
                        </div>

                        <!-- Cash & Change Calculation -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cash & Change</h3>
                            <div class="space-y-3 bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Amount Due:</span>
                                    <span class="font-semibold text-gray-900">₱<span id="calc_amount_due">{{ number_format($order->total_amount - $order->amount_paid, 2) }}</span></span>
                                </div>
                                @if($order->payments->count() > 0)
                                    @php
                                        $totalCashGiven = $order->payments->sum('cash_given');
                                        $totalChange = $order->payments->sum('change');
                                    @endphp
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Cash Given:</span>
                                        <span class="font-semibold text-gray-900">₱{{ number_format($totalCashGiven, 2) }}</span>
                                    </div>
                                    <div class="border-t border-blue-200 pt-3 flex justify-between">
                                        <span class="text-gray-600 font-medium">Change:</span>
                                        <span class="font-bold text-lg {{ $totalChange > 0 ? 'text-green-600' : 'text-gray-900' }}">₱{{ number_format($totalChange, 2) }}</span>
                                    </div>
                                @else
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Cash Given:</span>
                                        <span class="font-semibold text-gray-900">₱<span id="calc_cash_given">0.00</span></span>
                                    </div>
                                    <div class="border-t border-blue-200 pt-3 flex justify-between">
                                        <span class="text-gray-600 font-medium">Change:</span>
                                        <span class="font-bold text-lg" id="calc_change_display">₱<span id="calc_change">0.00</span></span>
                                    </div>
                                @endif
                                <div class="mt-3 p-3 bg-white rounded border border-blue-100">
                                    <p class="text-xs text-gray-600 mb-2">💡 <strong>Tip:</strong> Enter the amount customer is paying to see change automatically</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weight Confirmation Card -->
            @if(!$order->isWeightConfirmed() && in_array($order->status, ['approved', 'picked_up']))
            <div class="bg-amber-50 border border-amber-200 rounded-lg mx-4 sm:mx-0 p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M6.343 3.665c.886-.887 2.318-.887 3.203 0l9.759 9.759c.887.886.887 2.318 0 3.203l-9.759 9.759c-.886.887-2.317.887-3.203 0L3.14 16.168c-.887-.886-.887-2.317 0-3.203L6.343 3.665z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-amber-900 mb-3">Confirm Weight</h3>
                        <p class="text-sm text-amber-800 mb-4">
                            @if($order->weight)
                                Customer declared weight: <strong>{{ $order->weight }} kg</strong>. Please measure and confirm the actual weight.
                            @else
                                Customer selected "measure at shop". Please measure and confirm the weight.
                            @endif
                        </p>
                        <form id="confirmWeightForm" class="flex items-end gap-3">
                            @csrf
                            <div class="flex-1">
                                <label for="confirmed_weight" class="block text-sm font-medium text-gray-700 mb-1">Confirmed Weight (kg)</label>
                                <input type="number" step="0.01" min="0.1" max="100" id="confirmed_weight" name="confirmed_weight" 
                                       class="w-full px-3 py-2 border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                       placeholder="Enter weight" required>
                            </div>
                            <button type="button" onclick="confirmWeight({{ $order->id }})" 
                                    class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition font-medium">
                                Confirm Weight
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <!-- Laundry Workflow Control -->
            <div class="bg-white shadow-lg rounded-lg mx-4 sm:mx-0">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Laundry Workflow</h3>
                    
                    <!-- Workflow Stages -->
                    <div class="space-y-4">
                        <!-- Stage 1: Laundry Received -->
                        <div class="flex items-center justify-between p-4 border rounded-lg
                            @if($order->status === 'picked_up') bg-blue-50 border-blue-200
                            @elseif(in_array($order->status, ['washing', 'drying', 'folding', 'quality_check', 'ready', 'completed'])) bg-green-50 border-green-200
                            @else bg-gray-50 border-gray-200
                            @endif">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    @if($order->status === 'picked_up') bg-blue-500 text-white
                                    @elseif(in_array($order->status, ['washing', 'drying', 'folding', 'quality_check', 'ready', 'completed'])) bg-green-500 text-white
                                    @else bg-gray-300 text-gray-600
                                    @endif">
                                    @if(in_array($order->status, ['picked_up', 'washing', 'drying', 'folding', 'quality_check', 'ready', 'completed'])) ✓
                                    @else 1
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Laundry Received</h4>
                                    <p class="text-sm text-gray-600">Laundry received at shop</p>
                                </div>
                            </div>
                            @if($order->status === 'approved')
                            <button type="button" onclick="startPickedUp({{ $order->id }}); return false;" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Mark as Laundry Received
                            </button>
                            @endif
                        </div>

                        <!-- Stage 2: Washing -->
                        <div class="flex items-center justify-between p-4 border rounded-lg
                            @if($order->status === 'washing') bg-blue-50 border-blue-200
                            @elseif(in_array($order->status, ['drying', 'folding', 'quality_check', 'ready', 'completed'])) bg-green-50 border-green-200
                            @else bg-gray-50 border-gray-200
                            @endif">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    @if($order->status === 'washing') bg-blue-500 text-white
                                    @elseif(in_array($order->status, ['drying', 'folding', 'quality_check', 'ready', 'completed'])) bg-green-500 text-white
                                    @else bg-gray-300 text-gray-600
                                    @endif">
                                    @if(in_array($order->status, ['washing', 'drying', 'folding', 'quality_check', 'ready', 'completed'])) ✓
                                    @else 2
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Washing</h4>
                                    <p class="text-sm text-gray-600">37-38 minutes cycle</p>
                                    @if($order->status === 'washing' && $order->assigned_washer_id)
                                        <div class="mt-2">
                                            <div class="text-sm font-medium text-blue-600">
                                                Machine: {{ $order->assignedWasher->name ?? 'Unknown' }}
                                            </div>
                                            <div class="text-sm text-blue-600">
                                                Time remaining: <span id="washing-timer">{{ $order->washing_end ? now()->diffInSeconds($order->washing_end, false) > 0 ? ceil(now()->diffInSeconds($order->washing_end, false) / 60) . ' mins' : 'Completed' : 'Calculating...' }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                                <div id="washing-progress" class="bg-blue-500 h-2 rounded-full transition-all duration-1000" style="width: {{ $order->washing_end ? (($order->washing_end->diffInSeconds(now()) / ($order->washing_end->diffInSeconds($order->washing_start))) * 100) : 0 }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if($order->status === 'picked_up' || $order->loads()->where('status', 'pending')->count() > 0)
                                <div class="flex items-center space-x-2">
                                    @php
                                        // Calculate number of loads needed based on confirmed weight
                                        $confirmedWeight = $order->confirmed_weight ?? $order->weight ?? 0;
                                        $machineCapacity = 8.0;
                                        $numberOfLoads = ceil($confirmedWeight / $machineCapacity);
                                        $pendingLoads = $order->loads()->where('status', 'pending')->count();
                                    @endphp
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium">{{ $confirmedWeight }}kg ÷ {{ $machineCapacity }}kg = {{ $numberOfLoads }} load(s)</span>
                                        <span class="text-xs text-gray-500">({{ $pendingLoads }} pending)</span>
                                    </div>
                                    <form method="POST" action="{{ route('machines.assign-washer', $order->id) }}" class="flex items-center space-x-2" id="washer-form">
                                        @csrf
                                        <select name="washer_id" required class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select Washer ({{ $numberOfLoads }} needed)</option>
                                            @foreach(\App\Models\Machine::washers()->idle()->get() as $washer)
                                                <option value="{{ $washer->id }}">{{ $washer->name }} ({{ $washer->capacity_kg }}kg)</option>
                                            @endforeach
                                        </select>
                                        <button type="button" onclick="openAssignWasherModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                            Assign Washer
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <!-- Stage 3: Drying -->
                        <div class="flex items-center justify-between p-4 border rounded-lg
                            @if($order->status === 'drying') bg-blue-50 border-blue-200
                            @elseif(in_array($order->status, ['ready', 'completed'])) bg-green-50 border-green-200
                            @else bg-white border-gray-200 @endif">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    @if($order->status === 'drying') bg-blue-500 text-white
                                    @elseif(in_array($order->status, ['ready', 'completed'])) bg-green-500 text-white
                                    @else bg-gray-300 text-gray-600
                                    @endif">
                                    @if(in_array($order->status, ['ready', 'completed'])) ✓
                                    @else 3
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Drying</h4>
                                    <p class="text-sm text-gray-600">15-20 minutes</p>
                                </div>
                            </div>
                            @if($order->loads()->where('status', 'drying')->whereNull('dryer_machine_id')->count() > 0)
                                <div class="flex items-center space-x-2">
                                    <form method="POST" action="{{ route('machines.assign-dryer', $order->id) }}" class="flex items-center space-x-2" id="dryer-form">
                                        @csrf
                                        <select name="dryer_id" required class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                            <option value="">Select Dryer</option>
                                            @foreach(\App\Models\Machine::dryers()->idle()->get() as $dryer)
                                                <option value="{{ $dryer->id }}">{{ $dryer->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" onclick="openAssignDryerModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                            Assign Dryer
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <!-- Stage 4: Ready -->
                        <div class="flex items-center justify-between p-4 border rounded-lg
                            @if($order->status === 'ready') bg-blue-50 border-blue-200
                            @elseif($order->status === 'completed') bg-green-50 border-green-200
                            @else bg-white border-gray-200 @endif">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-indigo-600 font-semibold text-sm">4</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Ready for Pickup/Delivery</h4>
                                    <p class="text-sm text-gray-600">Order completed and ready</p>
                                </div>
                            </div>
                            @if($order->status === 'ready')
                            <button type="button" onclick="markAsCompleted({{ $order->id }}); return false;" 
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                Mark as Completed
                            </button>
                            @elseif($order->status !== 'completed' && $order->loads()->where('status', 'completed')->count() > 0)
                            <button type="button" onclick="markAsReady({{ $order->id }}); return false;" 
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                Mark as Ready
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load Management -->
            @if($order->loads->count() > 0)
            <div class="bg-white shadow-lg rounded-lg mx-4 sm:mx-0">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Load Management</h3>
                    <div class="space-y-3">
                        @foreach($order->loads as $load)
                        <div class="flex items-center justify-between p-3 border rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold text-sm">{{ $loop->index + 1 }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Load {{ $loop->index + 1 }}</p>
                                    <p class="text-xs text-gray-600">{{ $load->weight }}kg 
                                        @if($load->washerMachine) • Washer: {{ $load->washerMachine->name }} @endif
                                        @if($load->dryerMachine) • Dryer: {{ $load->dryerMachine->name }} @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($load->status === 'completed') bg-green-100 text-green-800
                                    @elseif($load->status === 'washing') bg-blue-100 text-blue-800
                                    @elseif($load->status === 'drying') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($load->status) }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $load->capacity_utilization }}% utilized</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        let timers = {};
        let intervals = {};

        function confirmWeight(orderId) {
            const weight = document.getElementById('confirmed_weight').value;
            
            if (!weight || parseFloat(weight) < 0.1) {
                alert('Please enter a valid weight (minimum 0.1 kg)');
                return;
            }

            // Store weight in modal and open confirmation
            document.getElementById('confirmWeightModal').dataset.weight = weight;
            document.getElementById('confirmWeightModal').dataset.orderId = orderId;
            openModal('confirmWeightModal');
        }

        function submitConfirmWeight() {
            const modal = document.getElementById('confirmWeightModal');
            const weight = modal.dataset.weight;
            const orderId = modal.dataset.orderId;

            fetch(`/orders/${orderId}/confirm-weight`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ confirmed_weight: weight })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal('confirmWeightModal');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to confirm weight'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error confirming weight');
            });
        }

        function startPickedUp(orderId) {
            fetch(`/orders/${orderId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: 'picked_up' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status');
            });
        }

        function startWashing(orderId) {
            fetch(`/orders/${orderId}/start-washing`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    startTimer('washing', data.duration * 60, orderId);
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error starting washing');
            });
        }

        function startDrying(orderId) {
            fetch(`/orders/${orderId}/start-drying`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    startTimer('drying', data.duration * 60, orderId);
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error starting drying');
            });
        }

        function markAsReady(orderId) {
            fetch(`/orders/${orderId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: 'ready' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error marking as ready');
            });
        }

        function markAsCompleted(orderId) {
            fetch(`/orders/${orderId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: 'completed' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error marking as completed');
            });
        }

        function startTimer(type, durationInSeconds, orderId) {
            const timerElement = document.getElementById(`${type}-timer`);
            const progressElement = document.getElementById(`${type}-progress`);
            
            if (!timerElement || !progressElement) return;

            let timeRemaining = durationInSeconds;
            const totalTime = durationInSeconds;

            // Clear any existing interval
            if (intervals[type]) {
                clearInterval(intervals[type]);
            }

            intervals[type] = setInterval(() => {
                timeRemaining--;
                
                // Update timer display
                const minutes = Math.floor(timeRemaining / 60);
                const seconds = timeRemaining % 60;
                timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                // Update progress bar
                const progress = ((totalTime - timeRemaining) / totalTime) * 100;
                progressElement.style.width = `${progress}%`;
                
                // Timer completed
                if (timeRemaining <= 0) {
                    clearInterval(intervals[type]);
                    timerElement.textContent = 'Completed';
                    progressElement.style.width = '100%';
                    
                    // Check for completed machines
                    fetch('{{ route("machines.check-completed") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Machine completion checked:', data);
                        // Show notification
                        showNotification(`${type.charAt(0).toUpperCase() + type.slice(1)} cycle completed!`);
                        
                        // Auto-refresh after a delay to show updated status
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    })
                    .catch(error => {
                        console.error('Error checking completed machines:', error);
                        // Still show notification and refresh
                        showNotification(`${type.charAt(0).toUpperCase() + type.slice(1)} cycle completed!`);
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    });
                }
            }, 1000);
        }

        function showNotification(message) {
            // Create a simple notification (you can replace this with a better notification system)
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }

        // Initialize any active timers on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check for completed machines every 5 seconds
            setInterval(() => {
                fetch('{{ route("machines.check-completed") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.completed_washing_loads > 0 || data.completed_drying_loads > 0) {
                        // Auto-refresh page to show updated status
                        location.reload();
                    }
                })
                .catch(error => console.log('Error checking completed machines:', error));
            }, 5000);

        // Handle washer form submission with AJAX
        document.getElementById('washer-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Start timer
                    startTimer('washing', data.duration * 60, {{ $order->id }});
                    // Show success message
                    alert(data.message);
                    // Reload page to update UI
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error assigning washer');
            });
        });

        // Handle dryer form submission with AJAX
        document.getElementById('dryer-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Start timer
                    startTimer('drying', data.duration * 60, {{ $order->id }});
                    // Show success message
                    alert(data.message);
                    // Reload page to update UI
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error assigning dryer');
            });
        });
        });

        // Function to calculate cash given and change
        function calculateCashAndChange() {
            const paymentAmount = parseFloat(document.getElementById('payment_amount').value) || 0;
            const totalAmount = parseFloat('{{ $order->total_amount }}');
            const amountPaid = parseFloat('{{ $order->amount_paid }}');
            const remainingBalance = totalAmount - amountPaid;
            
            // Update cash given (this is what customer gives)
            document.getElementById('calc_cash_given').textContent = paymentAmount.toFixed(2);
            
            // Calculate change (payment - remaining balance, or payment - total if overpaying)
            const change = paymentAmount - remainingBalance;
            const changeDisplay = document.getElementById('calc_change_display');
            const changeValue = document.getElementById('calc_change');
            
            if (change > 0) {
                changeValue.textContent = change.toFixed(2);
                changeDisplay.className = 'font-bold text-lg text-green-600';
            } else {
                changeValue.textContent = '0.00';
                changeDisplay.className = 'font-bold text-lg text-gray-900';
            }
        }

        // Function to record payment
        function recordPayment(orderId) {
            const amount = document.getElementById('payment_amount').value;
            const paymentDate = document.getElementById('payment_date').value;
            const notes = document.getElementById('payment_notes').value;

            if (!amount || parseFloat(amount) <= 0) {
                alert('Please enter a valid payment amount');
                return;
            }

            if (!paymentDate) {
                alert('Please select a payment date and time');
                return;
            }

            // Convert datetime-local to proper format
            const [date, time] = paymentDate.split('T');
            const formattedDate = `${date} ${time}`;

            fetch(`/orders/${orderId}/record-payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount: parseFloat(amount),
                    payment_date: formattedDate,
                    notes: notes || null
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Clear form
                    document.getElementById('payment_amount').value = '';
                    document.getElementById('payment_notes').value = '';
                    // Reset calculations
                    calculateCashAndChange();
                    // Reload page to update payment summary and history
                    setTimeout(() => location.reload(), 500);
                } else {
                    alert('Error: ' + (data.message || 'Failed to record payment'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error recording payment');
            });
        }

        // Function to open assign washer confirmation modal
        function openAssignWasherModal() {
            // Validate that a washer is selected
            const washerSelect = document.querySelector('select[name="washer_id"]');
            if (!washerSelect.value) {
                alert('Please select a washer first.');
                return;
            }
            
            openModal('assignWasherModal');
        }

        // Function to open assign dryer confirmation modal
        function openAssignDryerModal() {
            // Validate that a dryer is selected
            const dryerSelect = document.querySelector('select[name="dryer_id"]');
            if (!dryerSelect.value) {
                alert('Please select a dryer first.');
                return;
            }
            
            openModal('assignDryerModal');
        }

        // Handle washer modal confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const washerModal = document.getElementById('assignWasherModal');
            if (washerModal) {
                const confirmButton = washerModal.querySelector('.confirm-button');
                if (confirmButton) {
                    confirmButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        const form = document.getElementById('washer-form');
                        if (form) {
                            // Submit the form using AJAX
                            const formData = new FormData(form);
                            
                            fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Show success message
                                    alert(data.message);
                                    // Reload page to update UI
                                    setTimeout(() => location.reload(), 1000);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Error assigning washer');
                            });
                        }
                    });
                }
            }

            // Handle dryer modal confirmation
            const dryerModal = document.getElementById('assignDryerModal');
            if (dryerModal) {
                const confirmButton = dryerModal.querySelector('.confirm-button');
                if (confirmButton) {
                    confirmButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        const form = document.getElementById('dryer-form');
                        if (form) {
                            // Submit the form using AJAX
                            const formData = new FormData(form);
                            
                            fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Show success message
                                    alert(data.message);
                                    // Reload page to update UI
                                    setTimeout(() => location.reload(), 1000);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Error assigning dryer');
                            });
                        }
                    });
                }
            }
        });

    </script>

    <!-- Assign Washer Confirmation Modal -->
    <x-confirmationmodal 
        modalId="assignWasherModal"
        title="Assign Washer"
        message="Are you sure you want to assign this washer to the order? This will start the washing process."
        confirmText="Assign Washer"
        cancelText="Cancel"
        confirmColor="blue"
        formId="washer-form"
    />

    <!-- Confirm Weight Modal -->
    <x-confirmationmodal 
        modalId="confirmWeightModal"
        title="Confirm Weight"
        message="Are you sure you want to confirm this weight? This action cannot be undone."
        confirmText="Confirm Weight"
        cancelText="Cancel"
        confirmColor="amber"
        formId="nonExistentForm"
    />

    <!-- Assign Dryer Confirmation Modal -->
    <x-confirmationmodal 
        modalId="assignDryerModal"
        title="Assign Dryer"
        message="Are you sure you want to assign this dryer to the order? This will start the drying process."
        confirmText="Assign Dryer"
        cancelText="Cancel"
        confirmColor="green"
        formId="dryer-form"
    />

    <!-- Record Payment Confirmation Modal -->
    <x-confirmationmodal 
        modalId="recordPaymentModal"
        title="Confirm Payment Recording"
        message="Are you sure you want to record this payment? This action will update the order's payment status."
        confirmText="Record Payment"
        cancelText="Cancel"
        confirmColor="green"
        formId="nonExistentForm"
    />

    <script>
        // Store order ID for record payment modal
        let recordPaymentOrderId = null;

        // Record payment button click - open modal directly
        function recordPaymentClick(orderId) {
            const paymentAmount = document.getElementById('payment_amount')?.value;
            
            if (!paymentAmount || parseFloat(paymentAmount) <= 0) {
                alert('Please enter a valid payment amount');
                return;
            }

            recordPaymentOrderId = orderId;
            openModal('recordPaymentModal');
        }

        // Submit record payment from modal
        function submitRecordPayment() {
            const paymentAmount = document.getElementById('payment_amount')?.value;
            const paymentDateInput = document.getElementById('payment_date')?.value;
            const paymentNotes = document.getElementById('payment_notes')?.value;

            // Convert datetime-local format (2025-12-14T16:53) to Y-m-d H:i format (2025-12-14 16:53)
            const paymentDate = paymentDateInput.replace('T', ' ');

            fetch(`/orders/${recordPaymentOrderId}/record-payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount: paymentAmount,
                    payment_date: paymentDate,
                    notes: paymentNotes
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal('recordPaymentModal');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to record payment'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error recording payment');
            });
        }

        // Handle confirm weight modal submission
        setTimeout(function() {
            const confirmWeightModal = document.getElementById('confirmWeightModal');
            if (confirmWeightModal) {
                // Find all buttons in the modal
                const allButtons = confirmWeightModal.querySelectorAll('button');
                // The confirm button should be the one with "Confirm Weight" text
                for (let btn of allButtons) {
                    if (btn.textContent.includes('Confirm Weight')) {
                        btn.onclick = function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            submitConfirmWeight();
                            return false;
                        };
                        break;
                    }
                }
            }

            // Handle record payment modal submission
            const recordPaymentModal = document.getElementById('recordPaymentModal');
            if (recordPaymentModal) {
                const allButtons = recordPaymentModal.querySelectorAll('button');
                for (let btn of allButtons) {
                    if (btn.textContent.includes('Record Payment')) {
                        btn.onclick = function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            submitRecordPayment();
                            return false;
                        };
                        break;
                    }
                }
            }
        }, 100);
    </script>
</x-sidebar-app>
