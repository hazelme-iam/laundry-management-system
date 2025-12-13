@props(['items' => []])

@if(count($items) > 0)
<nav class="w-full bg-transparent p-2" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-1 md:space-x-3">
        @php
            $firstItem = true;
        @endphp
        
        @foreach($items as $label => $url)
            <li class="flex items-center">
                @if(!$firstItem)
                    <svg class="w-4 h-4 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                @endif
                
                @if($url)
                    <a href="{{ $url }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 {{ !$firstItem ? 'ml-1 md:ml-2' : '' }}">
                        {{ $label }}
                    </a>
                @else
                    <span class="text-sm font-medium text-gray-900 {{ !$firstItem ? 'ml-1 md:ml-2' : '' }}">
                        {{ $label }}
                    </span>
                @endif
                
                @php
                    $firstItem = false;
                @endphp
            </li>
        @endforeach
    </ol>
</nav>
@endif