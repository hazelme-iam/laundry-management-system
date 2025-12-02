{{-- resources/views/admin/customers/create.blade.php --}}
<x-sidebar-app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumb Navigation -->
            <x-breadcrumbs :items="[
                'Customers' => route('admin.customers.index'),
                'Create Customer' => null
            ]" />
            
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Create New Customer</h1>

                    <form action="{{ route('admin.customers.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name </label>
                                <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                       id="email" name="email" value="{{ old('email') }}">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone (11 digits only) -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                       id="phone" name="phone" value="{{ old('phone') }}" 
                                       pattern="[0-9]{11}" 
                                       maxlength="11"
                                       placeholder="09XXXXXXXXX"
                                       required>
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Customer Type -->
                            <div>
                                <label for="customer_type" class="block text-sm font-medium text-gray-700">Customer Type </label>
                                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                        id="customer_type" name="customer_type" required>
                                    <option value="">Select Type</option>
                                    <option value="walk-in" {{ old('customer_type') == 'walk-in' ? 'selected' : '' }}>Walk-in</option>
                                    <option value="regular" {{ old('customer_type') == 'regular' ? 'selected' : '' }}>Regular</option>
                                    <option value="vip" {{ old('customer_type') == 'vip' ? 'selected' : '' }}>VIP</option>
                                </select>
                                @error('customer_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Address Details -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Barangay Dropdown -->
                            <div>
                                <label for="barangay" class="block text-sm font-medium text-gray-700">Barangay</label>
                                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                        id="barangay" name="barangay">
                                    <option value="">Select Barangay</option>
                                    <option value="Poblacion" {{ old('barangay') == 'Poblacion' ? 'selected' : '' }}>Poblacion</option>
                                    <option value="Baluarte" {{ old('barangay') == 'Baluarte' ? 'selected' : '' }}>Baluarte</option>
                                    <option value="Binuangan" {{ old('barangay') == 'Binuangan' ? 'selected' : '' }}>Binuangan</option>
                                    <option value="Gracia" {{ old('barangay') == 'Gracia' ? 'selected' : '' }}>Gracia</option>
                                    <option value="Mohon" {{ old('barangay') == 'Mohon' ? 'selected' : '' }}>Mohon</option>
                                    <option value="Rosario" {{ old('barangay') == 'Rosario' ? 'selected' : '' }}>Rosario</option>
                                    <option value="Santa Ana" {{ old('barangay') == 'Santa Ana' ? 'selected' : '' }}>Santa Ana</option>
                                    <option value="Santo Niño" {{ old('barangay') == 'Santo Niño' ? 'selected' : '' }}>Santo Niño</option>
                                    <option value="Sugbongcogon" {{ old('barangay') == 'Sugbongcogon' ? 'selected' : '' }}>Sugbongcogon</option>
                                    <!-- Add more barangays in Tagoloan as needed -->
                                </select>
                                @error('barangay')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Purok Dropdown -->
                            <div>
                                <label for="purok" class="block text-sm font-medium text-gray-700">Purok/Zone</label>
                                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                        id="purok" name="purok">
                                    <option value="">Select Purok</option>
                                    <option value="Purok 1" {{ old('purok') == 'Purok 1' ? 'selected' : '' }}>Purok 1</option>
                                    <option value="Purok 2" {{ old('purok') == 'Purok 2' ? 'selected' : '' }}>Purok 2</option>
                                    <option value="Purok 3" {{ old('purok') == 'Purok 3' ? 'selected' : '' }}>Purok 3</option>
                                    <option value="Purok 4" {{ old('purok') == 'Purok 4' ? 'selected' : '' }}>Purok 4</option>
                                    <option value="Purok 5" {{ old('purok') == 'Purok 5' ? 'selected' : '' }}>Purok 5</option>
                                    <option value="Purok 6" {{ old('purok') == 'Purok 6' ? 'selected' : '' }}>Purok 6</option>
                                    <option value="Purok 7" {{ old('purok') == 'Purok 7' ? 'selected' : '' }}>Purok 7</option>
                                    <option value="Purok 8" {{ old('purok') == 'Purok 8' ? 'selected' : '' }}>Purok 8</option>
                                    <!-- Add more puroks as needed -->
                                </select>
                                @error('purok')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Street Dropdown -->
                            <div>
                                <label for="street" class="block text-sm font-medium text-gray-700">Street</label>
                                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                        id="street" name="street">
                                    <option value="">Select Street</option>
                                    <option value="Rizal Street" {{ old('street') == 'Rizal Street' ? 'selected' : '' }}>Rizal Street</option>
                                    <option value="Mabini Street" {{ old('street') == 'Mabini Street' ? 'selected' : '' }}>Mabini Street</option>
                                    <option value="Bonifacio Street" {{ old('street') == 'Bonifacio Street' ? 'selected' : '' }}>Bonifacio Street</option>
                                    <option value="Luna Street" {{ old('street') == 'Luna Street' ? 'selected' : '' }}>Luna Street</option>
                                    <option value="Burgos Street" {{ old('street') == 'Burgos Street' ? 'selected' : '' }}>Burgos Street</option>
                                    <option value="Del Pilar Street" {{ old('street') == 'Del Pilar Street' ? 'selected' : '' }}>Del Pilar Street</option>
                                    <option value="Aguinaldo Street" {{ old('street') == 'Aguinaldo Street' ? 'selected' : '' }}>Aguinaldo Street</option>
                                    <!-- Add more streets in Tagoloan as needed -->
                                </select>
                                @error('street')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Full Address (for additional details) -->
                        <div class="mt-6">
                            <label for="address" class="block text-sm font-medium text-gray-700">Complete Address Details</label>
                            <textarea class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                      id="address" name="address" rows="3" 
                                      placeholder="e.g., House #, Landmarks, Additional directions">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Additional customer information">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="mt-6 flex space-x-3">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                Create Customer
                            </button>
                            <a href="{{ route('admin.customers.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-sidebar-app>