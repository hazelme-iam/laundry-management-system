<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Machine;
use App\Models\Load;
use App\Models\User;
use App\Services\CacheService;
use App\Services\NotificationService;
use App\Services\ShopService;
use App\Rules\ValidWeight;
use App\Rules\ValidMonetary;
use App\Rules\ValidFutureDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    private const WASHING_TIME_MINUTES = 38; // Fixed washing time
    private const DRYING_TIME_PER_KG = 5; // 5 minutes per kg for drying
    private const MIN_DRYING_TIME = 30; // Minimum 30 minutes
    private const MAX_DRYING_TIME = 50; // Maximum 50 minutes for 8kg

    /**
     * Display a listing of orders.
     */
    public function index()
    {
        // Use selective eager loading - only load what's needed for the list view
        $query = Order::with(['customer', 'creator', 'payments']);

        // Backlog filter: orders marked as backlog
        if (request('backlog') === 'backlog') {
            $query->where('is_backlog', true);
        } else {
            // Status filter
            if (request('status')) {
                $query->where('status', request('status'));
            }

            // Source filter: online vs walk-in
            if ($src = request('source')) {
                if ($src === 'online') {
                    // Online: customer has a user_id and order was created by that same user
                    $query->whereHas('customer', function ($q) {
                        $q->whereNotNull('customers.user_id')
                          ->whereColumn('customers.user_id', 'orders.created_by');
                    });
                } elseif ($src === 'walk_in') {
                    // Walk-in: either customer has no user account, OR order was created by someone else (admin)
                    $query->whereHas('customer', function ($q) {
                        $q->where(function ($x) {
                            $x->whereNull('customers.user_id')
                              ->orWhereColumn('customers.user_id', '!=', 'orders.created_by');
                        });
                    });
                }
            }
        }

        $orders = $query->latest()->paginate(10);

        // Use cached order statistics instead of multiple queries
        $stats = CacheService::getOrderStats();
        $pendingCount = $stats['pending'];
        $inProgressCount = $stats['in_progress'];
        $completedCount = $stats['completed'];

        return view('admin.orders.index', compact('orders', 'pendingCount', 'inProgressCount', 'completedCount'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        // Fetch fresh customer list without cache to ensure newly created customers appear
        $customers = [];
        
        // Get all regular users (non-admin) and create customer options for them
        $regularUsers = User::where('role', '!=', 'admin')->get();
        
        foreach ($regularUsers as $user) {
            // Check if user has a customer record
            $customerRecord = Customer::where('user_id', $user->id)->first();
            
            if ($customerRecord) {
                // Use existing customer record
                $customers[] = (object) [
                    'id' => $customerRecord->id,
                    'name' => $customerRecord->name,
                    'email' => $customerRecord->email,
                    'phone' => $customerRecord->phone,
                    'address' => $customerRecord->address,
                    'customer_type' => $customerRecord->customer_type,
                    'user_id' => $user->id,
                    'is_virtual' => false
                ];
            } else {
                // Create virtual customer object for user without customer record
                $customers[] = (object) [
                    'id' => 'user_' . $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? null,
                    'address' => null,
                    'customer_type' => 'online',
                    'user_id' => $user->id,
                    'is_virtual' => true
                ];
            }
        }
        
        // Add walk-in customers (customers without user_id)
        $walkinCustomers = Customer::whereNull('user_id')
            ->select('id', 'name', 'email', 'phone', 'customer_type', 'user_id', 'address')
            ->get();
        
        foreach ($walkinCustomers as $customer) {
            $customers[] = (object) [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'customer_type' => $customer->customer_type,
                'user_id' => null,
                'is_virtual' => false
            ];
        }
        
        // Sort by name
        usort($customers, function($a, $b) {
            return strcmp($a->name, $b->name);
        });
        
        $customers = collect($customers);
        
        return view('admin.orders.create', compact('customers'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required', // Will validate manually
            'weight' => ['required', new ValidWeight],
            'detergent_qty' => 'nullable|numeric|min:0',
            'fabric_conditioner_qty' => 'nullable|numeric|min:0',
            'subtotal' => ['required', new ValidMonetary],
            'total_amount' => ['required', new ValidMonetary],
            'amount_paid' => ['required', new ValidMonetary],
            'pickup_date' => ['nullable', 'date', new ValidFutureDate(true, 1)],
            'estimated_finish' => ['required', 'date', new ValidFutureDate(false, 1)],
            'remarks' => 'nullable|string|max:1000',
        ]);

        // Handle virtual customer IDs (user_2 format)
        $customerId = $request->customer_id;
        if (strpos($customerId, 'user_') === 0) {
            // This is a virtual customer - extract user ID
            $userId = str_replace('user_', '', $customerId);
            $user = User::findOrFail($userId);
            
            // Create or get customer record for this user
            $customer = Customer::firstOrCreate([
                'user_id' => $userId,
            ], [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? 'N/A',
                'address' => 'N/A',
                'customer_type' => 'online',
            ]);
            
            $data['customer_id'] = $customer->id;
        } else {
            // Regular customer ID - validate it exists
            $customer = Customer::findOrFail($customerId);
            $data['customer_id'] = $customerId;
        }

        // Sanitize text inputs to prevent XSS
        $data['remarks'] = Str::limit(strip_tags($data['remarks'] ?? ''), 1000);

        // Build add_ons from quantity fields
        $addOns = [];
        $detergentQty = (int) ($data['detergent_qty'] ?? 0);
        $fabricConditionerQty = (int) ($data['fabric_conditioner_qty'] ?? 0);

        if ($detergentQty > 0) {
            $addOns['detergent'] = $detergentQty;
        }

        if ($fabricConditionerQty > 0) {
            $addOns['fabric_conditioner'] = $fabricConditionerQty;
        }

        // Validate business logic
        if ($data['amount_paid'] > $data['total_amount']) {
            return back()->withErrors(['amount_paid' => 'Amount paid cannot be greater than total amount.'])->withInput();
        }

        // Set default status to 'pending' for new orders
        $data['status'] = 'pending';
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $data['add_ons'] = count($addOns) > 0 ? $addOns : null;
        $data['discount'] = 0; // No discount in admin form

        $order = Order::create($data);
        
        // Reload order with relationships to ensure customer data is available
        $order->load('customer');
        
        // Automatically create optimized loads
        $loads = $order->createOptimizedLoads();

        // Clear relevant caches
        CacheService::clearOrderRelatedCaches();
        CacheService::clearCustomerCache();

        // Send notifications
        NotificationService::newOrderCreated($order);

        // Check if order exceeded capacity and went to backlog
        $today = now()->format('Y-m-d');
        $todayOrders = Order::whereDate('created_at', $today)
            ->with('customer')
            ->orderBy('created_at', 'asc')
            ->get();
        
        $dailyWasherCapacity = 5 * 12 * 8; // WASHERS_COUNT * OPERATING_HOURS_PER_DAY * CYCLE_CAPACITY_KG
        $cumulativeWeight = 0;
        $isInBacklog = false;
        
        foreach ($todayOrders as $todayOrder) {
            $orderWeight = $todayOrder->confirmed_weight ?? $todayOrder->weight;
            if ($cumulativeWeight + $orderWeight > $dailyWasherCapacity) {
                if ($todayOrder->id === $order->id) {
                    $isInBacklog = true;
                }
                // Mark this order as backlog
                $todayOrder->update(['is_backlog' => true]);
                // Do NOT add to cumulativeWeight - backlog orders don't count toward today's capacity
            } else {
                // Mark as not backlog
                $todayOrder->update(['is_backlog' => false]);
                $cumulativeWeight += $orderWeight;
            }
        }
        
        // Send backlog notification to customer if order is in backlog
        if ($isInBacklog) {
            NotificationService::orderPlacedInBacklog($order);
        }
        
        // Check and send capacity alert if machines are 80%+ full
        NotificationService::capacityAlert();
        
        $successMessage = 'Order created successfully.';
        if ($isInBacklog) {
            $successMessage .= ' This order has been placed in backlog as it exceeds today\'s capacity.';
        }

        return redirect()->route('admin.orders.index')->with('success', $successMessage);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Always load fresh data for Livewire auto-refresh
        $order->load(['customer', 'creator', 'updater', 'loads.washerMachine', 'loads.dryerMachine', 'assignedWasher', 'assignedDryer', 'payments.recordedBy']);
        
        // Calculate drying time for this order
        $dryingTime = $this->calculateDryingTime($order->weight);
        
        // Check if there's an active timer (you might want to store this in session or database)
        $activeTimer = null;
        if (in_array($order->status, ['washing', 'drying'])) {
            // For now, we'll calculate based on when status was last updated
            // In a real implementation, you'd store the start time in the database
            $statusUpdatedAt = $order->updated_at;
            $elapsedMinutes = $statusUpdatedAt->diffInMinutes(now());
            
            if ($order->status === 'washing') {
                $remaining = max(0, self::WASHING_TIME_MINUTES - $elapsedMinutes);
                $activeTimer = $remaining > 0 ? $remaining : 0;
            } elseif ($order->status === 'drying') {
                $remaining = max(0, $dryingTime - $elapsedMinutes);
                $activeTimer = $remaining > 0 ? $remaining : 0;
            }
        }
        
        return view('admin.orders.show', compact('order', 'dryingTime', 'activeTimer'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        $customers = CacheService::getCustomerList();
        return view('admin.orders.edit', compact('order', 'customers'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:pending,approved,rejected,picked_up,washing,drying,folding,quality_check,ready,delivery_pending,completed,cancelled',
            'weight' => ['required', new ValidWeight],
            'add_ons' => 'nullable|array',
            'add_ons.*' => 'string|in:detergent,fabric_conditioner',
            'subtotal' => ['required', new ValidMonetary],
            'discount' => ['required', new ValidMonetary],
            'total_amount' => ['required', new ValidMonetary],
            'amount_paid' => ['required', new ValidMonetary],
            'pickup_date' => ['nullable', 'date', new ValidFutureDate(true, 1)],
            'estimated_finish' => ['required', 'date', new ValidFutureDate(false, 1)],
            'finished_at' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
            'primary_washer_id' => 'nullable|exists:machines,id',
            'primary_dryer_id' => 'nullable|exists:machines,id',
        ]);

        // Sanitize text inputs to prevent XSS
        $data['remarks'] = Str::limit(strip_tags($data['remarks'] ?? ''), 1000);

        // Validate business logic
        if ($data['discount'] > $data['subtotal']) {
            return back()->withErrors(['discount' => 'Discount cannot be greater than subtotal.'])->withInput();
        }

        if ($data['amount_paid'] > $data['total_amount']) {
            return back()->withErrors(['amount_paid' => 'Amount paid cannot be greater than total amount.'])->withInput();
        }

        // Validate status transitions
        if (!$this->isValidStatusTransition($order->status, $data['status'])) {
            return back()->withErrors(['status' => 'Invalid status transition from ' . $order->status . ' to ' . $data['status']])->withInput();
        }

        $data['updated_by'] = Auth::id();

        $order->update($data);

        // Clear relevant caches
        CacheService::clearOrderRelatedCaches();
        CacheService::clearCustomerCache();

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // Check if order has related records that prevent deletion
        if ($order->loads()->count() > 0) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Cannot delete order with associated laundry loads. Please cancel the order instead.');
        }

        // Additional checks for other potential constraints
        if ($order->status === 'washing' || $order->status === 'drying' || $order->status === 'folding') {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Cannot delete order that is currently being processed. Please cancel the order instead.');
        }

        try {
            $order->delete();
            
            // Clear relevant caches
            CacheService::clearOrderRelatedCaches();
            
            return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle foreign key constraint violations
            if ($e->getCode() === '23000') {
                return redirect()->route('admin.orders.index')
                    ->with('error', 'Cannot delete order due to related records. Please cancel the order instead.');
            }
            
            throw $e;
        }
    }

    /**
     * Calculate order total based on weight and add-ons
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'weight' => ['required', new ValidWeight],
            'add_ons' => 'nullable|array',
            'add_ons.*' => 'string|in:detergent,fabric_conditioner',
        ]);

        $weight = $request->weight;
        $addOns = $request->add_ons ?? [];
        
        $subtotal = ShopService::calculateOrderTotal($weight, $addOns);
        
        return response()->json([
            'subtotal' => number_format($subtotal, 2),
            'total_amount' => number_format($subtotal, 2),
        ]);
    }

    /**
     * Validate if status transition is allowed
     */
    private function recalculateBacklogStatus()
    {
        // Get all orders created today
        $today = now()->format('Y-m-d');
        $todayOrders = Order::whereDate('created_at', $today)
            ->orderBy('created_at', 'asc')
            ->get();
        
        $dailyWasherCapacity = 5 * 12 * 8; // 480kg
        $cumulativeWeight = 0;
        
        foreach ($todayOrders as $order) {
            $orderWeight = $order->confirmed_weight ?? $order->weight;
            
            // Smart backlog: only mark as backlog if THIS order exceeds remaining capacity
            if ($cumulativeWeight + $orderWeight > $dailyWasherCapacity) {
                // This order exceeds capacity - mark as backlog
                $order->update(['is_backlog' => true]);
                // Do NOT add to cumulativeWeight - backlog orders don't count toward today's capacity
            } else {
                // This order fits within remaining capacity - mark as not backlog
                $order->update(['is_backlog' => false]);
                $cumulativeWeight += $orderWeight;
            }
        }
    }

    private function isValidStatusTransition($fromStatus, $toStatus)
    {
        $validTransitions = [
            'pending' => ['approved', 'rejected', 'cancelled'],
            'approved' => ['picked_up', 'cancelled'],
            'rejected' => ['pending', 'cancelled'],
            'picked_up' => ['washing', 'cancelled'],
            'washing' => ['drying', 'cancelled'],
            'drying' => ['folding', 'cancelled'],
            'folding' => ['quality_check', 'cancelled'],
            'quality_check' => ['ready', 'cancelled'],
            'ready' => ['delivery_pending', 'completed'],
            'delivery_pending' => ['completed', 'cancelled'],
            'completed' => [], // Terminal state
            'cancelled' => [], // Terminal state
        ];

        return in_array($toStatus, $validTransitions[$fromStatus] ?? []);
    }

    // User-specific methods
    public function userIndex(Request $request)
    {
        // Get or create customer record for logged-in user
        $customer = Customer::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => '',
                'address' => '',
                'customer_type' => 'regular',
            ]
        );

        // Get orders for this customer with sorting
        $query = Order::where('customer_id', $customer->id)
            ->with(['customer', 'creator']);

        // Optional status filter
        $status = $request->get('status');
        if (!empty($status)) {
            $query->where('status', $status);
        }

        // Apply sorting based on request parameter
        $sort = $request->get('sort', 'latest');
        
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'status_pending':
                $query->where('status', 'pending')->latest();
                break;
            case 'status_completed':
                $query->where('status', 'completed')->latest();
                break;
            case 'highest_amount':
                $query->orderBy('total_amount', 'desc');
                break;
            case 'lowest_amount':
                $query->orderBy('total_amount', 'asc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $orders = $query->paginate(10)->appends($request->query());

        return view('user.orders.index', compact('orders'));
    }

    public function userCreate()
    {
        // Get or create customer record for logged-in user
        $customer = Customer::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => '',
                'address' => '',
                'customer_type' => 'regular',
            ]
        );
        
        return view('user.orders.create', compact('customer'));
    }

    public function userStore(Request $request)
    {
        $data = $request->validate([
            'weight_option' => 'required|in:measure_at_shop,manual_weight',
            'weight' => 'nullable|numeric|min:1|required_if:weight_option,manual_weight',
            'detergent_qty' => 'nullable|numeric|min:0',
            'fabric_conditioner_qty' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'pickup_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'customer_phone' => 'required|string',
            'customer_address' => 'required|string',
            'barangay' => 'nullable|string',
            'purok' => 'nullable|string',
            'street' => 'nullable|string',
        ]);

        // Get or create customer record for logged-in user
        $customer = Customer::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => $data['customer_phone'],
                'address' => $data['customer_address'],
                'customer_type' => 'regular',
            ]
        );

        // Update customer info if changed
        $customer->update([
            'phone' => $data['customer_phone'],
            'address' => $data['customer_address'],
            'barangay' => $data['barangay'] ?? null,
            'purok' => $data['purok'] ?? null,
            'street' => $data['street'] ?? null,
        ]);

        // Handle weight based on measurement option
        $weight = null;
        $remarks = $data['remarks'] ?? '';
        
        if ($data['weight_option'] === 'manual_weight') {
            $weight = $data['weight'];
        } else {
            // For measure_at_shop, add note to remarks
            $remarks = ($remarks ? $remarks . ' | ' : '') . 'Weight to be measured at shop upon arrival.';
        }

        $addOns = [];
        $detergentQty = (int) ($data['detergent_qty'] ?? 0);
        $fabricConditionerQty = (int) ($data['fabric_conditioner_qty'] ?? 0);

        if ($detergentQty > 0) {
            $addOns['detergent'] = $detergentQty;
        }

        if ($fabricConditionerQty > 0) {
            $addOns['fabric_conditioner'] = $fabricConditionerQty;
        }

        // Create order with pending status for admin approval
        $orderData = [
            'customer_id' => $customer->id,
            'weight' => $weight,
            'add_ons' => count($addOns) > 0 ? $addOns : null,
            'subtotal' => $data['subtotal'],
            'discount' => $data['discount'] ?? 0,
            'total_amount' => $data['subtotal'] ?? 0,
            'amount_paid' => 0,
            'pickup_date' => $data['pickup_date'],
            'remarks' => $remarks,
            'status' => 'pending', // Pending admin approval
            'priority' => 'normal',
            'service_type' => 'standard',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ];

        $order = Order::create($orderData);

        // Notify admins that an online customer placed an order
        NotificationService::newOrderCreated($order);

        return redirect()->route('user.orders.index')->with('success', 'Order submitted successfully! Awaiting admin approval.');
    }

    public function userShow(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->customer->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $order->load(['customer', 'creator', 'updater', 'loads.washerMachine', 'loads.dryerMachine']);
        return view('user.orders.show', compact('order'));
    }

    public function userCancel(Order $order)
    {
        // Ensure user can only cancel their own orders
        if ($order->customer->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Only allow cancellation of pending orders
        if ($order->status !== 'pending') {
            return redirect()->route('user.orders.show', $order)
                ->with('error', 'Only pending orders can be cancelled.');
        }

        // Update order status to cancelled
        $order->update([
            'status' => 'cancelled',
            'updated_by' => auth()->id(),
        ]);

        // Clear relevant caches
        CacheService::clearOrderRelatedCaches();

        return redirect()->route('user.orders.index')
            ->with('success', 'Order cancelled successfully.');
    }

    public function userReceipt(Order $order)
    {
        // Ensure user can only view their own order receipts
        if ($order->customer->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Generate receipt HTML
        $html = view('user.orders.receipt', compact('order'))->render();

        // Return as downloadable HTML file
        return response($html)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="receipt-order-' . $order->id . '.html"');
    }

    /**
     * Calculate order total for user orders
     */
    public function userCalculate(Request $request)
    {
        return $this->calculate($request);
    }

    // Admin approval methods
    public function approve(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Only pending orders can be approved.');
        }

        // Update order status to approved and create optimized loads
        $order->update([
            'status' => 'approved',
            'updated_by' => Auth::id(),
        ]);

        // Create loads for approved order
        $loads = $order->createOptimizedLoads();
        
        // Recalculate backlog status for all orders created today
        $this->recalculateBacklogStatus();

        return redirect()->route('admin.orders.index')
            ->with('success', "Order #{$order->id} approved successfully! Loads created.");
    }

    public function decline(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Only pending orders can be declined.');
        }

        $order->update([
            'status' => 'rejected',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order declined successfully.');
    }

    // Pending orders page
    public function pending()
    {
        $orders = Order::with(['customer', 'creator', 'updater'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.orders.pending', compact('orders'));
    }

    /**
     * Calculate drying time based on weight
     */
    private function calculateDryingTime($weight)
    {
        return ShopService::calculateDryingTime($weight);
    }

    /**
     * Confirm weight for an order
     */
    public function confirmWeight(Request $request, Order $order)
    {
        $data = $request->validate([
            'confirmed_weight' => 'required|numeric|min:0.1|max:100',
        ]);

        // Prevent confirmation if order is already in processing
        if (in_array($order->status, ['washing', 'drying', 'folding', 'quality_check', 'ready', 'completed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot confirm weight for orders already in processing.'
            ], 422);
        }

        $confirmedWeight = $data['confirmed_weight'];

        // Recalculate pricing if weight was null (measure at shop option)
        $updateData = [
            'confirmed_weight' => $confirmedWeight,
            'weight_confirmed_at' => now(),
            'weight_confirmed_by' => Auth::id(),
            'updated_by' => Auth::id()
        ];

        // If original weight was null, recalculate subtotal and total_amount based on confirmed weight
        if ($order->weight === null) {
            // Pricing rule: ₱150 for up to 5kg + ₱30 per additional kg
            $basePrice = 150;
            $baseWeightLimit = 5;
            $excessPricePerKg = 30;

            if ($confirmedWeight <= $baseWeightLimit) {
                $newSubtotal = $basePrice;
            } else {
                $excessWeight = $confirmedWeight - $baseWeightLimit;
                $newSubtotal = $basePrice + ($excessWeight * $excessPricePerKg);
            }

            // Add existing add-ons cost
            if ($order->add_ons && count($order->add_ons) > 0) {
                $addOnPrices = [
                    'detergent' => 16,
                    'fabric_conditioner' => 14,
                ];

                foreach ($order->add_ons as $key => $value) {
                    $addOn = is_int($key) ? $value : $key;
                    $qty = is_int($key) ? 1 : (int) $value;
                    
                    if (isset($addOnPrices[$addOn])) {
                        $newSubtotal += $addOnPrices[$addOn] * $qty;
                    }
                }
            }

            // Update subtotal and total_amount (discount is always 0 for online orders)
            $updateData['subtotal'] = $newSubtotal;
            $updateData['total_amount'] = $newSubtotal;
        }

        $order->update($updateData);

        // If loads don't exist yet, create them now with confirmed weight
        if ($order->loads()->count() === 0) {
            $order->createOptimizedLoads();
        }

        // Recalculate backlog status for all orders created today
        $this->recalculateBacklogStatus();
        
        // Reload order to get updated is_backlog status
        $order->refresh();
        
        // Send backlog notification to customer if order is now in backlog
        if ($order->is_backlog) {
            NotificationService::orderPlacedInBacklog($order);
        }

        return response()->json([
            'success' => true,
            'message' => "Weight confirmed: {$confirmedWeight} kg" . (isset($newSubtotal) ? " - Price updated to ₱" . number_format($newSubtotal, 2) : "") . ($order->is_backlog ? " - Order placed in backlog due to capacity." : ""),
            'confirmed_weight' => $confirmedWeight,
            'subtotal' => $order->subtotal,
            'total_amount' => $order->total_amount,
            'is_backlog' => $order->is_backlog
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,picked_up,washing,drying,folding,quality_check,ready,ready_for_pickup,delivery_pending,completed,cancelled'
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;
        $order->update([
            'status' => $newStatus,
            'updated_by' => Auth::id()
        ]);

        // Reload order to get fresh data
        $order->refresh();

        // Send admin notifications for washing and drying completion
        if ($newStatus === 'washing') {
            // Notify admins when washing starts
            NotificationService::orderStatusChanged($order);
        } elseif ($newStatus === 'drying') {
            // Notify admins when washing is done and drying starts
            NotificationService::washingCompleted($order);
            NotificationService::orderStatusChanged($order);
        } elseif ($newStatus === 'folding') {
            // Notify admins when drying is done
            NotificationService::dryingCompleted($order);
            NotificationService::orderStatusChanged($order);
        } else {
            // Send status change notification for other statuses
            NotificationService::orderStatusChanged($order);
        }

        // Send automatic notifications to online customers
        if ($order->customer && $order->customer->user_id) {
            $user = User::find($order->customer->user_id);
            
            if ($user) {
                if ($newStatus === 'ready') {
                    // Send receipt notification when order is ready
                    $user->notify(new \App\Notifications\OrderReceiptNotification($order));
                } elseif ($newStatus === 'completed') {
                    // Send completion notification
                    $user->notify(new \App\Notifications\OrderUpdateNotification(
                        $order,
                        'Your order has been completed and is ready for pickup!'
                    ));
                }
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Start washing cycle
     */
    public function startWashing(Request $request, Order $order)
    {
        // Find an available washer
        $washer = Machine::where('type', 'washer')
            ->where('status', 'available')
            ->first();

        if (!$washer) {
            return response()->json([
                'success' => false, 
                'message' => 'No available washers'
            ], 422);
        }

        // Update order status and assign washer
        $order->update([
            'status' => 'washing',
            'primary_washer_id' => $washer->id,
            'updated_by' => Auth::id()
        ]);

        // Mark washer as in use
        $washer->update(['status' => 'in_use']);

        // Create or update load record
        $load = Load::where('order_id', $order->id)->first();
        if ($load) {
            $load->update([
                'washer_machine_id' => $washer->id,
                'wash_start' => now(),
                'status' => 'washing'
            ]);
        }

        return response()->json([
            'success' => true,
            'duration' => self::WASHING_TIME_MINUTES
        ]);
    }

    /**
     * Start drying cycle
     */
    public function startDrying(Request $request, Order $order)
    {
        // Find an available dryer
        $dryer = Machine::where('type', 'dryer')
            ->where('status', 'available')
            ->first();

        if (!$dryer) {
            return response()->json([
                'success' => false, 
                'message' => 'No available dryers'
            ], 422);
        }

        // Update order status and assign dryer
        $order->update([
            'status' => 'drying',
            'primary_dryer_id' => $dryer->id,
            'updated_by' => Auth::id()
        ]);

        // Mark dryer as in use
        $dryer->update(['status' => 'in_use']);

        // Update load record
        $load = Load::where('order_id', $order->id)->first();
        if ($load) {
            $load->update([
                'dryer_machine_id' => $dryer->id,
                'wash_end' => now(),
                'dry_start' => now(),
                'status' => 'drying'
            ]);
        }

        // Calculate drying time based on weight
        $dryingTime = $this->calculateDryingTime($order->weight);

        return response()->json([
            'success' => true,
            'duration' => $dryingTime
        ]);
    }

    public function recordPayment(Request $request, Order $order)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date_format:Y-m-d H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        // Calculate remaining balance
        $remainingBalance = $order->total_amount - $order->amount_paid;

        // Allow overpayment (customer might give more cash for change)
        // Just ensure the payment amount is positive
        if ($data['amount'] <= 0) {
            return response()->json([
                'success' => false,
                'message' => "Payment amount must be greater than zero"
            ], 422);
        }

        // Calculate change if overpaid (payment amount - remaining balance before this payment)
        $remainingBalanceBeforePayment = $order->total_amount - $order->amount_paid;
        $change = $data['amount'] - $remainingBalanceBeforePayment;

        // Create payment record with cash_given and change
        $payment = $order->payments()->create([
            'amount' => $data['amount'],
            'cash_given' => $data['amount'],
            'change' => $change > 0 ? $change : 0,
            'payment_date' => $data['payment_date'],
            'recorded_by' => Auth::id(),
            'notes' => $data['notes'] ?? null,
        ]);

        // Update order's amount_paid (cap it at total_amount to avoid overpayment in balance calculation)
        $newAmountPaid = min($order->amount_paid + $data['amount'], $order->total_amount);
        $order->update([
            'amount_paid' => $newAmountPaid,
            'updated_by' => Auth::id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => "Payment of ₱" . number_format($data['amount'], 2) . " recorded successfully" . ($change > 0 ? " (Change: ₱" . number_format($change, 2) . ")" : ""),
            'amount_paid' => $newAmountPaid,
            'balance' => $order->total_amount - $newAmountPaid,
            'change' => $change > 0 ? $change : 0,
            'payment' => $payment
        ]);
    }

    public function downloadReceipt(Order $order)
    {
        // Load payments relationship
        $order->load('payments.recordedBy');
        
        return view('user.orders.receipt', compact('order'));
    }

    public function sendReceiptEmail(Order $order)
    {
        // Check if order has an associated online customer with user
        if (!$order->customer->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'This is not an online customer order.'
            ], 422);
        }

        $user = User::find($order->customer->user_id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found.'
            ], 422);
        }

        try {
            // Send receipt notification to customer
            $user->notify(new \App\Notifications\OrderReceiptNotification($order));

            return response()->json([
                'success' => true,
                'message' => "Receipt notification sent to {$user->name}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendOrderUpdate(Request $request, Order $order)
    {
        $data = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // Check if order has an associated online customer with user
        if (!$order->customer->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'This is not an online customer order.'
            ], 422);
        }

        $user = User::find($order->customer->user_id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found.'
            ], 422);
        }

        try {
            // Send order update notification to customer
            $user->notify(new \App\Notifications\OrderUpdateNotification($order, $data['message']));

            return response()->json([
                'success' => true,
                'message' => "Update notification sent to {$user->name}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ], 500);
        }
    }
}
