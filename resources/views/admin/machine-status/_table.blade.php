@if($powerPlants->isEmpty())
    <div class="text-center py-4 text-gray-500">
        Tidak ada data untuk ditampilkan
    </div>
@else
    {{-- <div class="mb-4">
        <p class="text-sm text-gray-600">Data untuk tanggal: <span class="font-semibold">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</span></p>
    </div> --}}
    
    @foreach($powerPlants as $powerPlant)
        @unless($powerPlant->name === 'UP KENDARI')
            <div class="bg-white rounded-lg shadow p-6 mb-4">
                <!-- Judul dan Informasi Unit -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <div class="w-full">    
                            <div class="flex justify-between items-center mb-2">
                                <h1 class="text-lg font-semibold uppercase">STATUS MESIN - {{ $powerPlant->name }}</h1>
                                @php
                                    // Ambil update terakhir untuk unit ini
                                    $lastUpdate = $logs->whereIn('machine_id', $powerPlant->machines->pluck('id'))
                                        ->max('updated_at');
                                    
                                    // Format waktu update terakhir
                                    $formattedLastUpdate = $lastUpdate 
                                        ? \Carbon\Carbon::parse($lastUpdate)->format('d/m/Y H:i:s')
                                        : '-';
                                @endphp
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">Update Terakhir:</span>
                                    <span class="ml-1">{{ $formattedLastUpdate }}</span>
                                </div>
                            </div>
                            
                            <!-- Tambahkan informasi total DMN, DMP, dan Beban -->
                            <div class="grid grid-cols-5 gap-4 mb-4">
                                @php
                                    // Filter logs berdasarkan tanggal yang dipilih
                                    $filteredLogs = $logs->filter(function($log) use ($date) {
                                        return $log->created_at->format('Y-m-d') === $date;
                                    });

                                    // Mengambil log terakhir untuk setiap mesin
                                    $latestLogs = $filteredLogs
                                        ->whereIn('machine_id', $powerPlant->machines->pluck('id'))
                                        ->groupBy('machine_id')
                                        ->map(function ($machineLogs) {
                                            return $machineLogs->sortByDesc('created_at')->first();
                                        });

                                    // Menghitung total DMP dan DMN dari log terakhir setiap mesin
                                    $totalDMP = $latestLogs->sum(function($log) {
                                        return is_numeric($log->dmp) ? (float) $log->dmp : 0;
                                    });
                                    
                                    $totalDMN = $latestLogs->sum(function($log) {
                                        return is_numeric($log->dmn) ? (float) $log->dmn : 0;
                                    });
                                    
                                    $totalBeban = $latestLogs->sum(function($log) {
                                        if ($log->status === 'Operasi') {
                                            return is_numeric($log->load_value) ? (float) $log->load_value : 0;
                                        }
                                        return 0;
                                    });

                                    // Ambil data HOP untuk power plant ini
                                    $hopValue = \App\Models\UnitOperationHour::where('power_plant_id', $powerPlant->id)
                                        ->whereDate('tanggal', $date)
                                        ->value('hop_value') ?? 0;
                                    
                                    // Tentukan status HOP
                                    $hopStatus = $hopValue >= 15 ? 'aman' : 'siaga';
                                    $hopClass = $hopStatus === 'aman' ? 'text-green-600' : 'text-red-600';
                                @endphp
                                
                                
                                <div class="bg-blue-50 p-3 rounded-lg md:col-span-1 col-span-5">
                                    <p class="text-sm text-gray-600">DMN:</p>
                                    <p class="text-xl font-bold text-blue-700">{{ number_format($totalDMP, 2) }} MW</p>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg md:col-span-1 col-span-5">
                                    <p class="text-sm text-gray-600">DMP:</p>
                                    <p class="text-xl font-bold text-green-700">{{ number_format($totalDMN, 2) }} MW</p>
                                </div>
                                
                                <div class="bg-red-50 p-3 rounded-lg md:col-span-1 col-span-5">
                                    <p class="text-sm text-gray-600 ">Derating:</p>
                                    <p class="text-xl font-bold text-red-700">
                                        {{ number_format($totalDMP - $totalDMN, 2) }} MW 
                                        @if($totalDMN > 0)
                                            ({{ number_format((($totalDMP - $totalDMN) / $totalDMP) * 100, 2) }}%)
                                        @else
                                            (0%)
                                        @endif
                                    </p>
                                </div>
                                <div class="bg-purple-50 p-3 rounded-lg md:col-span-1 col-span-5">
                                    <p class="text-sm text-gray-600">Total Beban:</p>
                                    <p class="text-xl font-bold text-purple-700">{{ number_format($totalBeban, 2) }} MW</p>
                                </div>
                                <div class="bg-orange-50 p-3 rounded-lg md:col-span-1 col-span-5">
                                    <p class="text-sm text-gray-600">
                                        @if(str_starts_with(trim(strtoupper($powerPlant->name)), 'PLTM '))
                                            Total Inflow:
                                        @else
                                            Total HOP:
                                        @endif
                                    </p>    
                                    <p class="text-xl font-bold text-orange-700">
                                        {{ number_format($hopValue, 1) }} 
                                        @if(str_starts_with(trim(strtoupper($powerPlant->name)), 'PLTM '))
                                            m³/s
                                        @else
                                            Hari
                                        @endif
                                    </p>
                                    @unless(str_starts_with(trim(strtoupper($powerPlant->name)), 'PLTM '))
                                        <p class="text-sm font-medium {{ $hopClass }}">
                                            Status: {{ ucfirst($hopStatus) }}
                                        </p>
                                    @endunless
                                </div>
                            </div>

                            <div class="grid grid-cols-7 gap-4">
                                @php
                                    $machineCount = $powerPlant->machines->count();
                                    
                                    // Menghitung status berdasarkan log terakhir
                                    $operasiCount = $latestLogs->where('status', 'Operasi')->count();
                                    $gangguanCount = $latestLogs->where('status', 'Gangguan')->count();
                                    $pemeliharaanCount = $latestLogs->where('status', 'Pemeliharaan')->count();
                                    $standbyCount = $latestLogs->where('status', 'Standby')->count();
                                    $overhaulCount = $latestLogs->where('status', 'Overhaul')->count();
                                    $mothballedCount = $latestLogs->where('status', 'Mothballed')->count();
                                @endphp
                                
                                <div class="bg-gray-100 p-4 rounded-lg shadow-md hover:bg-gray-200 transition duration-300 md:col-span-1 col-span-7">
                                    <p class="text-sm text-gray-700 font-medium">Total Mesin</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $machineCount }}</p>
                                </div>
                                <div class="bg-emerald-100 p-4 rounded-lg shadow-md hover:bg-emerald-200 transition duration-300 md:col-span-1 col-span-7">
                                    <p class="text-sm text-emerald-700 font-medium">Operasi</p>
                                    <p class="text-2xl font-bold text-emerald-900">{{ $operasiCount }}</p>
                                </div>
                                <div class="bg-rose-100 p-4 rounded-lg shadow-md hover:bg-rose-200 transition duration-300 md:col-span-1 col-span-7">
                                    <p class="text-sm text-rose-700 font-medium">Gangguan</p>
                                    <p class="text-2xl font-bold text-rose-900">{{ $gangguanCount }}</p>
                                </div>
                                <div class="bg-amber-100 p-4 rounded-lg shadow-md hover:bg-amber-200 transition duration-300 md:col-span-1 col-span-7">
                                    <p class="text-sm text-amber-700 font-medium">Pemeliharaan</p>
                                    <p class="text-2xl font-bold text-amber-900">{{ $pemeliharaanCount }}</p>
                                </div>
                                <div class="bg-sky-100 p-4 rounded-lg shadow-md hover:bg-sky-200 transition duration-300 md:col-span-1 col-span-7">
                                    <p class="text-sm text-sky-700 font-medium">Standby</p>
                                    <p class="text-2xl font-bold text-sky-900">{{ $standbyCount }}</p>
                                </div>
                                <div class="bg-violet-100 p-4 rounded-lg shadow-md hover:bg-violet-200 transition duration-300 md:col-span-1 col-span-7">
                                    <p class="text-sm text-violet-700 font-medium">Overhaul</p>
                                    <p class="text-2xl font-bold text-violet-900">{{ $overhaulCount }}</p>
                                </div>
                                <div class="bg-gray-100 p-4 rounded-lg shadow-md hover:bg-gray-200 transition duration-300 md:col-span-1 col-span-7">
                                    <p class="text-sm text-gray-700 font-medium">Mothballed</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $mothballedCount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel -->
                
                <div class="table-responsive">
                    <table class="min-w-full bg-white table-fixed">
                        <thead>
                            <tr>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">No</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Mesin</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Daya Mampu Slim (MW)</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Daya Mampu Pasok (MW)</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Beban (MW)</th>
                                
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Status</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Issue Engine</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Catatan Issue</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Deskripsi</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Kronologi</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Action Plan</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Progress</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Dokumentasi</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Keterangan Gambar</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Tanggal Mulai</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center">Target Selesai</th>
                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($powerPlant->machines as $index => $machine)
                                @php
                                    $log = $filteredLogs->firstWhere('machine_id', $machine->id);
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
                                    <td class="px-3 py-2 border-r border-gray-200 text-center">{{ $log?->dmp ?? '-' }}</td> 
                                    <td class="px-3 py-2 border-r border-gray-200 text-center">{{ $log?->dmn ?? '-' }}</td>
                                    <td class="px-3 py-2 border-r border-gray-200 text-center">{{ $log?->load_value ?? '-' }}</td>
                                  
                                    <td class="px-3 py-2 border-r border-gray-200 text-center">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 border-r border-gray-200 text-center">
                                        <span class="{{ $log?->component ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} px-2 py-1 rounded-full text-xs font-medium">
                                            {{ $log?->component ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 border-r border-gray-200 !text-left" data-content-type="equipment" style="text-align: left !important;">
                                        <div class="min-w-[300px] max-w-full overflow-hidden !text-left" style="text-align: left !important; justify-content: flex-start !important;">
                                            <div class="max-h-[150px] overflow-y-auto whitespace-pre-wrap break-words !text-center" style="text-align: center !important; justify-content: flex-start !important;">
                                                <span style="text-align: left !important; display: block;">{{ $log?->equipment ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 border-r border-gray-200 !text-left" data-content-type="description" style="text-align: left !important;">
                                        <div class="min-w-[300px] max-w-full overflow-hidden !text-left" style="text-align: left !important; justify-content: flex-start !important;">
                                            <div class="max-h-[150px] overflow-y-auto whitespace-pre-wrap break-words !text-left" style="text-align: left !important; justify-content: flex-start !important;">
                                                <span style="text-align: left !important; display: block;">{{ $log?->deskripsi ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 border-r border-gray-200 !text-left" data-content-type="kronologi" style="text-align: left !important;">
                                        <div class="min-w-[300px] max-w-full overflow-hidden !text-left" style="text-align: left !important; justify-content: flex-start !important;">
                                            <div class="max-h-[150px] overflow-y-auto whitespace-pre-wrap break-words !text-left" style="text-align: left !important; justify-content: flex-start !important;">
                                                <span style="text-align: left !important; display: block;">{{ $log?->kronologi ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 border-r border-gray-200 !text-left" data-content-type="action-plan" style="text-align: left !important;">
                                        <div class="min-w-[450px] max-w-full overflow-hidden !text-left" style="text-align: left !important; justify-content: flex-start !important;">
                                            <div class="max-h-[250px] overflow-y-auto whitespace-pre-wrap break-words !text-left" style="text-align: left !important; justify-content: flex-start !important;">
                                                <span style="text-align: left !important; display: block;">{{ $log?->action_plan ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 border-r border-gray-200 !text-left" data-content-type="progress" style="text-align: left !important;">
                                        <div class="min-w-[450px] max-w-full overflow-hidden !text-left" style="text-align: left !important; justify-content: flex-start !important;">
                                            <div class="max-h-[250px] overflow-y-auto whitespace-pre-wrap break-words !text-left" style="text-align: left !important; justify-content: flex-start !important;">
                                                <span style="text-align: left !important; display: block;">{{ $log?->progres ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 border-r border-gray-200">
                                        @if($log?->image_path)
                                            <div class="flex justify-center">
                                                <div class="w-32 h-32 border rounded-lg overflow-hidden">
                                                    <img src="{{ asset('storage/' . $log->image_path) }}" 
                                                         alt="Dokumentasi" 
                                                         class="w-full h-full object-cover"
                                                         onclick="showFullImage(this.src)"
                                                         style="cursor: pointer;">
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 border-r border-gray-200 !text-left" data-content-type="image-description" style="text-align: left !important;">
                                        <div class="min-w-[300px] max-w-full overflow-hidden !text-left" style="text-align: left !important; justify-content: flex-start !important;">
                                            <div class="max-h-[150px] overflow-y-auto whitespace-pre-wrap break-words !text-left" style="text-align: left !important; justify-content: flex-start !important;">
                                                <span style="text-align: left !important; display: block;">{{ $log?->image_description ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 border-r border-gray-200 text-center">
                                        {{ $log?->tanggal_mulai ? \Carbon\Carbon::parse($log->tanggal_mulai)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-center border-r border-gray-200">
                                        {{ $log?->target_selesai ? \Carbon\Carbon::parse($log->target_selesai)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <div class="items-center justify-center space-y-2">
                                            <button onclick="editMachineStatus({{ $machine->id }}, '{{ $log?->id ?? 0 }}')" 
                                                    class="flex items-center justify-center w-8 h-8 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteMachineStatus({{ $machine->id }}, '{{ $log?->id ?? 0 }}')" 
                                                    class="flex items-center justify-center w-8 h-8 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="px-3 py-4 text-center text-gray-500">
                                        Tidak ada data mesin untuk unit ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                
            </div>
        @endunless
    @endforeach
@endif 

<style>
/* Highest specificity selectors */
table.min-w-full td[data-content-type="equipment"],
table.min-w-full td[data-content-type="description"],
table.min-w-full td[data-content-type="kronologi"],
table.min-w-full td[data-content-type="action-plan"],
table.min-w-full td[data-content-type="progress"],
table.min-w-full td[data-content-type] div,
table.min-w-full td[data-content-type] div div {
    text-align: left !important;
    justify-content: flex-start !important;
}

/* Additional specificity for nested elements */
.table-responsive table.min-w-full td[data-content-type] *,
.table-responsive table.min-w-full td[data-content-type] div *,
.table-responsive table.min-w-full td[data-content-type] div div * {
    text-align: left !important;
    justify-content: flex-start !important;
}

/* Force left alignment with max specificity */
body .table-responsive table.min-w-full td[data-content-type],
body .table-responsive table.min-w-full td[data-content-type] > div,
body .table-responsive table.min-w-full td[data-content-type] > div > div {
    text-align: left !important;
    justify-content: flex-start !important;
}
</style>

<script>
function editMachineStatus(machineId, logId) {
    if (logId == 0) {
        alert('Tidak ada data log untuk diedit');
        return;
    }
    
    // Gunakan URL yang benar dengan base_url
    const baseUrl = '{{ url("/") }}';
    const editUrl = `${baseUrl}/admin/machine-status/${machineId}/edit/${logId}`;
    
    fetch(editUrl)
        .then(response => response.text())
        .then(text => {
            try {
                const json = JSON.parse(text);
                if (!json.success) {
                    throw new Error(json.message);
                }
            } catch (e) {
                // Redirect ke URL yang benar
                window.location.href = editUrl;
                return;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Terjadi kesalahan saat mengakses data');
        });
}

function deleteMachineStatus(machineId, logId) {
    if (logId == 0) {
        alert('Tidak ada data log untuk dihapus');
        return;
    }
    
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        
        fetch(`/admin/machine-status/${machineId}/destroy/${logId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data berhasil dihapus');
                window.location.reload();
            } else {
                if (response.status === 404) {
                    alert('Data log tidak ditemukan atau sudah tidak tersedia');
                    window.location.reload();
                } else {
                    alert(data.message || 'Terjadi kesalahan saat menghapus data');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data');
        });
    }
}
</script> 

<script>
function showFullImage(src) {
    Swal.fire({
        imageUrl: src,
        imageAlt: 'Dokumentasi',
        width: '80%',
        imageWidth: '100%',
        imageHeight: 'auto',
        showConfirmButton: false,
        showCloseButton: true,
        padding: '1rem'
    });
}
</script> 