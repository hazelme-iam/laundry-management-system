<x-sidebar-app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                    <p class="text-gray-600">View all your notifications</p>
                </div>
                <button onclick="markAllAsRead()" class="text-sm text-blue-600 hover:text-blue-800">
                    Mark all as read
                </button>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse($notifications as $notification)
                        <div class="p-4 sm:p-6 {{ !$notification->is_read ? 'bg-blue-50' : '' }}">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                    <p class="text-xs text-gray-500 mt-2">{{ $notification->formatted_created_at }}</p>

                                    @if(isset($notification->data['url']))
                                        <a href="{{ $notification->data['url'] }}" onclick="markAsRead({{ $notification->id }})" class="inline-flex items-center mt-3 text-sm text-blue-600 hover:text-blue-800">
                                            View details
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </div>

                                <div class="flex-shrink-0">
                                    @if(!$notification->is_read)
                                        <button onclick="markAsRead({{ $notification->id }})" class="text-sm text-blue-600 hover:text-blue-800">
                                            Mark as read
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            No notifications yet.
                        </div>
                    @endforelse
                </div>

                @if($notifications->hasPages())
                    <div class="px-4 sm:px-6 py-4 border-t">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-sidebar-app>
