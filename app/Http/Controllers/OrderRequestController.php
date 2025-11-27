<?php

namespace App\Http\Controllers;

use App\Models\OrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // List all order requests
    public function index()
    {
        $requests = OrderRequest::with(['customer', 'order', 'creator', 'updater'])
            ->latest()
            ->paginate(10);

        return view('order_requests.index', compact('requests'));
    }

    // Show create form
    public function create()
    {
        return view('order_requests.create');
    }

    // Store a new order request
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:pending,in_progress,ready,completed,cancelled',
            'weight' => 'required|numeric',
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

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        OrderRequest::create($data);

        return redirect()->route('order_requests.index')->with('success', 'Order request created successfully.');
    }

    // Show a single order request
    public function show(OrderRequest $orderRequest)
    {
        $orderRequest->load(['customer', 'order', 'creator', 'updater']);
        return view('order_requests.show', compact('orderRequest'));
    }

    // Show edit form
    public function edit(OrderRequest $orderRequest)
    {
        return view('order_requests.edit', compact('orderRequest'));
    }

    // Update order request
    public function update(Request $request, OrderRequest $orderRequest)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:pending,in_progress,ready,completed,cancelled',
            'weight' => 'required|numeric',
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

        $orderRequest->update($data);

        return redirect()->route('order_requests.index')->with('success', 'Order request updated successfully.');
    }

    // Delete order request
    public function destroy(OrderRequest $orderRequest)
    {
        $orderRequest->delete();
        return redirect()->route('order_requests.index')->with('success', 'Order request deleted successfully.');
    }
}
