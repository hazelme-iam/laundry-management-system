<aside class="w-64 bg-white border-r">
    <div class="h-16 flex items-center px-6 border-b">
        <span class="text-lg font-semibold">{{ config('app.name', 'Laundry System') }}</span>
    </div>

    <nav class="p-4 space-y-1">
        <a href="{{ route('dashboard') }}"
           class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'text-gray-900 font-medium' : 'text-gray-700' }}">
            {{ __('Overview') }}
        </a>
        <a href="{{ url('/customers') }}"
           class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->is('customers*') ? 'text-gray-900 font-medium' : 'text-gray-700' }}">
            {{ __('Customers') }}
        </a>
        <a href="{{ url('/orders') }}"
           class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->is('orders*') ? 'text-gray-900 font-medium' : 'text-gray-700' }}">
            {{ __('Laundry Management') }}
        </a>
        <a href="{{ route('machines.dashboard') }}"
           class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->is('machines*') ? 'text-gray-900 font-medium' : 'text-gray-700' }}">
            {{ __('Machines') }}
        </a>

        <a href="{{ url('/reports') }}"
           class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->is('reports*') ? 'text-gray-900 font-medium' : 'text-gray-700' }}">
            {{ __('Reports') }}
        </a>
    </nav>
</aside>