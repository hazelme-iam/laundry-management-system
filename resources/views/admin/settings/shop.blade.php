@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Shop Settings</h2>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <ul class="list-disc list-inside text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.settings.shop.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Shop Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Shop Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="shop_name" class="block text-sm font-medium text-gray-700">Shop Name</label>
                                <input type="text" name="shop_name" id="shop_name" value="{{ old('shop_name', $settings->shop_name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="owner_name" class="block text-sm font-medium text-gray-700">Owner Name</label>
                                <input type="text" name="owner_name" id="owner_name" value="{{ old('owner_name', $settings->owner_name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                <textarea name="address" id="address" rows="2" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address', $settings->address) }}</textarea>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone', $settings->phone) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $settings->email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="base_price_per_kg" class="block text-sm font-medium text-gray-700">Base Price per KG (₱)</label>
                                <input type="number" name="base_price_per_kg" id="base_price_per_kg" step="0.01" value="{{ old('base_price_per_kg', $settings->base_price_per_kg) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="detergent_price" class="block text-sm font-medium text-gray-700">Detergent Add-on (₱)</label>
                                <input type="number" name="detergent_price" id="detergent_price" step="0.01" value="{{ old('detergent_price', $settings->add_on_prices['detergent'] ?? 16) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="fabric_conditioner_price" class="block text-sm font-medium text-gray-700">Fabric Conditioner Add-on (₱)</label>
                                <input type="number" name="fabric_conditioner_price" id="fabric_conditioner_price" step="0.01" value="{{ old('fabric_conditioner_price', $settings->add_on_prices['fabric_conditioner'] ?? 14) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Processing Times -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Processing Times</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="washing_time_minutes" class="block text-sm font-medium text-gray-700">Washing Time (minutes)</label>
                                <input type="number" name="washing_time_minutes" id="washing_time_minutes" value="{{ old('washing_time_minutes', $settings->washing_time_minutes) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="drying_time_per_kg" class="block text-sm font-medium text-gray-700">Drying Time per KG (minutes)</label>
                                <input type="number" name="drying_time_per_kg" id="drying_time_per_kg" value="{{ old('drying_time_per_kg', $settings->drying_time_per_kg) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="min_drying_time" class="block text-sm font-medium text-gray-700">Minimum Drying Time (minutes)</label>
                                <input type="number" name="min_drying_time" id="min_drying_time" value="{{ old('min_drying_time', $settings->min_drying_time) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="max_drying_time" class="block text-sm font-medium text-gray-700">Maximum Drying Time (minutes)</label>
                                <input type="number" name="max_drying_time" id="max_drying_time" value="{{ old('max_drying_time', $settings->max_drying_time) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="business_hours" class="block text-sm font-medium text-gray-700">Business Hours</label>
                                <textarea name="business_hours" id="business_hours" rows="2" placeholder="e.g., Mon-Fri: 8AM-6PM, Sat: 8AM-5PM, Sun: Closed" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('business_hours', $settings->business_hours) }}</textarea>
                            </div>

                            <div>
                                <label for="about" class="block text-sm font-medium text-gray-700">About the Shop</label>
                                <textarea name="about" id="about" rows="3" placeholder="Brief description about your laundry shop" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('about', $settings->about) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 border-t pt-6">
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
