{{-- resources/views/components/status-badge.blade.php --}}
@props([
    'status' => 'pending',
    'showText' => true,
])

@php
    $statusConfig = [
        'pending' => ['bg-yellow-100', 'text-yellow-800', 'border-yellow-300', 'Pending'],
        'in_progress' => ['bg-blue-100', 'text-blue-800', 'border-blue-300', 'In Progress'],
        'ready' => ['bg-indigo-100', 'text-indigo-800', 'border-indigo-300', 'Ready'],
        'completed' => ['bg-green-100', 'text-green-800', 'border-green-300', 'Completed'],
        'cancelled' => ['bg-red-100', 'text-red-800', 'border-red-300', 'Cancelled'],
    ];
    
    $config = $statusConfig[$status] ?? $statusConfig['pending'];
@endphp

<span {{ $attributes->class([
    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full border',
    $config[0], $config[1], $config[2]
]) }}>
    {{ $showText ? $config[3] : '' }}
</span>