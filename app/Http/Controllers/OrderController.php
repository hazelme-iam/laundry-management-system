<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Machine;
use App\Models\Load;
use App\Models\User;
use App\Services\CacheService;
use App\Services\NotificationService;
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
        $query = Order::with(['customer', 'creator']);

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
        $customers = CacheService::getCustomerList();
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
            'add_ons' => 'nullable|array',
            'add_ons.*' => 'string|in:detergent,fabric_conditioner',
            'subtotal' => ['required', new ValidMonetary],
            'discount' => ['required', new ValidMonetary],
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

        // Validate business logic
        if ($data['discount'] > $data['subtotal']) {
            return back()->withErrors(['discount' => 'Discount cannot be greater than subtotal.'])->withInput();
        }

        if ($data['amount_paid'] > $data['total_amount']) {
            return back()->withErrors(['amount_paid' => 'Amount paid cannot be greater than total amount.'])->withInput();
        }

        // Set default status to 'pending' for new orders
        $data['status'] = 'pending';
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $order = Order::create($data);
        
        // Automatically create optimized loads
        $loads = $order->createOptimizedLoads();

        // Clear relevant caches
        CacheService::clearOrderRelatedCaches();
        CacheService::clearCustomerCache();

        // Send notifications
        NotificationService::newOrderCreated($order);

        return redirect()->route('admin.orders.index')->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Always load fresh data for Livewire auto-refresh
        $order->load(['customer', 'creator', 'updater', 'loads.washerMachine', 'loads.dryerMachine', 'assignedWasher', 'assignedDryer']);
        
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

        // Store original status for notification
        $originalStatus = $order->status;

        $order->update($data);

        // Clear relevant caches
        CacheService::clearOrderRelatedCaches();
        CacheService::clearCustomerCache();

        // Send notifications if status changed
        if (isset($data['status']) && $data['status'] !== $originalStatus) {
            NotificationService::orderStatusChanged($order);
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        
        // Clear relevant caches
        CacheService::clearOrderRelatedCaches();
        
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
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
        
        // Base price: 150 per kilo (minimum)
        $basePrice = 150;
        $subtotal = $basePrice * max(1, $weight);
        
        // Add-on prices
        $addOnPrices = [
            'detergent' => 16,
            'fabric_conditioner' => 14,
        ];
        
        foreach ($addOns as $addOn) {
            if (isset($addOnPrices[$addOn])) {
                $subtotal += $addOnPrices[$addOn];
            }
        }
        
        return response()->json([
            'subtotal' => number_format($subtotal, 2),
            'total_amount' => number_format($subtotal, 2),
        ]);
    }

    /**
     * Validate if status transition is allowed
     */
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
    public function userIndex()
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

        // Get orders for this customer
        $orders = Order::where('customer_id', $customer->id)
            ->with(['customer', 'creator'])
            ->latest()
            ->paginate(10);

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

        // Create order with pending status for admin approval
        $orderData = [
            'customer_id' => $customer->id,
            'weight' => $weight,
            'add_ons' => $data['add_ons'] ?? null,
            'subtotal' => $data['subtotal'],
            'discount' => $data['discount'] ?? 0,
            'total_amount' => $data['total_amount'] ?? 0,
            'amount_paid' => $data['amount_paid'] ?? 0,
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
        $baseTime = $weight * self::DRYING_TIME_PER_KG;
        
        // Ensure it's within min/max bounds
        if ($baseTime < self::MIN_DRYING_TIME) {
            return self::MIN_DRYING_TIME;
        } elseif ($baseTime > self::MAX_DRYING_TIME) {
            return self::MAX_DRYING_TIME;
        }
        
        return (int) $baseTime;
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,picked_up,washing,drying,folding,quality_check,ready,ready_for_pickup,delivery_pending,completed,cancelled'
        ]);

        $order->update([
            'status' => $request->status,
            'updated_by' => Auth::id()
        ]);

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
}
