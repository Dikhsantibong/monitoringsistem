<div class="bg-white rounded-lg shadow-md p-6 mb-6 overflow-x-auto">
    <h3 class="text-lg font-semibold text-gray-700 mb-1">{{ $title }}</h3>
    <p class="text-sm text-gray-500 mb-4">{{ count($rows ?? []) }} baris</p>

    @if(!empty($error))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
            <p class="font-bold">Error</p>
            <p>{{ $error }}</p>
        </div>
    @elseif(count($rows ?? []) > 0)
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    @foreach(array_keys($rows[0]) as $col)
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($rows as $row)
                    <tr>
                        @foreach($row as $val)
                            <td class="px-4 py-2 whitespace-pre-wrap text-gray-900" style="max-width: 280px;">
                                {{ is_array($val) || is_object($val) ? json_encode($val) : $val }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-gray-500 italic">Tidak ada data.</p>
    @endif
</div>
