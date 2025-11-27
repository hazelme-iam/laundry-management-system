{{-- resources/views/components/table.blade.php --}}
@props([
    'headers' => [],
    'emptyMessage' => 'No records found.',
    'emptyAction' => null,
])

<div class="bg-white rounded-lg shadow overflow-hidden">
    @if(isset($filters))
        <div class="p-4 border-b">
            {{ $filters }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            @if(!empty($headers))
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($headers as $header)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endif

            <tbody class="bg-white divide-y divide-gray-200">
                @if(isset($rows) && $rows->count() > 0)
                    {{ $rows }}
                @else
                    <tr>
                        <td colspan="{{ count($headers) }}" class="px-6 py-4 text-center text-gray-500">
                            {{ $emptyMessage }}
                            @if($emptyAction)
                                {!! $emptyAction !!}
                            @endif
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if(isset($pagination))
        <div class="px-6 py-4 border-t">
            {{ $pagination }}
        </div>
    @endif
</div>