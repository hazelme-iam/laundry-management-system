@props(['items' => []])

@if(count($items) > 0)
<nav class="w-full bg-transparent p-2" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L4 11.414V18a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-6.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                </svg>
                Dashboard
            </a>
        </li>
        @foreach($items as $label => $url)
            <li class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                </svg>
                @if($url)
                    <a href="{{ $url }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">{{ $label }}</a>
                @else
                    <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">{{ $label }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
