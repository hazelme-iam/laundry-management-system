@props(['count' => 0])

<div {{ $attributes->merge(['class' => 'relative']) }}>
    <button type="button" class="text-gray-500 hover:text-gray-700 focus:outline-none p-2">
        <!-- Bell Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        <!-- Unread Indicator -->
        @if($count > 0)
            <span class="absolute top-1.5 right-1.5 inline-block w-3 h-3 bg-red-500 rounded-full border-2 border-white">
                <span class="sr-only">{{ $count }} unread notifications</span>
            </span>
        @endif
    </button>
</div>
