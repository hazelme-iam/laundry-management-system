{{-- Reusable Order Status Table Component --}}
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Customer Name</th>
                <th>Kilo of Laundry</th>
                <th>Status</th>
                <th>Recommendations</th>
                @if(isset($showActions) && $showActions)
                    <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->customer_name ?? $order['customer_name'] }}</td>
                <td>{{ ($order->kilo ?? $order['kilo']) }} kg</td>
                <td>
                    @php
                        $status = $order->status ?? $order['status'];
                        $statusClass = match($status) {
                            'Pending' => 'bg-warning',
                            'Washing' => 'bg-info',
                            'Drying' => 'bg-primary',
                            'Ready for Pickup' => 'bg-success',
                            'Completed' => 'bg-dark',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $status }}</span>
                </td>
                <td>
                    @if($order->recommendations ?? $order['recommendations'] ?? null)
                        <small>{{ $order->recommendations ?? $order['recommendations'] }}</small>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                @if(isset($showActions) && $showActions)
                <td>
                    {{-- Add action buttons here if needed --}}
                    <button class="btn btn-sm btn-outline-primary">View</button>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ isset($showActions) && $showActions ? 5 : 4 }}" class="text-center text-muted">
                    No orders found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>