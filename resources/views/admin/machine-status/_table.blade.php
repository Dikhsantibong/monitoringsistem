@php
use Illuminate\Support\Facades\Storage;
@endphp
@if($powerPlants->isEmpty())
    <div class="text-center py-4 text-gray-500">
        Tidak ada data untuk ditampilkan
    </div>
@else
    <div class="mb-4">
        <p class="text-sm text-gray-600">Data untuk tanggal: <span class="font-semibold">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</span></p>
    </div>
    
    @foreach($powerPlants as $powerPlant)
        <div class="bg-white rounded-lg shadow p-6 mb-4">
            <!-- Judul dan Informasi Unit -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <div class="w-full">
    
                        <h1 class="text-lg font-semibold uppercase mb-2">STATUS MESIN - {{ $powerPlant->name }}</h1>
                        <div class="grid grid-cols-6 gap-4">
                            @php
                                $machineCount = $powerPlant->machines->count();
                                $operasiCount = $logs->whereIn('machine_id', $powerPlant->machines->pluck('id'))->where('status', 'Operasi')->count();
                                $gangguanCount = $logs->whereIn('machine_id', $powerPlant->machines->pluck('id'))->where('status', 'Gangguan')->count();
                                $pemeliharaanCount = $logs->whereIn('machine_id', $powerPlant->machines->pluck('id'))->where('status', 'Pemeliharaan')->count();
                                $standbyCount = $logs->whereIn('machine_id', $powerPlant->machines->pluck('id'))->where('status', 'Standby')->count();
                                $overhaulCount = $logs->whereIn('machine_id', $powerPlant->machines->pluck('id'))->where('status', 'Overhaul')->count();
                            @endphp
                            
                            <div class="bg-gray-100 p-4 rounded-lg shadow-md hover:bg-gray-200 transition duration-300">
                                <p class="text-sm text-gray-700 font-medium">Total Mesin</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $machineCount }}</p>
                            </div>
                            <div class="bg-emerald-100 p-4 rounded-lg shadow-md hover:bg-emerald-200 transition duration-300">
                                <p class="text-sm text-emerald-700 font-medium">Operasi</p>
                                <p class="text-2xl font-bold text-emerald-900">{{ $operasiCount }}</p>
                            </div>
                            <div class="bg-rose-100 p-4 rounded-lg shadow-md hover:bg-rose-200 transition duration-300">
                                <p class="text-sm text-rose-700 font-medium">Gangguan</p>
                                <p class="text-2xl font-bold text-rose-900">{{ $gangguanCount }}</p>
                            </div>
                            <div class="bg-amber-100 p-4 rounded-lg shadow-md hover:bg-amber-200 transition duration-300">
                                <p class="text-sm text-amber-700 font-medium">Pemeliharaan</p>
                                <p class="text-2xl font-bold text-amber-900">{{ $pemeliharaanCount }}</p>
                            </div>
                            <div class="bg-sky-100 p-4 rounded-lg shadow-md hover:bg-sky-200 transition duration-300">
                                <p class="text-sm text-sky-700 font-medium">Standby</p>
                                <p class="text-2xl font-bold text-sky-900">{{ $standbyCount }}</p>
                            </div>
                            <div class="bg-violet-100 p-4 rounded-lg shadow-md hover:bg-violet-200 transition duration-300">
                                <p class="text-sm text-violet-700 font-medium">Overhaul</p>
                                <p class="text-2xl font-bold text-violet-900">{{ $overhaulCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel -->
            <div class="table-responsive">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">No</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Mesin</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">DMN</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">DMP</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Beban</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Status</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Component</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Equipment</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Deskripsi</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Kronologi</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Action Plan</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Progress</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Gambar</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Tanggal Mulai</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center">Target Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($powerPlant->machines as $index => $machine)
                            @php
                                $log = $logs->firstWhere('machine_id', $machine->id);
                                $status = $log?->status ?? '-';
                                $statusClass = match($status) {
                                    'Operasi' => 'bg-green-100 text-green-800',
                                    'Standby' => 'bg-blue-100 text-blue-800',
                                    'Gangguan' => 'bg-red-100 text-red-800',
                                    'Pemeliharaan' => 'bg-orange-100 text-orange-800',
                                    'Overhaul' => 'bg-violet-100 text-violet-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 border border-gray-200">
                                <td class="px-3 py-2 border-r border-gray-200 text-center">{{ $index + 1 }}</td>
                                <td class="px-3 py-2 border-r border-gray-200" data-id="{{ $machine->id }}">{{ $machine->name }}</td>
                                <td class="px-3 py-2 border-r border-gray-200 text-center">{{ $log?->dmn ?? '-' }}</td>
                                <td class="px-3 py-2 border-r border-gray-200 text-center">{{ $log?->dmp ?? '-' }}</td>
                                <td class="px-3 py-2 border-r border-gray-200 text-center">{{ $log?->load_value ?? '-' }}</td>
                                <td class="px-3 py-2 border-r border-gray-200 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 border-r border-gray-200">{{ $log?->component ?? '-' }}</td>
                                <td class="px-3 py-2 border-r border-gray-200">{{ $log?->equipment ?? '-' }}</td>
                                <td class="px-3 py-2 border-r border-gray-200">{{ $log?->deskripsi ?? '-' }}</td>
                                <td class="px-3 py-2 border-r border-gray-200">{{ $log?->kronologi ?? '-' }}</td>
                                <td class="px-3 py-2 border-r border-gray-200">{{ $log?->action_plan ?? '-' }}</td>
                                <td class="px-3 py-2 border-r border-gray-200">{{ $log?->progres ?? '-' }}</td>
                                <td class="px-3 py-2 border-r border-gray-200">
                                    @if($log && $log->image_path)
                                        <div class="flex flex-col items-center">
                                            <div class="relative group">
                                                @php
                                                    // Pastikan path gambar benar
                                                    $imagePath = str_replace('storage/', '', $log->image_path);
                                                    $fullImagePath = Storage::url($imagePath);
                                                @endphp
                                                <!-- Tampilkan gambar -->
                                                <img src="{{ $fullImagePath }}" 
                                                     alt="Status Image" 
                                                     class="w-12 h-12 object-cover rounded cursor-pointer"
                                                     onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'"
                                                     onclick="showSingleImage('{{ $fullImagePath }}', '{{ $log->image_description }}')">
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 border-r border-gray-200 text-center">
                                    {{ $log?->tanggal_mulai ? \Carbon\Carbon::parse($log->tanggal_mulai)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    {{ $log?->target_selesai ? \Carbon\Carbon::parse($log->target_selesai)->format('d/m/Y') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="px-3 py-4 text-center text-gray-500">
                                    Tidak ada data mesin untuk unit ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endif 

<script>
function showSingleImage(imagePath, description) {
    let html = `
        <div class="relative">
            <img src="${imagePath}" 
                 class="max-h-[70vh] mx-auto" 
                 alt="Machine Status Image">
            ${description ? `
                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-3">
                    ${description}
                </div>
            ` : ''}
        </div>
    `;

    Swal.fire({
        html: html,
        width: '80%',
        showCloseButton: true,
        showConfirmButton: false,
        imageAlt: 'Machine Status Image'
    });
}
</script>

@push('styles')
<style>
.swal2-popup img {
    max-width: 100%;
    height: auto;
    object-fit: contain;
}
</style>
@endpush 