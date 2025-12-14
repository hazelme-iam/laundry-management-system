{{-- resources/views/components/capacity-backlog-section.blade.php --}}
<div class="p-3 xs:p-4 sm:p-5 md:p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 xs:gap-4 sm:gap-5 md:gap-6">
        <!-- Capacity Overview Card -->
        <div class="bg-white rounded-lg xs:rounded-xl sm:rounded-2xl p-3 xs:p-4 sm:p-5 md:p-6 shadow-md xs:shadow-lg border border-gray-100 md:col-span-2 xl:col-span-1">
            <div class="flex items-center justify-between mb-3 xs:mb-4">
                <h2 class="text-sm xs:text-base sm:text-lg font-semibold text-gray-800 truncate">Capacity Overview</h2>
                @if($capacityData['has_backlog'] ?? false)
                    <span class="bg-red-100 text-red-800 text-[10px] xs:text-xs px-2 py-1 rounded-full shrink-0 ml-2">Backlog</span>
                @else
                    <span class="bg-green-100 text-green-800 text-[10px] xs:text-xs px-2 py-1 rounded-full shrink-0 ml-2">Normal</span>
                @endif
            </div>
            
            <div class="space-y-3 xs:space-y-4">
                <!-- Washers -->
                <div>
                    <div class="flex flex-wrap xs:flex-nowrap justify-between items-center mb-1 xs:mb-2 gap-1">
                        <span class="text-[11px] xs:text-xs sm:text-sm text-gray-600 truncate">
                            Washers ({{ $capacityData['washers']['count'] ?? 0 }})
                        </span>
                        <span class="text-[11px] xs:text-xs font-semibold {{ ($capacityData['washers']['utilization_percent'] ?? 0) > 100 ? 'text-red-600' : 'text-gray-800' }} shrink-0">
                            {{ $capacityData['washers']['utilization_percent'] ?? 0 }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 xs:h-2">
                        <div class="bg-blue-500 h-1.5 xs:h-2 rounded-full transition-all duration-300" 
                             style="width: {{ min(100, $capacityData['washers']['utilization_percent'] ?? 0) }}%"></div>
                    </div>
                    <div class="flex justify-between text-[10px] xs:text-xs text-gray-500 mt-1">
                        <span class="truncate">{{ $capacityData['washers']['current_load_kg'] ?? 0 }}kg</span>
                        <span class="truncate">{{ $capacityData['washers']['daily_capacity_kg'] ?? 0 }}kg</span>
                    </div>
                </div>
                
                <!-- Dryers -->
                <div>
                    <div class="flex flex-wrap xs:flex-nowrap justify-between items-center mb-1 xs:mb-2 gap-1">
                        <span class="text-[11px] xs:text-xs sm:text-sm text-gray-600 truncate">
                            Dryers ({{ $capacityData['dryers']['count'] ?? 0 }})
                        </span>
                        <span class="text-[11px] xs:text-xs font-semibold {{ ($capacityData['dryers']['utilization_percent'] ?? 0) > 100 ? 'text-red-600' : 'text-gray-800' }} shrink-0">
                            {{ $capacityData['dryers']['utilization_percent'] ?? 0 }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 xs:h-2">
                        <div class="bg-green-500 h-1.5 xs:h-2 rounded-full transition-all duration-300" 
                             style="width: {{ min(100, $capacityData['dryers']['utilization_percent'] ?? 0) }}%"></div>
                    </div>
                    <div class="flex justify-between text-[10px] xs:text-xs text-gray-500 mt-1">
                        <span class="truncate">{{ $capacityData['dryers']['current_load_kg'] ?? 0 }}kg</span>
                        <span class="truncate">{{ $capacityData['dryers']['daily_capacity_kg'] ?? 0 }}kg</span>
                    </div>
                </div>
                
                <!-- Summary -->
                <div class="pt-2 xs:pt-3 border-t border-gray-200 space-y-1">
                    <div class="flex justify-between text-[11px] xs:text-xs text-gray-600">
                        <span class="truncate">Today's Load:</span>
                        <span class="font-semibold truncate">{{ $capacityData['today_weight'] ?? 0 }}kg</span>
                    </div>
                    <div class="flex justify-between text-[11px] xs:text-xs text-blue-600">
                        <span class="truncate">Confirmed Kilos:</span>
                        <span class="font-semibold truncate">{{ $capacityData['confirmed_weight'] ?? 0 }}kg</span>
                    </div>
                    @if(($capacityData['backlog_weight'] ?? 0) > 0)
                    <div class="flex justify-between text-[11px] xs:text-xs text-red-600">
                        <span class="truncate">Backlog:</span>
                        <span class="font-semibold truncate">{{ $capacityData['backlog_weight'] ?? 0 }}kg</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Backlog Card -->
        <div class="bg-white rounded-lg xs:rounded-xl sm:rounded-2xl p-3 xs:p-4 sm:p-5 md:p-6 shadow-md xs:shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-3 xs:mb-4">
                <h2 class="text-sm xs:text-base sm:text-lg font-semibold text-gray-800 truncate">Backlog Orders</h2>
                <span class="bg-orange-100 text-orange-800 text-[10px] xs:text-xs px-2 py-1 rounded-full shrink-0 ml-2">Will Wash Tomorrow</span>
            </div>
            
            <div class="space-y-2 xs:space-y-3 max-h-[200px] xs:max-h-[240px] sm:max-h-none overflow-y-auto pr-1">
                @forelse ($backlogOrders ?? [] as $backlogOrder)
                <div class="flex items-center justify-between p-2 xs:p-3 bg-orange-50 rounded-lg border border-orange-100">
                    <div class="flex items-center min-w-0 flex-1">
                        <div class="w-6 h-6 xs:w-7 xs:h-7 sm:w-8 sm:h-8 bg-orange-500 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-white text-[10px] xs:text-xs font-bold">#{{ $backlogOrder['id'] ?? 'N/A' }}</span>
                        </div>
                        <div class="min-w-0 flex-1 ml-2 xs:ml-3">
                            <div class="text-xs xs:text-sm font-medium text-gray-800 truncate">
                                {{ $backlogOrder['customer_name'] ?? 'Unknown Customer' }}
                            </div>
                            <div class="text-[10px] xs:text-xs text-gray-500 truncate">
                                {{ $backlogOrder['weight'] ?? 0 }}kg • {{ $backlogOrder['service_type'] ?? 'Standard' }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right shrink-0 ml-2">
                        <div class="text-[10px] xs:text-xs font-semibold text-orange-600 whitespace-nowrap">
                            {{ $backlogOrder['estimated_time'] ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-gray-500 text-xs xs:text-sm">
                    No backlog orders for tomorrow
                </div>
                @endforelse
            </div>

            <!-- Backlog Summary -->
            <div class="mt-3 xs:mt-4 pt-3 xs:pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center text-xs xs:text-sm mb-3">
                    <span class="text-gray-500 truncate">Total Backlog:</span>
                    <span class="font-semibold text-orange-600 whitespace-nowrap">
                        {{ isset($backlogOrders) ? count($backlogOrders) : 0 }} orders
                    </span>
                </div>
                @if(isset($backlogOrders) && count($backlogOrders) > 0)
                <a href="{{ route('admin.orders.index', ['backlog' => 'backlog']) }}" 
                   class="w-full inline-flex items-center justify-center px-3 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs xs:text-sm font-medium rounded-lg transition-colors">
                    View All Backlog Orders
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.5 1.5H19v8.5m0 0l-8.56-8.56M19 10v8.5a1.5 1.5 0 01-1.5 1.5H2.5a1.5 1.5 0 01-1.5-1.5V2.5A1.5 1.5 0 012.5 1h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                </a>
                @endif
            </div>
        </div>

        <!-- Orders Today Card -->
        <div class="bg-white rounded-lg xs:rounded-xl sm:rounded-2xl p-3 xs:p-4 sm:p-5 md:p-6 shadow-md xs:shadow-lg border border-gray-100 md:col-span-2 xl:col-span-1">
            <div class="flex items-center justify-between mb-3 xs:mb-4">
                <h2 class="text-sm xs:text-base sm:text-lg font-semibold text-gray-800 truncate">Orders Today</h2>
                <span class="bg-purple-100 text-purple-800 text-[10px] xs:text-xs px-2 py-1 rounded-full shrink-0 ml-2">Today</span>
            </div>
            
            <div class="space-y-2 xs:space-y-3 max-h-[200px] xs:max-h-[240px] sm:max-h-none overflow-y-auto pr-1">
                @if(!empty($todayOrders) && count($todayOrders) > 0)
                    @foreach ($todayOrders as $todayOrder)
                    <div class="flex items-center justify-between p-2 xs:p-3 bg-purple-50 rounded-lg border border-purple-100">
                        <div class="flex items-center min-w-0 flex-1">
                            <div class="w-6 h-6 xs:w-7 xs:h-7 sm:w-8 sm:h-8 bg-purple-500 rounded-full flex items-center justify-center shrink-0">
                                <span class="text-white text-[10px] xs:text-xs font-bold">#{{ $todayOrder['id'] }}</span>
                            </div>
                            <div class="min-w-0 flex-1 ml-2 xs:ml-3">
                                <div class="text-xs xs:text-sm font-medium text-gray-800 truncate">
                                    {{ $todayOrder['customer_name'] }}
                                </div>
                                <div class="text-[10px] xs:text-xs text-gray-500 truncate">
                                    {{ $todayOrder['weight'] }}kg • {{ $todayOrder['service_type'] }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 ml-2">
                            <div class="text-[10px] xs:text-xs font-semibold {{ $todayOrder['status_color'] ?? 'text-gray-600' }} whitespace-nowrap">
                                {{ $todayOrder['status'] }}
                            </div>
                            <div class="text-[10px] xs:text-xs text-gray-500 whitespace-nowrap">
                                {{ $todayOrder['created_at'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4 text-gray-500 text-xs xs:text-sm">
                        No orders for today
                    </div>
                @endif
            </div>

            <!-- Today's Orders Summary -->
            <div class="mt-3 xs:mt-4 pt-3 xs:pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center text-xs xs:text-sm mb-1">
                    <span class="text-gray-600 truncate">Total Today:</span>
                    <span class="font-semibold text-purple-600 whitespace-nowrap">
                        {{ $todayOrdersSummary['total'] ?? 0 }} orders
                    </span>
                </div>
                <div class="flex flex-wrap xs:flex-nowrap justify-between items-center text-[10px] xs:text-xs text-gray-500 gap-1 xs:gap-2">
                    <span class="truncate">Status:</span>
                    <div class="flex space-x-1 xs:space-x-2">
                        <span class="flex items-center whitespace-nowrap">
                            <div class="w-1.5 h-1.5 xs:w-2 xs:h-2 bg-green-500 rounded-full mr-1"></div>
                            {{ $todayOrdersSummary['completed'] ?? 0 }} done
                        </span>
                        <span class="flex items-center whitespace-nowrap">
                            <div class="w-1.5 h-1.5 xs:w-2 xs:h-2 bg-purple-500 rounded-full mr-1"></div>
                            {{ $todayOrdersSummary['processing'] ?? 0 }} processing
                        </span>
                        <span class="flex items-center whitespace-nowrap">
                            <div class="w-1.5 h-1.5 xs:w-2 xs:h-2 bg-yellow-500 rounded-full mr-1"></div>
                            {{ $todayOrdersSummary['pending'] ?? 0 }} pending
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>