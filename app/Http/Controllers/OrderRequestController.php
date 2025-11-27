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

        return view('admin.order_requests.index', compact('requests')); // Match the plural directory
    }

    // Show create form
    public function create()
    {
        return view('admin.order_requests.create'); // Plural
    }

    // Store new order request
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:pending,in_progress,ready,completed,cancelled',
            'weight' => 'required|numeric|min:0',
            'add_ons' => 'nullable|array',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'pickup_date' => 'nullable|date',
            'estimated_finish' => 'required|date',
            'finished_at' => 'nullable|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        if (isset($data['add_ons'])) {
            $data['add_ons'] = json_encode($data['add_ons']);
        }

        OrderRequest::create($data);

        return redirect()->route('admin.order_request.index')
            ->with('success', 'Order request created successfully.');
    }

    // Show a single order request
    public function show(OrderRequest $orderRequest)
    {
        $orderRequest->load(['customer', 'order', 'creator', 'updater']);

        return view('admin.order_requests.show', compact('orderRequest')); // Plural
    }

    // Edit form
    public function edit(OrderRequest $orderRequest)
    {
        return view('admin.order_requests.edit', compact('orderRequest')); // Plural
    }

    // Update
    public function update(Request $request, OrderRequest $orderRequest)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:pending,in_progress,ready,completed,cancelled',
            'weight' => 'required|numeric|min:0',
            'add_ons' => 'nullable|array',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'pickup_date' => 'nullable|date',
            'estimated_finish' => 'required|date',
            'finished_at' => 'nullable|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        $data['updated_by'] = Auth::id();

        if (isset($data['add_ons'])) {
            $data['add_ons'] = json_encode($data['add_ons']);
        }

        $orderRequest->update($data);

        return redirect()->route('admin.order_request.index')
            ->with('success', 'Order request updated successfully.');
    }

    // Delete
    public function destroy(OrderRequest $orderRequest)
    {
        $orderRequest->delete();

        return redirect()->route('admin.order_request.index')
            ->with('success', 'Order request deleted successfully.');
    }
}