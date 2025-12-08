<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // List customers
    public function index()
{
    $query = Customer::query()
        ->withCount('orders')
        ->withSum('orders', 'total_amount')
        ->withCount([
            // Orders placed online by the customer (created_by == customers.user_id)
            'orders as online_orders_count' => function ($q) {
                $q->whereColumn('orders.created_by', 'customers.user_id');
            },
            // Orders created by admins (walk-ins)
            'orders as walkin_orders_count' => function ($q) {
                $q->whereColumn('orders.created_by', '!=', 'customers.user_id');
            },
        ]);

    // Existing "filter" (new/returning/active)
    if (request('filter')) {
        switch (request('filter')) {
            case 'new':
                $query->where('created_at', '>=', now()->subDays(30));
                break;
            case 'returning':
                $query->whereHas('orders', function ($q) {
                    $q->where('created_at', '<', now()->subDays(30));
                })->where('created_at', '<', now()->subDays(30));
                break;
            case 'active':
                $query->whereHas('orders', function ($q) {
                    $q->whereDate('created_at', today());
                });
                break;
        }
    }

    // New "source" filter: walk-in (admin-created) vs online (customer-placed)
    if (request('source')) {
        if (request('source') === 'online') {
            $query->whereHas('orders', function ($q) {
                $q->whereColumn('orders.created_by', 'customers.user_id');
            });
        } elseif (request('source') === 'walk_in') {
            $query->whereHas('orders', function ($q) {
                $q->whereColumn('orders.created_by', '!=', 'customers.user_id');
            });
        }
    }

    // Optional text search
    if ($term = request('q')) {
        $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%");
        });
    }

    $customers = $query->latest()->paginate(10);
    return view('admin.customers.index', compact('customers'));
}

    // Show create form
    public function create()
    {
        return view('admin.customers.create'); // Changed from 'customers.create' to 'admin.customers.create'
    }

    // Store a new customer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'required|string|max:20',
            'address'       => 'nullable|string',
            'customer_type' => 'required|in:walk-in,regular,vip',
            'notes'         => 'nullable|string',
        ]);

        Customer::create($validated);

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer created successfully.');
    }

    // Show specific customer
    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    // Show edit form
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    // Update customer
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'required|string|max:20',
            'address'       => 'nullable|string',
            'customer_type' => 'required|in:walk-in,regular,vip',
            'notes'         => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer updated.');
    }

    // Delete customer
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer deleted.');
    }
}