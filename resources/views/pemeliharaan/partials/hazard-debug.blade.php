@if(!empty($hazardDebug))
    <div class="mb-8">
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6 text-sm text-amber-900">
            <p><strong>WONUM:</strong> {{ $hazardDebug['requested_wonum'] }}</p>
            <p><strong>WO terkait:</strong> {{ implode(', ', $hazardDebug['all_wonums']) }}</p>
            @if($hazardDebug['child_wo_error'])
                <p class="text-red-700 mt-2"><strong>Child WO error:</strong> {{ $hazardDebug['child_wo_error'] }}</p>
            @endif
        </div>

        @if(count($hazardDebug['summary']) > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 overflow-x-auto">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Ringkasan Hazard &amp; Precaution</h3>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">WONUM</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">HAZARDID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi Hazard</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Precautions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($hazardDebug['summary'] as $item)
                            <tr>
                                <td class="px-4 py-3">{{ $item['wonum'] }}</td>
                                <td class="px-4 py-3">{{ $item['hazardid'] }}</td>
                                <td class="px-4 py-3">{{ $item['description'] }}</td>
                                <td class="px-4 py-3">
                                    @if(count($item['precautions']) > 0)
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach($item['precautions'] as $prec)
                                                <li>
                                                    <span class="text-gray-500 text-xs">[{{ $prec['source'] }}]</span>
                                                    {{ $prec['precautionid'] }} — {{ $prec['description'] }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-400 italic">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 p-4 mb-6">
                Tidak ada baris di <strong>WOHAZARD</strong> untuk WONUM di atas (SITEID = KD).
            </div>
        @endif

        @foreach(['WOHAZARD', 'HAZARD', 'WOHAZARDPREC', 'HAZARDPREC', 'PRECAUTION'] as $tableName)
            @php
                $meta = $hazardDebug['tables'][$tableName];
                $rows = $meta['rows'] ?? [];
            @endphp
            @include('pemeliharaan.partials.oracle-table-block', [
                'title' => $tableName,
                'rows' => $rows,
                'error' => $meta['error'] ?? null,
            ])
        @endforeach
    </div>
@endif
