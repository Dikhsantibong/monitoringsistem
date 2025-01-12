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
                            <div class="bg-gray-100 p-4 rounded-lg shadow-md hover:bg-gray-200 transition duration-300">
                                <p class="text-sm text-gray-700 font-medium">Total Mesin</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $powerPlant->machines->count() }}</p>
                            </div>
                            <div class="bg-emerald-100 p-4 rounded-lg shadow-md hover:bg-emerald-200 transition duration-300">
                                <p class="text-sm text-emerald-700 font-medium">Operasi</p>
                                <p class="text-2xl font-bold text-emerald-900">{{ $logs->where('machine.power_plant_id', $powerPlant->id)->where('status', 'Operasi')->count() }}</p>
                            </div>
                            <div class="bg-rose-100 p-4 rounded-lg shadow-md hover:bg-rose-200 transition duration-300">
                                <p class="text-sm text-rose-700 font-medium">Gangguan</p>
                                <p class="text-2xl font-bold text-rose-900">{{ $logs->where('machine.power_plant_id', $powerPlant->id)->where('status', 'Gangguan')->count() }}</p>
                            </div>
                            <div class="bg-amber-100 p-4 rounded-lg shadow-md hover:bg-amber-200 transition duration-300">
                                <p class="text-sm text-amber-700 font-medium">Pemeliharaan</p>
                                <p class="text-2xl font-bold text-amber-900">{{ $logs->where('machine.power_plant_id', $powerPlant->id)->where('status', 'Pemeliharaan')->count() }}</p>
                            </div>
                            <div class="bg-sky-100 p-4 rounded-lg shadow-md hover:bg-sky-200 transition duration-300">
                                <p class="text-sm text-sky-700 font-medium">Standby</p>
                                <p class="text-2xl font-bold text-sky-900">{{ $logs->where('machine.power_plant_id', $powerPlant->id)->where('status', 'Standby')->count() }}</p>
                            </div>
                            <div class="bg-violet-100 p-4 rounded-lg shadow-md hover:bg-violet-200 transition duration-300">
                                <p class="text-sm text-violet-700 font-medium">Overhaul</p>
                                <p class="text-2xl font-bold text-violet-900">{{ $logs->where('machine.power_plant_id', $powerPlant->id)->where('status', 'Overhaul')->count() }}</p>
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
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Tanggal Mulai</th>
                            <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center">Target Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($powerPlant->machines as $index => $machine)
                            @php
                                $log = $logs->where('machine_id', $machine->id)->first();
                                $status = $log->status ?? '-';
                                $statusClass = match($status) {
                                    'Operasi' => 'bg-green-100 text-green-800',
                                    'Standby' => 'bg-blue-100 text-blue-800',
                                    'Gangguan' => 'bg-red-100 text-red-800',
                                    'Pemeliharaan' => 'bg-orange-100 text-orange-800',
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
                                <td class="px-3 py-2 border-r border-gray-200 text-center">
                                    {{ $log?->tanggal_mulai ? date('d/m/Y', strtotime($log->tanggal_mulai)) : '-' }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    {{ $log?->target_selesai ? date('d/m/Y', strtotime($log->target_selesai)) : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endif 