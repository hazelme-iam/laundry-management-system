<!-- Notification Bell Component -->
<div class="relative" x-data="notificationBellData()">
    <!-- Notification Bell -->
    <button @click="open = !open" data-bell-button class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg">
        <!-- Bell Icon -->
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Notification Badge -->
        @if($unreadCount > 0)
            <span data-unread-count class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50 flex flex-col max-h-96">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex-shrink-0">
            <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
            @if($unreadCount > 0)
                <button @click.stop="markAllAsRead()" type="button" class="text-xs text-blue-600 hover:text-blue-800 mt-1 font-medium">
                    Mark all as read
                </button>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="overflow-y-auto flex-1" data-notifications-list>
            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                    <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors" data-notification-id="{{ $notification->id }}">
                        <!-- Notification Icon based on type -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                @switch($notification->type)
                                    @case('order_status')
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        @break
                                    @case('capacity_alert')
                                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                        </div>
                                        @break
                                    @case('new_order')
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </div>
                                        @break
                                    @case('pickup_reminder')
                                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        @break
                                    @case('order_backlog')
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        @break
                                    @case('washing_completed')
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        @break
                                    @case('drying_completed')
                                        <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                        </div>
                                        @break
                                    @case('machine_available')
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        @break
                                    @default
                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                @endswitch
                            </div>
                            
                            <!-- Content -->
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $notification->formatted_created_at }}</p>
                                
                                <!-- Action Button -->
                                @if(isset($notification->data['url']))
                                    <button @click="viewDetails('{{ $notification->data['url'] }}', {{ $notification->id }})"
                                       class="inline-flex items-center mt-2 text-xs text-blue-600 hover:text-blue-800 transition-colors">
                                        View Details
                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            
                            <!-- Mark as Read Button (X) -->
                            <button @click.stop="markAsRead({{ $notification->id }}, $event)" 
                                    class="ml-2 text-gray-400 hover:text-gray-600 transition-colors"
                                    title="Mark as read">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="px-4 py-8 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="text-sm text-gray-500">No new notifications</p>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200 flex-shrink-0">
            <a href="{{ route('notifications.index') }}" class="block w-full text-center px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-medium text-sm transition">
                View All Notifications →
            </a>
        </div>
    </div>
</div>

