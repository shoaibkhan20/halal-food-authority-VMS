@props(['headers' => [], 'rows' => []])

<div class="overflow-x-auto">
    <table class="table min-w-full text-center text-sm">
        <thead class="bg-gray-200">
            <tr>
                @foreach ($headers as $header)
                    <th class="px-4 py-2">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr class="border-t  border-gray-300 hover:bg-gray-50 transition">
                    @foreach ($row as $cell)
                        <td class="px-4 py-2">
                            {!! $cell !!}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-4 py-4 text-center text-gray-500">
                        No data found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>