<div wire:poll-5000="loadMachines">
    <!-- Auto-refresh indicator -->
    <div class="px-4 sm:px-0 flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-sm text-gray-600">Auto-refreshing every 5 seconds</span>
        </div>
        <button wire:click="loadMachines" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">
            Refresh Now
        </button>
    </div>

    <!-- Quick Availability Summary -->
    <div class="bg-white shadow-lg rounded-lg mx-4 sm:mx-0 mb-6">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Machine Availability</h3>
            <p class="text-sm text-gray-600 mt-1">Current status of all machines</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Washers -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Washers</h4>
                    <div class="space-y-2">
                        @foreach($washers as $washer)
                            <div class="flex items-center justify-between p-2 rounded
                                @if($washer->status === 'idle') bg-green-50 border border-green-200
                                @else bg-blue-50 border border-blue-200
                                @endif">
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium text-gray-900">{{ $washer->name }}</span>
                                    @if($washer->status === 'idle')
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Available</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">In Use</span>
                                    @endif
                                </div>
                                @if($washer->status === 'in_use' && $washer->currentOrder)
                                    <div class="text-sm text-gray-600">
                                        Order #{{ str_pad($washer->currentOrder->id, 5, '0', STR_PAD_LEFT) }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Dryers -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Dryers</h4>
                    <div class="space-y-2">
                        @foreach($dryers as $dryer)
                            <div class="flex items-center justify-between p-2 rounded
                                @if($dryer->status === 'idle') bg-green-50 border border-green-200
                                @else bg-green-50 border border-green-200
                                @endif">
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium text-gray-900">{{ $dryer->name }}</span>
                                    @if($dryer->status === 'idle')
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Available</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">In Use</span>
                                    @endif
                                </div>
                                @if($dryer->status === 'in_use' && $dryer->currentOrder)
                                    <div class="text-sm text-gray-600">
                                        Order #{{ str_pad($dryer->currentOrder->id, 5, '0', STR_PAD_LEFT) }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Washers Section -->
    <div class="bg-white shadow-lg rounded-lg mx-4 sm:mx-0 mb-6">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Washing Machines</h3>
            <p class="text-sm text-gray-600 mt-1">Monitor and manage washing machine status</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($washers as $washer)
                    <div class="border rounded-lg p-4
                        @if($washer->status === 'idle') bg-gray-50 border-gray-200
                        @elseif($washer->status === 'in_use') bg-blue-50 border-blue-200
                        @else bg-red-50 border-red-200
                        @endif">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900">{{ $washer->name }}</h4>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($washer->status === 'idle') bg-gray-100 text-gray-800
                                @elseif($washer->status === 'in_use') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($washer->status) }}
                            </span>
                        </div>
                        
                        @if($washer->status === 'in_use' && $washer->currentOrder)
                            <div class="space-y-2">
                                <div class="text-sm">
                                    <span class="text-gray-600">Order:</span>
                                    <span class="font-medium text-gray-900 ml-1">#{{ str_pad($washer->currentOrder->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-gray-600">Customer:</span>
                                    <span class="font-medium text-gray-900 ml-1">{{ $washer->currentOrder->customer->name ?? 'Unknown' }}</span>
                                </div>
                                @if($washer->getTimeRemainingFormatted())
                                    <div class="text-sm font-medium text-blue-600">
                                        {{ $washer->getTimeRemainingFormatted() }}
                                    </div>
                                @endif
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-1000" 
                                         style="width: {{ $washer->getTimeRemaining() > 0 ? (($washer->washing_end->diffInSeconds(now()) / ($washer->washing_end->diffInSeconds($washer->washing_start))) * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        @else
                            <div class="text-sm text-gray-500">
                                @if($washer->status === 'idle')
                                    Ready for use
                                @else
                                    Under maintenance
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Dryers Section -->
    <div class="bg-white shadow-lg rounded-lg mx-4 sm:mx-0 mb-6">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Drying Machines</h3>
            <p class="text-sm text-gray-600 mt-1">Monitor and manage drying machine status</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($dryers as $dryer)
                    <div class="border rounded-lg p-4
                        @if($dryer->status === 'idle') bg-gray-50 border-gray-200
                        @elseif($dryer->status === 'in_use') bg-green-50 border-green-200
                        @else bg-red-50 border-red-200
                        @endif">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900">{{ $dryer->name }}</h4>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($dryer->status === 'idle') bg-gray-100 text-gray-800
                                @elseif($dryer->status === 'in_use') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($dryer->status) }}
                            </span>
                        </div>
                        
                        @if($dryer->status === 'in_use' && $dryer->currentOrder)
                            <div class="space-y-2">
                                <div class="text-sm">
                                    <span class="text-gray-600">Order:</span>
                                    <span class="font-medium text-gray-900 ml-1">#{{ str_pad($dryer->currentOrder->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-gray-600">Customer:</span>
                                    <span class="font-medium text-gray-900 ml-1">{{ $dryer->currentOrder->customer->name ?? 'Unknown' }}</span>
                                </div>
                                @if($dryer->getTimeRemainingFormatted())
                                    <div class="text-sm font-medium text-green-600">
                                        {{ $dryer->getTimeRemainingFormatted() }}
                                    </div>
                                @endif
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full transition-all duration-1000" 
                                         style="width: {{ $dryer->getTimeRemaining() > 0 ? (($dryer->drying_end->diffInSeconds(now()) / ($dryer->drying_end->diffInSeconds($dryer->drying_start))) * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        @else
                            <div class="text-sm text-gray-500">
                                @if($dryer->status === 'idle')
                                    Ready for use
                                @else
                                    Under maintenance
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="bg-white shadow-lg rounded-lg mx-4 sm:mx-0">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Summary</h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['washers_in_use'] }}</div>
                    <div class="text-sm text-gray-600">Washers in Use</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ $stats['washers_available'] }}</div>
                    <div class="text-sm text-gray-600">Washers Available</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['dryers_in_use'] }}</div>
                    <div class="text-sm text-gray-600">Dryers in Use</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ $stats['dryers_available'] }}</div>
                    <div class="text-sm text-gray-600">Dryers Available</div>
                </div>
            </div>
        </div>
    </div>
</div>
