{{-- resources/views/components/capacity-backlog-section.blade.php --}}
<div class="p-4 sm:p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Capacity Overview Card -->
        <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base sm:text-lg font-semibold text-gray-800">Capacity Overview</h2>
                @if($capacityData['has_backlog'] ?? false)
                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Backlog</span>
                @else
                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Normal</span>
                @endif
            </div>
            
            <div class="space-y-4">
                <!-- Washers -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs sm:text-sm text-gray-600">Washers ({{ $capacityData['washers']['count'] ?? 0 }})</span>
                        <span class="text-xs font-semibold {{ ($capacityData['washers']['utilization_percent'] ?? 0) > 100 ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $capacityData['washers']['utilization_percent'] ?? 0 }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ min(100, $capacityData['washers']['utilization_percent'] ?? 0) }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>{{ $capacityData['washers']['current_load_kg'] ?? 0 }}kg</span>
                        <span>{{ $capacityData['washers']['daily_capacity_kg'] ?? 0 }}kg</span>
                    </div>
                </div>
                
                <!-- Dryers -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs sm:text-sm text-gray-600">Dryers ({{ $capacityData['dryers']['count'] ?? 0 }})</span>
                        <span class="text-xs font-semibold {{ ($capacityData['dryers']['utilization_percent'] ?? 0) > 100 ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $capacityData['dryers']['utilization_percent'] ?? 0 }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ min(100, $capacityData['dryers']['utilization_percent'] ?? 0) }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>{{ $capacityData['dryers']['current_load_kg'] ?? 0 }}kg</span>
                        <span>{{ $capacityData['dryers']['daily_capacity_kg'] ?? 0 }}kg</span>
                    </div>
                </div>
                
                <!-- Summary -->
                <div class="pt-3 border-t border-gray-200">
                    <div class="flex justify-between text-xs text-gray-600">
                        <span>Today's Load:</span>
                        <span class="font-semibold">{{ $capacityData['today_weight'] ?? 0 }}kg</span>
                    </div>
                    @if(($capacityData['backlog_weight'] ?? 0) > 0)
                    <div class="flex justify-between text-xs text-red-600 mt-1">
                        <span>Backlog:</span>
                        <span class="font-semibold">{{ $capacityData['backlog_weight'] ?? 0 }}kg</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Backlog Card -->
        <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base sm:text-lg font-semibold text-gray-800">Backlog Orders</h2>
                <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">Tomorrow</span>
            </div>
            
            <div class="space-y-3">
                @forelse ($backlogOrders ?? [] as $backlogOrder)
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-100">
                    <div class="flex items-center space-x-2 min-w-0">
                        <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-white text-xs font-bold">#{{ $backlogOrder['id'] ?? 'N/A' }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-800 truncate">
                                {{ $backlogOrder['customer_name'] ?? 'Unknown Customer' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $backlogOrder['weight'] ?? 0 }}kg • {{ $backlogOrder['service_type'] ?? 'Standard' }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right shrink-0 ml-2">
                        <div class="text-xs font-semibold text-orange-600">
                            {{ $backlogOrder['estimated_time'] ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-gray-500 text-sm">
                    No backlog orders for tomorrow
                </div>
                @endforelse
            </div>

            <!-- Backlog Summary -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center text-xs sm:text-sm">
                    <span class="text-gray-500">Total Backlog:</span>
                    <span class="font-semibold text-orange-600">
                        {{ isset($backlogOrders) ? count($backlogOrders) : 0 }} orders
                    </span>
                </div>
            </div>
        </div>

        <!-- Orders Today Card -->
        <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base sm:text-lg font-semibold text-gray-800">Orders Today</h2>
                <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Today</span>
            </div>
            
            <div class="space-y-3">
                @if(!empty($todayOrders) && count($todayOrders) > 0)
                    @foreach ($todayOrders as $todayOrder)
                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg border border-purple-100">
                        <div class="flex items-center space-x-2 min-w-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center shrink-0">
                                <span class="text-white text-xs font-bold">#{{ $todayOrder['id'] }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-800 truncate">
                                    {{ $todayOrder['customer_name'] }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $todayOrder['weight'] }}kg • {{ $todayOrder['service_type'] }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 ml-2">
                            <div class="text-xs font-semibold {{ $todayOrder['status_color'] ?? 'text-gray-600' }}">
                                {{ $todayOrder['status'] }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $todayOrder['created_at'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4 text-gray-500 text-sm">
                        No orders for today
                    </div>
                @endif
            </div>

            <!-- Today's Orders Summary -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center text-xs sm:text-sm">
                    <span class="text-gray-600">Total Today:</span>
                    <span class="font-semibold text-purple-600">
                        {{ $todayOrdersSummary['total'] ?? 0 }} orders
                    </span>
                </div>
                <div class="flex justify-between items-center text-xs text-gray-500 mt-1">
                    <span>Status:</span>
                    <div class="flex space-x-2">
                        <span class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                            {{ $todayOrdersSummary['completed'] ?? 0 }} done
                        </span>
                        <span class="flex items-center">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mr-1"></div>
                            {{ $todayOrdersSummary['processing'] ?? 0 }} processing
                        </span>
                        <span class="flex items-center">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></div>
                            {{ $todayOrdersSummary['pending'] ?? 0 }} pending
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>