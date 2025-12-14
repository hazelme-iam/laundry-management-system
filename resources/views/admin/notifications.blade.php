{{-- resources/views/admin/notifications.blade.php --}}
<x-sidebar-app>
    <div class="min-h-screen bg-gray-100">
        <div class="py-6 md:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Breadcrumb Navigation -->
                <div class="mb-6">
                    <x-breadcrumbs :items="['Notifications' => null]" />
                </div>
                
                <!-- Header Card -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Notifications</h1>
                                <p class="text-gray-600 mt-1">System alerts and order updates</p>
                            </div>
                            @if($unreadCount > 0)
                                <button onclick="markAllAsRead()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                                    Mark All as Read
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="border-b border-gray-200">
                        <div class="px-6 py-4 flex gap-2 overflow-x-auto">
                            <a href="{{ route('admin.notifications.index') }}" class="px-4 py-2 font-medium whitespace-nowrap {{ !request('type') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                                All ({{ $totalCount ?? 0 }})
                            </a>
                            <a href="{{ route('admin.notifications.index', ['type' => 'new_order']) }}" class="px-4 py-2 font-medium whitespace-nowrap {{ request('type') === 'new_order' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                                New Orders ({{ $newOrderCount ?? 0 }})
                            </a>
                            <a href="{{ route('admin.notifications.index', ['type' => 'capacity_alert']) }}" class="px-4 py-2 font-medium whitespace-nowrap {{ request('type') === 'capacity_alert' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                                Capacity Alerts ({{ $capacityAlertCount ?? 0 }})
                            </a>
                            <a href="{{ route('admin.notifications.index', ['type' => 'order_backlog']) }}" class="px-4 py-2 font-medium whitespace-nowrap {{ request('type') === 'order_backlog' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                                Backlog Orders ({{ $backlogCount ?? 0 }})
                            </a>
                        </div>
                    </div>

                    <!-- Notifications List -->
                    <div class="divide-y divide-gray-200">
                        @if($notifications->count() > 0)
                            @foreach($notifications as $notification)
                            <div class="p-6 hover:bg-gray-50 transition" id="notification-{{ $notification->id }}">
                                <div class="flex items-start gap-4">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        @switch($notification->type)
                                            @case('order_status')
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                    </svg>
                                                </div>
                                                @break
                                            @case('capacity_alert')
                                                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                </div>
                                                @break
                                            @case('order_backlog')
                                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                @break
                                            @case('new_order')
                                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </div>
                                                @break
                                            @default
                                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                        @endswitch
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="text-sm font-semibold text-gray-900">{{ $notification->title }}</h3>
                                            @if(!$notification->read_at)
                                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Unread</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600">{{ $notification->message }}</p>
                                        <p class="text-xs text-gray-500 mt-2">{{ $notification->created_at->format('M d, Y g:i A') }}</p>

                                        <!-- Notification Details -->
                                        @if($notification->data && (isset($notification->data['order_id']) || isset($notification->data['washer_utilization'])))
                                        <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                            @if(isset($notification->data['order_id']))
                                            <div>
                                                <p class="text-gray-600 text-xs">Order ID</p>
                                                <p class="font-semibold text-gray-900">#{{ str_pad($notification->data['order_id'], 3, '0', STR_PAD_LEFT) }}</p>
                                            </div>
                                            @endif
                                            @if(isset($notification->data['weight']))
                                            <div>
                                                <p class="text-gray-600 text-xs">Weight</p>
                                                <p class="font-semibold text-gray-900">{{ $notification->data['weight'] }}kg</p>
                                            </div>
                                            @endif
                                            @if(isset($notification->data['washer_utilization']))
                                            <div>
                                                <p class="text-gray-600 text-xs">Washer Utilization</p>
                                                <p class="font-semibold text-gray-900">{{ $notification->data['washer_utilization'] }}%</p>
                                            </div>
                                            @endif
                                            @if(isset($notification->data['customer_name']))
                                            <div>
                                                <p class="text-gray-600 text-xs">Customer</p>
                                                <p class="font-semibold text-gray-900">{{ $notification->data['customer_name'] }}</p>
                                            </div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="ml-4 flex gap-2 flex-shrink-0">
                                        @if(isset($notification->data['url']))
                                        <a href="{{ $notification->data['url'] }}"
                                           onclick="markAsRead({{ $notification->id }})"
                                           class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium whitespace-nowrap">
                                            View
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
                            <div class="px-6 py-4 border-t border-gray-200">
                                {{ $notifications->links() }}
                            </div>
                            @endif
                        @else
                            <div class="p-12 text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Notifications</h3>
                                <p class="text-gray-600">All caught up! You don't have any notifications.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
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
            element.style.transition = 'opacity 0.3s ease-out';
            element.style.opacity = '0';
            setTimeout(() => {
                element.remove();
                
                const notificationsList = document.querySelector('.divide-y');
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
</x-sidebar-app>
