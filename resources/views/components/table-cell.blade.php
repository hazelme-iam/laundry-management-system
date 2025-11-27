{{-- resources/views/components/table-cell.blade.php --}}
@props([
    'padding' => true,
    'nowrap' => true,
])

<td {{ $attributes->class([
    'px-6 py-4' => $padding,
    'whitespace-nowrap' => $nowrap,
]) }}>
    {{ $slot }}
</td>