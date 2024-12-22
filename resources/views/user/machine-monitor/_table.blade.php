<table class="min-w-full divide-y divide-gray-200 border border-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">No</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Unit</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Mesin</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Beban</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">DMN</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">DMP</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Kronologi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Deskripsi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Action Plan</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Progres</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Target Selesai</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($machineStatusLogs as $index => $log)
            <tr class="hover:bg-gray-50 border border-gray-200">
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $index + 1 }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->machine->powerPlant->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->machine->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $log->status === 'Operasi' ? 'bg-green-100 text-green-800' : 
                           ($log->status === 'Gangguan' ? 'bg-red-100 text-red-800' : 
                           'bg-yellow-100 text-yellow-800') }}">
                        {{ $log->status }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->load_value }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->dmn }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->dmp }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->kronologi }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->deskripsi }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->action_plan }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->progres }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->target_selesai ? $log->target_selesai->format('d/m/Y') : '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="12" class="px-6 py-4 text-center text-gray-500 border border-gray-200">
                    Tidak ada data untuk ditampilkan
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if($machineStatusLogs->hasPages())
<div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4">
    <div class="flex-1 flex justify-between sm:hidden">
        {{ $machineStatusLogs->links() }}
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-700">
                Menampilkan
                <span class="font-medium">{{ $machineStatusLogs->firstItem() ?? 0 }}</span>
                sampai
                <span class="font-medium">{{ $machineStatusLogs->lastItem() ?? 0 }}</span>
                dari
                <span class="font-medium">{{ $machineStatusLogs->total() }}</span>
                hasil
            </p>
        </div>
        <div>
            {{ $machineStatusLogs->links() }}
        </div>
    </div>
</div>
@endif 