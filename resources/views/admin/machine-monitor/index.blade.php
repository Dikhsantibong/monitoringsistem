@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Main Content -->
        <div id="main-content" class="flex-1 main-content">
            <!-- Header -->
            <header class="bg-white shadow-sm sticky top-0 z-10">
                <div class="flex justify-between items-center px-6 py-3">
                    <div class="flex items-center gap-x-3">
                        <!-- Mobile Menu Toggle -->
                        <button id="mobile-menu-toggle"
                            class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <!--  Menu Toggle Sidebar-->
                        <button id="desktop-menu-toggle"
                            class="hidden md:block relative items-center justify-center rounded-md text-gray-400 hover:bg-[#009BB9] p-2 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">Dasbor Pemantauan Mesin</h1>
                    </div>

                    @include('components.timer')
                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                class="w-7 h-7 rounded-full mr-2">
                            <span class="text-gray-700 text-sm">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2 text-gray-600"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">

                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>

            </header>
            <div class="flex items-center pt-2">
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Monitor Mesin', 'url' => null]]" />
            </div>

            <!-- Dashboard Content -->
            <main class="p-6">
                <!-- Indikator Kinerja -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <a href="{{ route('admin.machine-monitor.show', ['machine' => 1]) }}" class="bg-blue-500 rounded-lg shadow p-6 hover:bg-blue-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-white text-sm font-medium">Total Mesin</h3>
                                <p class="text-3xl font-bold text-white mt-1">
                                    {{ App\Models\Machine::count() }}
                                </p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-cog text-green-500 text-xl"></i>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.power-plants.index') }}" class="bg-green-500 rounded-lg shadow p-6 hover:bg-green-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-white text-sm font-medium">Total Unit</h3>
                                <p class="text-3xl font-bold text-white mt-1">
                                    {{ App\Models\PowerPlant::count() }}
                                </p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-building text-blue-500 text-xl"></i>
                            </div>
                        </div>
                    </a>

                    <div class="bg-red-500 rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-white text-sm font-medium">Masalah Aktif</h3>
                                <p class="text-3xl font-bold text-white mt-1">
                                    {{ $machines->sum(function ($machine) {
                                        return $machine->issues->where('status', 'open')->count();
                                    }) }}
                                </p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
                            

                <!-- Grafik Masalah dan Status Mesin -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Grafik Masalah Bulanan -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Masalah Unit Gangguan</h2>
                            <canvas id="monthlyIssuesChart" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Status Mesin -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Status Mesin</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 max-h-80 overflow-y-auto">
                            @foreach ($machines as $machine)
                                @php
                                    $statusLog = $machine->statusLogs()->latest()->first(); // Ambil status terbaru
                                @endphp
                                <div class="flex justify-between p-4 border rounded-lg hover:bg-gray-50">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-medium text-gray-800">{{ $machine->name }}</h3>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                            {{ $statusLog && $statusLog->status === 'START'
                                                ? 'bg-green-100 text-green-800'
                                                : ($statusLog && $statusLog->status === 'STOP'
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $statusLog->status ?? 'N/A' }}
                                            </span>
                                        </div>
                                        <div class="mt-1">
                                            <p class="text-sm text-gray-500">Kode: {{ $machine->kode }} | Unit: {{ $statusLog->powerPlant->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-cog text-xl text-gray-500"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Tabel Masalah Terbaru -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Masalah Terbaru</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mesin
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Kategori</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Deskripsi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($recentIssues as $issue)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $issue->created_at->format('M d, Y H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $issue->machine->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $issue->category->name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $issue->description }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $issue->status === 'open' ? 'bg-red-100 text-red-800' : ($issue->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                                    {{ ucfirst($issue->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $issue->machine->powerPlant->name ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pemantauan dan Statistik -->
                <h2 class="text-lg font-semibold text-gray-800 mb-4"></h2>

                <!-- Statistik Kinerja Mesin -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Grafik Efisiensi Mesin -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Frekuensi Gangguan Mesin</h3>
                        <canvas id="efficiencyChart" height="200"></canvas>
                    </div>

                    <!-- Ringkasan Kinerja -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Jam Gangguan Mesin</h3>
                        <div class="space-y-6 max-h-80 overflow-y-auto">
                            @foreach ($machines as $machine)
                                @php
                                    // Inisialisasi nilai default jika downtime_stats null
                                    $downtimeStats = $machine->downtime_stats ?? [
                                        'total_downtime' => 0,
                                        'current_downtime' => null,
                                        'is_down' => false
                                    ];
                                    
                                    $totalDowntime = $downtimeStats['total_downtime'] ?? 0;
                                    $isCurrentlyDown = $downtimeStats['is_down'] ?? false;
                                    $currentDowntimeDetails = $downtimeStats['current_downtime'] ?? null;
                                    
                                    // Ambil log status terbaru dengan pengecekan null
                                    $latestLog = $machine->statusLogs->first();
                                    $currentStatus = $latestLog ? strtolower($latestLog->status) : 'unknown';

                                    // Status dan warna dengan pengecekan null
                                    $statusClass = match($currentStatus) {
                                        'operasi' => 'bg-green-100 text-green-800',
                                        'standby' => 'bg-blue-100 text-blue-800',
                                        'gangguan' => 'bg-red-100 text-red-800',
                                        'mothballed' => 'bg-purple-100 text-purple-800',
                                        'overhaul' => 'bg-orange-100 text-orange-800',
                                        'pemeliharaan' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };

                                    // Warna progress bar dengan pengecekan null
                                    $colorClass = $totalDowntime > 100 ? 'bg-red-500' : ($totalDowntime > 50 ? 'bg-yellow-500' : 'bg-green-500');
                                    $textColorClass = $totalDowntime > 100 ? 'text-red-600' : ($totalDowntime > 50 ? 'text-yellow-600' : 'text-green-600');
                                @endphp

                                <div class="bg-gray-50 rounded-xl p-5 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-lg border border-gray-100">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-start space-x-3">
                                            <div class="mt-1">
                                                <div class="size-8 rounded-lg flex items-center justify-center {{ $statusClass }}">
                                                    <i class="fas {{ $isCurrentlyDown ? 'fa-exclamation-circle' : 'fa-check-circle' }}"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-800">{{ $machine->name }}</h4>
                                                <p class="text-sm text-gray-500">Unit: {{ $machine->powerPlant->name ?? 'N/A' }}</p>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }} mt-1">
                                                    {{ $latestLog ? ucfirst($latestLog->status) : 'Unknown' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <div class="flex items-center bg-gray-100 px-4 py-2 rounded-full">
                                                <i class="fas fa-clock text-gray-400 mr-2"></i>
                                                <span class="text-xl font-bold {{ $textColorClass }}">
                                                    {{ number_format($totalDowntime, 1) }}
                                                </span>
                                                <span class="text-sm text-gray-500 ml-1">jam</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if($isCurrentlyDown && $currentDowntimeDetails)
                                        <div class="mt-3 p-3 bg-red-50 rounded-lg border border-red-100">
                                            <div class="flex items-start space-x-2">
                                                <i class="fas fa-tools text-red-500 mt-1"></i>
                                                <div>
                                                    <p class="text-sm font-medium text-red-800">Detail Gangguan:</p>
                                                    <div class="text-xs text-red-600 mt-1 space-y-1">
                                                        <p>Durasi: {{ floor($currentDowntimeDetails['duration'] ?? 0) }} jam 
                                                            {{ round((($currentDowntimeDetails['duration'] ?? 0) - floor($currentDowntimeDetails['duration'] ?? 0)) * 60) }} menit</p>
                                                        @if(!empty($currentDowntimeDetails['component']))
                                                            <p>Komponen: {{ $currentDowntimeDetails['component'] }}</p>
                                                        @endif
                                                        @if(!empty($currentDowntimeDetails['equipment']))
                                                            <p>Peralatan: {{ $currentDowntimeDetails['equipment'] }}</p>
                                                        @endif
                                                        @if(!empty($currentDowntimeDetails['deskripsi']))
                                                            <p>Deskripsi: {{ $currentDowntimeDetails['deskripsi'] }}</p>
                                                        @endif
                                                        @if(!empty($currentDowntimeDetails['progres']))
                                                            <p>Progress: {{ $currentDowntimeDetails['progres'] }}%</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Tabel Detail Kinerja -->
                <div class="bg-white rounded-lg shadow mb-6 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Kinerja Mesin</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mesin
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total
                                        Jam
                                        Operasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Efisiensi
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Downtime
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Maintenance
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </main>
        </div>

        <!-- Modal Masalah Baru -->
        <div id="newIssueModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Laporkan Masalah Baru</h3>
                    <form id="newIssueForm">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Mesin</label>
                            <select name="machine_id"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                                required>
                                @foreach ($machines as $machine)
                                    <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                            <select name="category_id"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                                required>
                                @foreach ($healthCategories as $category)
                                    {{-- <option value="{{ $category->id }}">{{ $category->name }}</option> --}}
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                            <textarea name="description" rows="3"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" onclick="closeNewIssueModal()"
                                class="mr-2 px-4 py-2 text-gray-500 hover:text-gray-700">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                Kirim
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Machine Modal -->
        <div id="editMachineModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
            <!-- Similar structure to Add Machine Modal but with pre-filled values -->
        </div>
        <!-- Chart.js CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="{{ asset('js/toggle.js') }}"></script>
        <script>
            // Data sementara untuk demonstrasi
            const monthlyIssuesData = [{
                    date: 'Januari',
                    count: 10
                },
                {
                    date: 'Februari',
                    count: 20
                },
                {
                    date: 'Maret',
                    count: 15
                },
                {
                    date: 'April',
                    count: 25
                },
                {
                    date: 'Mei',
                    count: 30
                },
                {
                    date: 'Juni',
                    count: 35
                },
                {
                    date: 'Juli',
                    count: 40
                },
                {
                    date: 'Agustus',
                    count: 45
                },
                {
                    date: 'September',
                    count: 50
                },
                {
                    date: 'Oktober',
                    count: 55
                },
                {
                    date: 'November',
                    count: 60
                },
                {
                    date: 'Desember',
                    count: 65
                }
            ];

            // Ambil data dari PHP dan konversi ke format yang sesuai untuk Chart.js
            const powerPlantData = {!! json_encode($powerPlants->map(function($powerPlant) {
                return [
                    'name' => $powerPlant->name,
                    'issues' => $powerPlant->machines->sum(function($machine) {
                        return $machine->statusLogs()->count();
                    }),
                    'operations' => $powerPlant->machines->sum(function($machine) {
                        return $machine->machineOperations()->count();
                    })
                ];
            })) !!};

            // Siapkan data untuk grafik
            const datasets = [{
                label: 'Jumlah Gangguan',
                data: powerPlantData.map(plant => plant.issues),
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2
            }, {
                label: 'Jumlah Operasi',
                data: powerPlantData.map(plant => plant.operations),
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }];

            // Inisialisasi grafik
            const ctx = document.getElementById('monthlyIssuesChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: powerPlantData.map(plant => plant.name),
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Unit'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Gangguan dan Operasi per Unit'
                        }
                    }
                }
            });

            // Fungsi untuk membuka modal laporan masalah baru
            function openNewIssueModal() {
                document.getElementById('newIssueModal').classList.remove('hidden');
            }

            // Fungsi untuk menutup modal laporan masalah baru
            function closeNewIssueModal() {
                document.getElementById('newIssueModal').classList.add('hidden');
            }

            // Tutup modal saat mengklik di luar
            document.getElementById('newIssueModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeNewIssueModal();
                }
            });

            // Inisialisasi DataTable
            $(document).ready(function() {
                $('table').DataTable({
                    responsive: true,
                    pageLength: 10,
                    order: [
                        [0, 'desc']
                    ]
                });
            });

            // Fungsi untuk mengedit mesin
            function editMachine(machineId) {
                // Fetch detail mesin dan buka modal edit
                fetch(`/admin/machine-monitor/machines/${machineId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Isi form edit dengan data mesin
                        document.getElementById('editMachineModal').classList.remove('hidden');
                    });
            }

            // Fungsi untuk menghapus mesin
            function deleteMachine(machineId) {
                if (confirm('Apakah Anda yakin ingin menghapus mesin ini?')) {
                    fetch(`/admin/machine-monitor/machines/${machineId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            }
                        });
                }
            }

            // Fungsi untuk mengupdate status mesin
            function updateMachineStatus(machineId, status) {
                fetch(`/admin/machine-monitor/machines/${machineId}/status`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            status: status
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
            }

            // Fungsi untuk refresh status mesin
            function refreshMachineStatus() {
                location.reload();
            }

            // Data sementara untuk demonstrasi efisiensi mesin
            const efficiencyData = [{
                    name: 'Mesin 1',
                    efficiency: 80
                },
                {
                    name: 'Mesin 2',
                    efficiency: 75
                },
                {
                    name: 'Mesin 3',
                    efficiency: 90
                },
                {
                    name: 'Mesin 4',
                    efficiency: 85
                },
                {
                    name: 'Mesin 5',
                    efficiency: 95
                }
            ];

            // Grafik Efisiensi Mesin
            const ctxEfficiency = document.getElementById('efficiencyChart').getContext('2d');

            // Ambil data mesin dari PHP
            const machineData = {!! json_encode($machines->map(function($machine) {
                return [
                    'name' => $machine->name,
                    'status' => $machine->statusLogs()->latest()->first()->status ?? 'N/A',
                    'operations' => $machine->machineOperations()->count(),
                    'issues' => $machine->statusLogs()->count(),
                    'capacity' => $machine->capacity,
                    'efficiency' => $machine->machineOperations()
                        ->whereNotNull('load_value')
                        ->avg('load_value') ?? 0
                ];
            })) !!};

            new Chart(ctxEfficiency, {
                type: 'bar',
                data: {
                    labels: machineData.map(machine => machine.name),
                    datasets: [{
                        label: 'Jumlah Gangguan',
                        data: machineData.map(machine => machine.issues),
                        type: 'line',
                        fill: false,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nilai'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mesin'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Efisiensi dan Gangguan per Mesin'
                        }
                    }
                }
            });

            function populateEditForm(machine) {
                document.getElementById('name').value = machine.name;
                document.getElementById('code').value = machine.code;
                // Isi input lainnya sesuai kebutuhan
            }

            // Fungsi untuk toggle dropdown
            document.addEventListener('DOMContentLoaded', function() {
                const dropdownButton = document.querySelector('#machine-monitor-dropdown');
                const submenu = document.querySelector('#machine-monitor-submenu');
                let isOpen = false;

                // Check if current route is machine monitor or its children
                const isMonitorRoute = window.location.pathname.includes('/machine-monitor');
                if (isMonitorRoute) {
                    submenu.style.maxHeight = submenu.scrollHeight + 'px';
                    dropdownButton.classList.add('rotate-180');
                    isOpen = true;
                }

                dropdownButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    isOpen = !isOpen;
                    
                    if (isOpen) {
                        submenu.style.maxHeight = submenu.scrollHeight + 'px';
                        dropdownButton.classList.add('rotate-180');
                    } else {
                        submenu.style.maxHeight = '0';
                        dropdownButton.classList.remove('rotate-180');
                    }
                });
            });
        </script>

        @push('scripts')
        @endpush
    @endsection

    <style>
        /* Sembunyikan scrollbar tapi tetap bisa scroll */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .main-content {
        .scrollbar-hide {
            /* -ms-overflow-style: none; */
            /* scrollbar-width: none; */
        }

        /* Animasi untuk rotasi icon dropdown */
        .rotate-180 {
            transform: rotate(180deg);
        }

        /* Transisi untuk submenu */
        #machine-monitor-submenu {
            transition: max-height 0.3s ease-in-out;
            overflow: hidden; /* Menyembunyikan konten yang tidak terlihat */
        }

        /* Style untuk submenu items */
        #machine-monitor-submenu a {
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        #machine-monitor-submenu a:hover {
            padding-left: 1.5rem;
        }

        .bg-stripes {
            background-image: linear-gradient(
                45deg,
                rgba(255, 255, 255, 0.15) 25%,
                transparent 25%,
                transparent 50%,
                rgba(255, 255, 255, 0.15) 50%,
                rgba(255, 255, 255, 0.15) 75%,
                transparent 75%,
                transparent
            );
            background-size: 1rem 1rem;
        }

        .hover\:scale-\[1\.02\]:hover {
            transform: scale(1.02);
        }

        @keyframes move-stripes {
            from { background-position: 0 0; }
            to { background-position: 1rem 1rem; }
        }

        .animate-move-stripes {
            animation: move-stripes 1s linear infinite;
        }
    </style>
