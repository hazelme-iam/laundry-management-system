{{-- resources/views/components/status-dropdown.blade.php --}}
@props([
    'order',
    'formAction' => null,
])

@php
    $statusConfig = [
        'pending' => ['bg-yellow-100', 'text-yellow-800', 'border-yellow-300'],
        'in_progress' => ['bg-blue-100', 'text-blue-800', 'border-blue-300'],
        'ready' => ['bg-indigo-100', 'text-indigo-800', 'border-indigo-300'],
        'completed' => ['bg-green-100', 'text-green-800', 'border-green-300'],
        'cancelled' => ['bg-red-100', 'text-red-800', 'border-red-300'],
    ];
    
    $currentConfig = $statusConfig[$order->status] ?? $statusConfig['pending'];
@endphp

<form action="{{ $formAction ?? route('admin.orders.update', $order) }}" method="POST" class="inline">
    @csrf
    @method('PUT')
    <select name="status" 
            onchange="this.form.submit()" 
            {{ $attributes->class([
                'text-xs border rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer',
                $currentConfig[0], $currentConfig[1], $currentConfig[2]
            ]) }}>
        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="in_progress" {{ $order->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
        <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Ready</option>
        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
    </select>
</form>