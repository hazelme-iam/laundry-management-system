<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\LaundryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index()
    {
        $orders = Order::with(['customer', 'creator', 'updater'])->latest()->paginate(10);
        
        // Add statistics for the dashboard cards
        $pendingCount = Order::where('status', 'pending')->count();
        $inProgressCount = Order::where('status', 'in_progress')->count();
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
            'weight' => 'required|numeric|min:1',
            'add_ons' => 'nullable|array',
            'subtotal' => 'required|numeric',
            'discount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'amount_paid' => 'required|numeric',
            'pickup_date' => 'nullable|date',
            'estimated_finish' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        // Set default status to 'pending' for new orders
        $data['status'] = 'pending';
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        Order::create($data);

        return redirect()->route('admin.orders.index')->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'creator', 'updater']);
        return view('admin.orders.show', compact('order'));
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
            'status' => 'required|in:pending,in_progress,ready,completed,cancelled',
            'weight' => 'required|numeric|min:1',
            'add_ons' => 'nullable|array',
            'subtotal' => 'required|numeric',
            'discount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'amount_paid' => 'required|numeric',
            'pickup_date' => 'nullable|date',
            'estimated_finish' => 'required|date',
            'finished_at' => 'nullable|date',
            'remarks' => 'nullable|string',
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
            'detergent' => 25,
            'fabric_conditioner' => 20,
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
        // Get order requests (pending approval)
        $laundryRequests = LaundryRequest::whereHas('customer', function($query) {
            $query->where('user_id', auth()->id());
        })->with(['customer', 'creator', 'updater'])->latest()->paginate(10);
        
        return view('user.orders.index', compact('laundryRequests'));
    }

    public function userCreate()
    {
        // Get or create customer record for the logged-in user
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

        // Get or create customer record for the logged-in user
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

        // Prepare order request data
        $orderRequestData = [
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
            'status' => 'pending', // Order Request status
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ];

        LaundryRequest::create($orderRequestData);

        return redirect()->route('user.orders.index')->with('success', 'Order request submitted successfully! Awaiting admin approval.');
    }

    public function userShow(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->customer->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $order->load(['customer', 'creator', 'updater']);
        return view('user.orders.show', compact('order'));
    }

    /**
     * Calculate order total for user orders
     */
    public function userCalculate(Request $request)
    {
        return $this->calculate($request);
    }
}