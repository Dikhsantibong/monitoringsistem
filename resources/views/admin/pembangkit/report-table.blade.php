<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mesin</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beban</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DMN</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DMP</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
        </tr>
    </thead>
    <tbody id="reportTableBody">
        @forelse($logs as $log)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">{{ $log->machine->powerPlant->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $log->machine->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $log->status === 'Operasi' ? 'bg-green-100 text-green-800' : 
                           ($log->status === 'Gangguan' ? 'bg-red-100 text-red-800' : 
                           'bg-yellow-100 text-yellow-800') }}">
                        {{ $log->status }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $log->load_value }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $log->dmn }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $log->dmp }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $log->keterangan }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                    Tidak ada data untuk ditampilkan
                </td>
            </tr>
        @endforelse
    </tbody>
</table>