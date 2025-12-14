<x-app-layout>
    <div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">Notifications</h1>
                    <p class="text-gray-600 mt-2">Stay updated on your laundry orders</p>
                </div>
                @if($unreadCount > 0)
                <button onclick="markAllAsRead()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Mark All as Read
                </button>
                @endif
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="mb-6 flex gap-2 border-b border-gray-200 overflow-x-auto">
            <a href="{{ route('notifications.index') }}" class="px-4 py-3 font-medium whitespace-nowrap {{ !request('type') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                All ({{ $totalCount }})
            </a>
            <a href="{{ route('notifications.index', ['type' => 'order_status']) }}" class="px-4 py-3 font-medium whitespace-nowrap {{ request('type') === 'order_status' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                Status Updates ({{ $orderStatusCount }})
            </a>
            <a href="{{ route('notifications.index', ['type' => 'order_backlog']) }}" class="px-4 py-3 font-medium whitespace-nowrap {{ request('type') === 'order_backlog' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                Backlog ({{ $backlogCount }})
            </a>
            <a href="{{ route('notifications.index', ['type' => 'pickup_reminder']) }}" class="px-4 py-3 font-medium whitespace-nowrap {{ request('type') === 'pickup_reminder' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                Pickup ({{ $pickupCount }})
            </a>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 border-l-4 {{ $notification->read_at ? 'border-gray-300' : 'border-blue-500' }}" id="notification-{{ $notification->id }}">
                    <div class="flex items-start justify-between">
                        <!-- Icon and Content -->
                        <div class="flex items-start gap-4 flex-1">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                @switch($notification->type)
                                    @case('order_status')
                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        @break
                                    @case('order_backlog')
                                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        @break
                                    @case('pickup_reminder')
                                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        @break
                                    @default
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                @endswitch
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $notification->title }}</h3>
                                    @if(!$notification->read_at)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">New</span>
                                    @endif
                                    <span class="text-xs text-gray-500 ml-auto">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-700 text-sm">{{ $notification->message }}</p>
                                
                                <!-- Notification Details -->
                                @if($notification->data && isset($notification->data['order_id']))
                                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                    <div>
                                        <p class="text-gray-600 text-xs">Order ID</p>
                                        <p class="font-semibold text-gray-900">#{{ str_pad($notification->data['order_id'], 3, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                    @if(isset($notification->data['weight']))
                                    <div>
                                        <p class="text-gray-600 text-xs">Weight</p>
                                        <p class="font-semibold text-gray-900">{{ $notification->data['weight'] }}kg</p>
                                    </div>
                                    @endif
                                    @if(isset($notification->data['estimated_finish']))
                                    <div>
                                        <p class="text-gray-600 text-xs">Est. Finish</p>
                                        <p class="font-semibold text-gray-900">{{ $notification->data['estimated_finish'] }}</p>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="text-gray-600 text-xs">Type</p>
                                        <p class="font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $notification->type)) }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="ml-4 flex flex-col gap-2 flex-shrink-0">
                            @if(isset($notification->data['url']))
                            <a href="{{ $notification->data['url'] }}"
                               onclick="markAsRead({{ $notification->id }})"
                               class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium whitespace-nowrap">
                                View Order
                            </a>
                            @endif

                            <button onclick="deleteNotification('{{ $notification->id }}')"
                                    class="inline-flex items-center justify-center px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium whitespace-nowrap">
                                Dismiss
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                @if($notifications->hasPages())
                <div class="mt-8">
                    {{ $notifications->links() }}
                </div>
                @endif
            @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <div class="text-6xl mb-4">📭</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Notifications</h3>
                <p class="text-gray-600">You don't have any notifications yet. Check back later for order updates.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Optionally reload or update UI
            console.log('Notification marked as read');
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

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
                if (notificationsList && notificationsList.children.length === 0) {
                    location.reload();
                }
            }, 300);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error dismissing notification');
    });
}
</script>
    </div>
</x-app-layout>
