<x-sidebar-app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumb Navigation -->
            <x-breadcrumbs :items="[
                'Laundry Requests' => route('admin.laundry_request.index'),
                'Create Request' => null
            ]" />

            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Laundry Request</h1>
                    <p class="text-gray-600">Add a new laundry request for a customer</p>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">

    <form action="{{ route('admin.laundry_request.store') }}" method="POST">
        @csrf

        {{-- Customer --}}
        <div class="mb-3">
            <label class="form-label">Customer</label>
            <select name="customer_id" class="form-control" required>
                <option value="">-- Select Customer --</option>
                @foreach(App\Models\Customer::all() as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="ready">Ready</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        {{-- Weight --}}
        <div class="mb-3">
            <label class="form-label">Weight (kg)</label>
            <input type="number" name="weight" class="form-control" step="0.01" required>
        </div>

        {{-- Subtotal --}}
        <div class="mb-3">
            <label class="form-label">Subtotal</label>
            <input type="number" name="subtotal" class="form-control" step="0.01" required>
        </div>

        {{-- Discount --}}
        <div class="mb-3">
            <label class="form-label">Discount</label>
            <input type="number" name="discount" class="form-control" step="0.01" required>
        </div>

        {{-- Total Amount --}}
        <div class="mb-3">
            <label class="form-label">Total Amount</label>
            <input type="number" name="total_amount" class="form-control" step="0.01" required>
        </div>

        {{-- Amount Paid --}}
        <div class="mb-3">
            <label class="form-label">Amount Paid</label>
            <input type="number" name="amount_paid" class="form-control" step="0.01" required>
        </div>

        {{-- Pickup Date --}}
        <div class="mb-3">
            <label class="form-label">Pickup Date</label>
            <input type="date" name="pickup_date" class="form-control">
        </div>

        {{-- Estimated Finish --}}
        <div class="mb-3">
            <label class="form-label">Estimated Finish</label>
            <input type="datetime-local" name="estimated_finish" class="form-control" required>
        </div>

        {{-- Remarks --}}
        <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea name="remarks" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create Laundry Request</button>
        <a href="{{ route('admin.laundry_request.index') }}" class="btn btn-secondary">Cancel</a>

            </form>
        </div>
    </div>
</x-sidebar-app>
