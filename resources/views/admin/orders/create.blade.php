{{-- resources/views/admin/orders/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Order</h1>

    <form action="{{ route('admin.orders.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="customer_id" class="form-label">Customer</label>
            <select class="form-select" id="customer_id" name="customer_id" required>
                <option value="">Select Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="ready">Ready</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="weight" class="form-label">Weight (KG)</label>
            <input type="number" step="0.01" class="form-control" id="weight" name="weight" required>
        </div>

        <div class="mb-3">
            <label for="add_ons" class="form-label">Add-ons</label>
            <input type="text" class="form-control" id="add_ons" name="add_ons" placeholder="Comma separated e.g. stain_removal, fragrance">
        </div>

        <div class="mb-3">
            <label for="subtotal" class="form-label">Subtotal</label>
            <input type="number" step="0.01" class="form-control" id="subtotal" name="subtotal" required>
        </div>

        <div class="mb-3">
            <label for="discount" class="form-label">Discount</label>
            <input type="number" step="0.01" class="form-control" id="discount" name="discount" value="0">
        </div>

        <div class="mb-3">
            <label for="total_amount" class="form-label">Total Amount</label>
            <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" required>
        </div>

        <div class="mb-3">
            <label for="amount_paid" class="form-label">Amount Paid</label>
            <input type="number" step="0.01" class="form-control" id="amount_paid" name="amount_paid" value="0">
        </div>

        <div class="mb-3">
            <label for="pickup_date" class="form-label">Pickup Date</label>
            <input type="date" class="form-control" id="pickup_date" name="pickup_date">
        </div>

        <div class="mb-3">
            <label for="estimated_finish" class="form-label">Estimated Finish</label>
            <input type="datetime-local" class="form-control" id="estimated_finish" name="estimated_finish" required>
        </div>

        <div class="mb-3">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create Order</button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