<script>
// Notification bell Alpine.js data function
function notificationBellData() {
    return {
        open: false,
        viewDetails(url, notificationId) {
            // Mark as read first, then navigate
            this.markAsReadAndNavigate(notificationId, url);
        },
        
        markAsReadAndNavigate(notificationId, url) {
            // Get CSRF token safely
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : null;
            
            if (!csrfToken) {
                console.error('CSRF token not found');
                // Still navigate even if marking as read fails
                window.location.href = url;
                return;
            }
            
            fetch(`/notifications/${notificationId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({}),
            })
            .then(response => {
                // Navigate regardless of mark-as-read success
                window.location.href = url;
            })
            .catch(error => {
                console.error('Error marking as read:', error);
                // Still navigate even if there's an error
                window.location.href = url;
            });
        },
        
        markAsRead(notificationId, event = null) {
            // Prevent default behavior if event exists
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            console.log('Marking notification as read:', notificationId);
            
            // Get CSRF token safely
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : null;
            
            if (!csrfToken) {
                console.error('CSRF token not found');
                alert('Security token missing. Please refresh the page.');
                return;
            }
            
            fetch(`/notifications/${notificationId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({}),
            })
            .then(response => {
                console.log('Response status:', response.status, response.statusText);
                
                // Handle different response types
                if (response.status === 401) {
                    window.location.reload(); // Session expired
                    return;
                }
                
                if (response.status === 404) {
                    console.error('Notification not found');
                    // Remove the notification from DOM
                    const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (notificationElement) {
                        notificationElement.remove();
                    }
                    return;
                }
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.json();
            })
            .then(data => {
                if (!data) return; // Already handled
                
                console.log('Response data:', data);
                
                if (data.success) {
                    // Update UI without full page reload
                    this.updateNotificationUI(notificationId);
                    
                    // Update unread count
                    this.updateUnreadCount();
                } else {
                    console.error('Error from server:', data.message);
                    alert('Error: ' + (data.message || 'Failed to mark as read'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Network error: ' + error.message);
            });
        },
        
        updateNotificationUI(notificationId) {
            // Remove notification from dropdown
            const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationElement) {
                notificationElement.classList.add('opacity-50');
                setTimeout(() => {
                    notificationElement.remove();
                    
                    // If no notifications left, show empty state
                    const notificationsList = document.querySelector('[data-notifications-list]');
                    if (notificationsList && notificationsList.children.length === 0) {
                        notificationsList.innerHTML = `
                            <div class="px-4 py-8 text-center">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <p class="text-sm text-gray-500">No new notifications</p>
                            </div>
                        `;
                    }
                }, 300);
            }
        },
        
        updateUnreadCount() {
            const unreadBadge = document.querySelector('[data-unread-count]');
            if (unreadBadge) {
                const currentCount = parseInt(unreadBadge.textContent) || 0;
                const newCount = Math.max(0, currentCount - 1);
                
                if (newCount > 0) {
                    unreadBadge.textContent = newCount > 99 ? '99+' : newCount;
                } else {
                    unreadBadge.remove();
                }
            }
        },
        
        markAllAsRead() {
            console.log('markAllAsRead called');
            
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : null;
            
            if (!csrfToken) {
                console.error('CSRF token not found');
                alert('Security token missing. Please refresh the page.');
                return;
            }
            
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({}),
            })
            .then(response => {
                console.log('Response status:', response.status);
                
                if (response.status === 401) {
                    window.location.reload();
                    return;
                }
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    // Clear all notifications from UI
                    const notificationsList = document.querySelector('[data-notifications-list]');
                    if (notificationsList) {
                        notificationsList.innerHTML = `
                            <div class="px-4 py-8 text-center">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <p class="text-sm text-gray-500">No new notifications</p>
                            </div>
                        `;
                    }
                    
                    // Remove unread badge
                    const unreadBadge = document.querySelector('[data-unread-count]');
                    if (unreadBadge) {
                        unreadBadge.remove();
                    }
                    
                    // Hide "Mark all as read" button
                    const markAllButton = document.querySelector('[onclick*="markAllAsRead"]');
                    if (markAllButton) {
                        markAllButton.remove();
                    }
                } else {
                    alert('Error: ' + (data.message || 'Failed to mark all as read'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Network error: ' + error.message);
            });
        }
    }
}

// Real-time notification polling for order status updates
document.addEventListener('DOMContentLoaded', function() {
    let lastNotificationId = null;
    
    // Poll for new notifications every 30 seconds
    setInterval(function() {
        fetch('/notifications/check-new', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.hasNewNotifications && data.latestNotification) {
                // Only show toast if this is a new notification (not already shown)
                if (lastNotificationId !== data.latestNotification.id) {
                    lastNotificationId = data.latestNotification.id;
                    // Show a subtle toast notification
                    showNotificationToast(data.latestNotification);
                }
            }
        })
        .catch(error => console.log('Notification check failed:', error));
    }, 30000); // Check every 30 seconds
});

function showNotificationToast(notification) {
    // Create a toast notification element
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-4 py-3 rounded-lg shadow-lg z-50 animate-pulse';
    toast.innerHTML = `
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <div>
                <p class="font-medium">${notification.title}</p>
                <p class="text-sm text-blue-100">${notification.message}</p>
            </div>
        </div>
    `;
    document.body.appendChild(toast);
    
    // Remove toast after 5 seconds
    setTimeout(() => {
        toast.remove();
    }, 5000);
}
</script>
