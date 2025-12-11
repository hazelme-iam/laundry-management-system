<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // List customers
    public function index()
    {
        // Get users who don't have customer records and create a collection (exclude admin)
        $usersWithoutCustomers = \App\Models\User::whereDoesntHave('customer')
            ->where('role', '!=', 'admin')  // Exclude admin users
            ->get()
            ->map(function ($user) {
                // Create a virtual customer object for users without customer records
                return (object) [
                    'id' => 'user_' . $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? null,
                    'address' => null,
                    'customer_type' => 'online',
                    'orders_count' => $user->orders()->count(),
                    'orders_sum_total_amount' => $user->orders()->sum('total_amount'),
                    'online_orders_count' => $user->orders()->count(),
                    'walkin_orders_count' => 0,
                    'last_order_at' => $user->orders()->max('orders.created_at'),
                    'created_at' => $user->created_at,
                    'is_virtual' => true,
                    'user_id' => $user->id
                ];
            });

        // Get existing customers with their data
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

        // Apply filters to existing customers
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

        // Apply source filter to existing customers
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

        // Apply search to existing customers
        if ($term = request('q')) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        // Also search users without customers
        if ($term = request('q')) {
            $usersWithoutCustomers = $usersWithoutCustomers->filter(function ($user) use ($term) {
                return stripos($user->name, $term) !== false || 
                       stripos($user->email, $term) !== false || 
                       ($user->phone && stripos($user->phone, $term) !== false);
            });
        }

        $existingCustomers = $query->latest()->get();

        // Combine both collections
        $allCustomers = $usersWithoutCustomers->concat($existingCustomers);

        // Sort by creation date (newest first)
        $allCustomers = $allCustomers->sortByDesc('created_at');

        // Paginate manually
        $page = request('page', 1);
        $perPage = 10;
        $total = $allCustomers->count();
        $customers = new \Illuminate\Pagination\LengthAwarePaginator(
            $allCustomers->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

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

    // Show user customer details
    public function showUser(User $user)
    {
        // Create a virtual customer object for the user
        $virtualCustomer = (object) [
            'id' => 'user_' . $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'address' => null,
            'customer_type' => 'online',
            'orders_count' => $user->orders()->count(),
            'orders_sum_total_amount' => $user->orders()->sum('total_amount'),
            'online_orders_count' => $user->orders()->count(),
            'walkin_orders_count' => 0,
            'last_order_at' => $user->orders()->max('orders.created_at') ?: null,
            'created_at' => $user->created_at,
            'is_virtual' => true,
            'user_id' => $user->id
        ];

        // Use the same show view as regular customers
        return view('admin.customers.show', ['customer' => $virtualCustomer]);
    }
}