<!-- Orders Section Component -->
<div class="bg-white overflow-hidden shadow-xl rounded-lg sm:rounded-lg p-4 sm:p-6 mx-0 sm:mx-0">
    <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Orders</h2>
    
    <!-- Mobile: Horizontal scroll, Tablet: 2 columns, Desktop: 3 columns -->
    <div class="lg:max-h-96 lg:overflow-y-auto lg:pr-2">
        <!-- Mobile (sm and below): Horizontal Scroll -->
        <div class="block md:hidden overflow-x-auto pb-4 -mx-2 px-2">
            <div class="flex space-x-3 min-w-max">
                <!-- Add New Order Card -->
                <div class="flex-shrink-0 w-56 h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg text-gray-400 text-2xl font-bold cursor-pointer hover:border-gray-400 transition-colors"
                     onclick="openOrderModal()">
                    +
                </div>

                <!-- Order Cards -->
                @foreach($orders as $order)
                <div class="flex-shrink-0 w-56 bg-white border border-gray-200 rounded-lg p-3 sm:p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-500 text-xs sm:text-sm">#{{ $order['id'] }}</span>
                        <span class="{{ $order['status_class'] }} text-white text-xs px-2 py-1 rounded-full">
                            {{ $order['status'] }}
                        </span>
                    </div>
                    <div class="text-gray-800 font-medium text-sm sm:text-base">{{ $order['customer_name'] }}</div>
                    <div class="text-gray-500 text-xs sm:text-sm">{{ $order['service'] }} • {{ $order['weight'] }}kg</div>
                    <div class="text-gray-800 font-semibold mt-2 text-sm sm:text-base">{{ $order['price'] }}</div>
                    <a href="#" class="text-blue-600 text-xs sm:text-sm mt-2 inline-block hover:text-blue-800">Update</a>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Tablet (md) and Desktop (lg+): Grid Layout -->
        <div class="hidden md:grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            <!-- Add New Order Card -->
            <div class="flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg h-32 sm:h-36 text-gray-400 text-2xl sm:text-3xl font-bold cursor-pointer hover:border-gray-400 transition-colors"
                 onclick="openOrderModal()">
                +
            </div>

            <!-- Order Cards -->
            @foreach($orders as $order)
            <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-500 text-xs sm:text-sm">#{{ $order['id'] }}</span>
                    <span class="{{ $order['status_class'] }} text-white text-xs px-2 py-1 rounded-full">
                        {{ $order['status'] }}
                    </span>
                </div>
                <div class="text-gray-800 font-medium text-sm sm:text-base">{{ $order['customer_name'] }}</div>
                <div class="text-gray-500 text-xs sm:text-sm">{{ $order['service'] }} • {{ $order['weight'] }}kg</div>
                <div class="text-gray-800 font-semibold mt-2 text-sm sm:text-base">{{ $order['price'] }}</div>
                <a href="#" class="text-blue-600 text-xs sm:text-sm mt-2 inline-block hover:text-blue-800">Update</a>
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
/* Custom scrollbar styling */
.lg\:max-h-96::-webkit-scrollbar {
    width: 6px;
}

.lg\:max-h-96::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.lg\:max-h-96::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.lg\:max-h-96::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.overflow-x-auto::-webkit-scrollbar {
    height: 4px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

/* Better mobile horizontal scroll */
@media (max-width: 767px) {
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }
}
</style>

<script>
function openOrderModal() {
    // Add your modal opening logic here
    console.log('Open add order modal');
}
</script>