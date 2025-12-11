<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Machine;
use App\Models\Load;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $query = Order::with(['customer', 'creator', 'updater', 'loads', 'primaryWasher', 'primaryDryer']);

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

        $pendingCount = Order::where('status', 'pending')->count();
        $inProgressCount = Order::whereIn('status', ['picked_up', 'washing', 'drying', 'folding', 'quality_check'])->count();
        $completedCount = Order::where('status', 'completed')->count();

        return view('admin.orders.index', compact('orders', 'pendingCount', 'inProgressCount', 'completedCount'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $customers = Customer::all();
        return view('admin.orders.create', compact('customers'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'weight' => 'required|numeric|min:0.1',
            'add_ons' => 'nullable|array',
            'subtotal' => 'required|numeric',
            'discount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'amount_paid' => 'required|numeric',
            'pickup_date' => 'nullable|date',
            'estimated_finish' => 'required|date',
            'remarks' => 'nullable|string',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'service_type' => 'nullable|in:standard,express,premium',
        ]);

        // Set default status to 'pending' for new orders
        $data['status'] = 'pending';
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $order = Order::create($data);
        
        // Automatically create optimized loads
        $loads = $order->createOptimizedLoads();

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
        $customers = Customer::all();
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
            'weight' => 'required|numeric|min:0.1',
            'add_ons' => 'nullable|array',
            'subtotal' => 'required|numeric',
            'discount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'amount_paid' => 'required|numeric',
            'pickup_date' => 'nullable|date',
            'estimated_finish' => 'required|date',
            'finished_at' => 'nullable|date',
            'remarks' => 'nullable|string',
            'primary_washer_id' => 'nullable|exists:machines,id',
            'primary_dryer_id' => 'nullable|exists:machines,id',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'service_type' => 'nullable|in:standard,express,premium',
        ]);

        $data['updated_by'] = Auth::id();

        $order->update($data);

        return redirect()->route('admin.orders.index')->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }

    /**
     * Calculate order total based on weight and add-ons
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'weight' => 'required|numeric|min:1',
            'add_ons' => 'nullable|array',
        ]);

        $weight = $request->weight;
        $addOns = $request->add_ons ?? [];
        
        // Base price: 150 per kilo (minimum)
        $basePrice = 150;
        $subtotal = $weight * $basePrice;
        
        // Add-ons pricing
        $addOnPrices = [
            'folding' => 20,
            'hanger' => 15,
            'ironing' => 30,
            'detergent' => 16,
            'fabric_conditioner' => 14,
        ];
        
        $addOnsTotal = 0;
        foreach ($addOns as $addOn) {
            if (isset($addOnPrices[$addOn])) {
                $addOnsTotal += $addOnPrices[$addOn];
            }
        }
        
        $subtotal += $addOnsTotal;
        
        return response()->json([
            'subtotal' => $subtotal,
            'total_amount' => $subtotal, // Initially same as subtotal before discount
            'add_ons_total' => $addOnsTotal,
            'base_amount' => $weight * $basePrice,
        ]);
    }

    // User-specific methods
    public function userIndex()
    {
        // Implementation for user-specific order index
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
            'weight' => 'required|numeric|min:1',
            'add_ons' => 'nullable|array',
            'subtotal' => 'required|numeric',
            'discount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'amount_paid' => 'required|numeric',
            'pickup_date' => 'nullable|date',
            'estimated_finish' => 'required|date',
            'remarks' => 'nullable|string',
            'customer_phone' => 'required|string',
            'customer_address' => 'required|string',
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

        // Create order with pending status for admin approval
        $orderData = [
            'customer_id' => $customer->id,
            'weight' => $data['weight'],
            'add_ons' => $data['add_ons'] ?? null,
            'subtotal' => $data['subtotal'],
            'discount' => $data['discount'],
            'total_amount' => $data['total_amount'],
            'amount_paid' => $data['amount_paid'],
            'pickup_date' => $data['pickup_date'],
            'estimated_finish' => $data['estimated_finish'],
            'remarks' => $data['remarks'],
            'status' => 'pending', // Pending admin approval
            'priority' => 'normal',
            'service_type' => 'standard',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ];

        $order = Order::create($orderData);

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
            'status' => 'required|in:pending,approved,rejected,picked_up,washing,drying,folding,quality_check,ready,delivery_pending,completed,cancelled'
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
