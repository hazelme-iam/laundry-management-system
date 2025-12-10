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
        return view('admin.customers.create');
    }

    // Store a new customer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'nullable|email|max:255',
            'phone'     => 'required|string|max:20',
            'barangay'  => 'nullable|string|max:255',
            'purok'     => 'nullable|string|max:255',
            'street'    => 'nullable|string|max:255',
            'address'   => 'nullable|string',
            'notes'     => 'nullable|string',
        ]);

        // Build the complete address
        $addressParts = [];
        if ($request->filled('barangay')) {
            $addressParts[] = $request->barangay;
        }
        if ($request->filled('purok')) {
            $addressParts[] = $request->purok;
        }
        if ($request->filled('street')) {
            $addressParts[] = $request->street;
        }
        if ($request->filled('address')) {
            $addressParts[] = $request->address;
        }
        
        $completeAddress = !empty($addressParts) ? implode(', ', $addressParts) : null;

        // Create customer
        Customer::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'],
            'barangay'      => $validated['barangay'],
            'purok'         => $validated['purok'],
            'street'        => $validated['street'],
            'address'       => $completeAddress,
            'notes'         => $validated['notes'],
            'customer_type' => 'walk-in', // Default type for admin-created customers
        ]);

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer created successfully.');
    }

    // Show specific customer
    public function show(Customer $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    // Show edit form
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    // Update customer
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'nullable|email|max:255',
            'phone'     => 'required|string|max:20',
            'barangay'  => 'nullable|string|max:255',
            'purok'     => 'nullable|string|max:255',
            'street'    => 'nullable|string|max:255',
            'address'   => 'nullable|string',
            'notes'     => 'nullable|string',
        ]);

        // Build the complete address
        $addressParts = [];
        if ($request->filled('barangay')) {
            $addressParts[] = $request->barangay;
        }
        if ($request->filled('purok')) {
            $addressParts[] = $request->purok;
        }
        if ($request->filled('street')) {
            $addressParts[] = $request->street;
        }
        if ($request->filled('address')) {
            $addressParts[] = $request->address;
        }
        
        $completeAddress = !empty($addressParts) ? implode(', ', $addressParts) : null;

        $customer->update([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'],
            'barangay'  => $validated['barangay'],
            'purok'     => $validated['purok'],
            'street'    => $validated['street'],
            'address'   => $completeAddress,
            'notes'     => $validated['notes'],
            // Keep existing customer_type unless changed
        ]);

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer updated.');
    }

    // Delete customer - ADDED VALIDATION FOR ORDERS
    public function destroy(Customer $customer)
    {
        // Check if customer has orders
        if ($customer->orders()->count() > 0) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Cannot delete customer with existing orders. Please delete their orders first.');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer deleted successfully.');
    }
}