{{-- resources/views/components/table-row.blade.php --}}
@props([
    'hover' => true,
])

<tr {{ $attributes->class([
    'divide-x divide-gray-200',
    'hover:bg-gray-50' => $hover,
]) }}>
    {{ $slot }}
</tr>