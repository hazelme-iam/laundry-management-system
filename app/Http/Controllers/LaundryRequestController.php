<?php

namespace App\Http\Controllers;

use App\Models\LaundryRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaundryRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // List all order requests
    public function index()
    {
        $requests = LaundryRequest::with(['customer', 'order', 'creator', 'updater'])
            ->latest()
            ->paginate(10);

        return view('admin.laundry_requests.index', compact('requests'));
    }

    // Show create form
    public function create()
    {
        return view('admin.laundry_requests.create');
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

        LaundryRequest::create($data);

        return redirect()->route('admin.laundry_request.index')
            ->with('success', 'Laundry request created successfully.');
    }

    // Show a single order request
    public function show(LaundryRequest $laundryRequest)
    {
        $laundryRequest->load(['customer', 'order', 'creator', 'updater']);

        return view('admin.laundry_requests.show', compact('laundryRequest'));
    }

    // Edit form
    public function edit(LaundryRequest $laundryRequest)
    {
        return view('admin.laundry_requests.edit', compact('laundryRequest'));
    }

    // Update
    public function update(Request $request, LaundryRequest $laundryRequest)
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

        $laundryRequest->update($data);

        return redirect()->route('admin.laundry_request.index')
            ->with('success', 'Laundry request updated successfully.');
    }

    // Approve order request and convert to order
    public function approve(LaundryRequest $laundryRequest)
    {
        if ($laundryRequest->status !== 'pending') {
            return redirect()->route('admin.laundry_request.index')
                ->with('error', 'Only pending requests can be approved.');
        }

        try {
            // Create an order from the request
            $orderData = [
                'customer_id' => $laundryRequest->customer_id,
                'weight' => $laundryRequest->weight,
                'add_ons' => $laundryRequest->add_ons,
                'subtotal' => $laundryRequest->subtotal,
                'discount' => $laundryRequest->discount,
                'total_amount' => $laundryRequest->total_amount,
                'amount_paid' => $laundryRequest->amount_paid,
                'pickup_date' => $laundryRequest->pickup_date,
                'estimated_finish' => $laundryRequest->estimated_finish,
                'remarks' => $laundryRequest->remarks,
                'status' => 'pending', // Order starts as pending
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            $order = Order::create($orderData);

            // Link the request to the created order and mark as approved
            $laundryRequest->update([
                'status' => 'approved',
                'order_id' => $order->id,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('admin.orders.index')
                ->with('success', "Laundry request approved! Order #{$order->id} created successfully.");

        } catch (\Exception $e) {
            return redirect()->route('admin.laundry_request.index')
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    // Decline order request
    public function decline(LaundryRequest $laundryRequest)
    {
        if ($laundryRequest->status !== 'pending') {
            return redirect()->route('admin.laundry_request.index')
                ->with('error', 'Only pending requests can be declined.');
        }

        $laundryRequest->update([
            'status' => 'rejected',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.laundry_request.index')
            ->with('success', 'Laundry request declined successfully.');
    }

    // Delete
    public function destroy(LaundryRequest $laundryRequest)
    {
        $laundryRequest->delete();

        return redirect()->route('admin.laundry_request.index')
            ->with('success', 'Laundry request deleted successfully.');
    }
}