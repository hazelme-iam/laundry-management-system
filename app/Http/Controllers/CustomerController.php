<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // List customers
    public function index()
    {
        $customers = Customer::latest()->paginate(10);
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