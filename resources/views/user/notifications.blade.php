@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
            <p class="text-gray-600 mt-2">View your order updates and receipts</p>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                <div class="bg-white rounded-lg shadow p-6 border-l-4 {{ $notification->read_at ? 'border-gray-300' : 'border-blue-500' }}" id="notification-{{ $notification->id }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    @if($notification->data['type'] === 'receipt')
                                        📄 {{ $notification->data['title'] }}
                                    @elseif($notification->data['type'] === 'update')
                                        📢 {{ $notification->data['title'] }}
                                    @else
                                        {{ $notification->data['title'] }}
                                    @endif
                                </h3>
                                @if(!$notification->read_at)
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">New</span>
                                @endif
                            </div>
                            <p class="text-gray-700 mt-2">{{ $notification->data['message'] }}</p>
                            
                            <!-- Notification Details -->
                            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Order ID</p>
                                    <p class="font-semibold text-gray-900">#{{ str_pad($notification->data['data']['order_id'], 5, '0', STR_PAD_LEFT) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Status</p>
                                    <p class="font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $notification->data['data']['status'])) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Total Amount</p>
                                    <p class="font-semibold text-gray-900">₱{{ number_format($notification->data['data']['total_amount'], 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Amount Paid</p>
                                    <p class="font-semibold text-gray-900">₱{{ number_format($notification->data['data']['amount_paid'], 2) }}</p>
                                </div>
                            </div>

                            <p class="text-xs text-gray-500 mt-4">{{ $notification->created_at->format('M d, Y g:i A') }}</p>
                        </div>

                        <!-- Actions -->
                        <div class="ml-4 flex flex-col gap-2">
                            @if($notification->data['type'] === 'receipt')
                            <a href="{{ route('user.orders.receipt', $notification->data['data']['order_id']) }}" target="_blank"
                               class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium whitespace-nowrap">
                                📥 View Receipt
                            </a>
                            @endif
                            
                            <a href="{{ route('user.orders.show', $notification->data['data']['order_id']) }}"
                               class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium whitespace-nowrap">
                                👁️ View Order
                            </a>

                            <button onclick="deleteNotification('{{ $notification->id }}')"
                                    class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium whitespace-nowrap">
                                ✕ Close
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <div class="text-6xl mb-4">📭</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Notifications</h3>
                <p class="text-gray-600">You don't have any notifications yet. Check back later for order updates and receipts.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function deleteNotification(notificationId) {
    const element = document.getElementById(`notification-${notificationId}`);
    
    fetch(`/notifications/${notificationId}/mark-as-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fade out and remove the notification
            element.style.transition = 'opacity 0.3s ease-out';
            element.style.opacity = '0';
            setTimeout(() => {
                element.remove();
                
                // Check if there are any notifications left
                const notificationsList = document.querySelector('.space-y-4');
                if (notificationsList.children.length === 0) {
                    location.reload();
                }
            }, 300);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error closing notification');
    });
}

// Auto-mark all unread notifications as read when page loads
document.addEventListener('DOMContentLoaded', function() {
    const newBadges = document.querySelectorAll('.inline-block.bg-blue-100');
    
    newBadges.forEach(badge => {
        const notificationDiv = badge.closest('[id^="notification-"]');
        if (notificationDiv) {
            const notificationId = notificationDiv.id.replace('notification-', '');
            
            // Auto mark as read after 2 seconds
            setTimeout(() => {
                fetch(`/notifications/${notificationId}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI: remove "New" badge and change border color
                        badge.remove();
                        notificationDiv.classList.remove('border-blue-500');
                        notificationDiv.classList.add('border-gray-300');
                    }
                })
                .catch(error => console.error('Error:', error));
            }, 2000);
        }
    });
});
</script>
@endsection
