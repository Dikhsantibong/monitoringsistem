@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
        <aside id="mobile-menu"
            class="fixed z-20 transform overflow-hidden transition-transform duration-300 md:relative md:translate-x-0 h-screen w-64 bg-[#0A749B] shadow-md text-white hidden md:block md:shadow-lg">
            <div class="p-4 flex items-center gap-3">
                <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Rapat Harian" class="w-40 h-15">
                <!-- Mobile Menu Toggle -->
                <button id="menu-toggle-close"
                    class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                    aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <nav class="mt-4">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-4 py-3  {{ request()->routeIs('admin.dashboard') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-home mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.pembangkit.ready') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.pembangkit.ready') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-check mr-3"></i>
                    <span>Kesiapan Pembangkit</span>
                </a>
                <a href="{{ route('admin.laporan.sr_wo') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.laporan.sr_wo') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-file-alt mr-3"></i>
                    <span>Laporan SR/WO</span>
                </a>
                <a href="{{ route('admin.machine-monitor') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.machine-monitor') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-cogs mr-3"></i>
                    <span>Monitor Mesin</span>
                </a>
                <a href="{{ route('admin.daftar_hadir.index') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.daftar_hadir.index') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-list mr-3"></i>
                    <span>Daftar Hadir</span>
                </a>
                <a href="{{ route('admin.score-card.index') }}"
                    class="flex items-center px-4 py-3  {{ request()->routeIs('admin.score-card.*') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    <span>Score Card Daily</span>
                </a>
                <a href="{{ route('admin.users') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-users mr-3"></i>
                    <span>Manajemen Pengguna</span>
                </a>
                <a href="{{ route('admin.meetings') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.meetings') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-chart-bar mr-3"></i>
                    <span>Laporan Rapat</span>
                </a>
                <a href="{{ route('admin.settings') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Pengaturan</span>
                </a>
            </nav>
        </aside>

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
            <div class="flex justify-between items-center pt-2">
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Monitor Mesin', 'url' => null]]" />
                <div class="flex items-center space-x-4 px-6">
                    <button onclick="openNewIssueModal()"
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-exclamation-circle mr-2"></i>Laporkan Masalah
                    </button>
                    <button onclick="location.href='{{ route('admin.machine-monitor.create') }}'"
                        class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        <i class="fas fa-plus mr-2"></i>Tambah Mesin
                    </button>
                </div>
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

                    <div class="bg-green-500 rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-white text-sm font-medium">Akurasi Kerja</h3>
                                <p class="text-3xl font-bold text-white mt-1">
                                    {{ number_format($machines->flatMap->metrics->avg('achievement_percentage'), 1) }}%
                                </p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-chart-line text-blue-500 text-xl"></i>
                            </div>
                        </div>
                    </div>

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
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Masalah Bulanan</h2>
                            <canvas id="monthlyIssuesChart" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Status Mesin -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold text-gray-800">Status Mesin</h2>
                                <div class="flex space-x-2">
                                    <button onclick="refreshMachineStatus()" class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-4 max-h-60 overflow-y-auto flex flex-wrap w-full">
                                @foreach ($machines as $machine)
                                    @php
                                        $statusLog = $machine->statusLogs()->latest()->first(); // Ambil status terbaru
                                    @endphp
                                    <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-800">{{ $machine->name }}</h3>
                                            <p class="text-sm text-gray-500">Kode: {{ $machine->code }}</p>
                                            <p class="text-sm text-gray-500">Asal Unit:
                                                {{ $statusLog->powerPlant->name ?? 'N/A' }}</p>
                                            <!-- Menampilkan asal unit -->
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <!-- Status Badge -->
                                            <span
                                                class="px-3 py-1 rounded-full text-sm font-medium
                                        {{ $statusLog && $statusLog->status === 'START'
                                            ? 'bg-green-100 text-green-800'
                                            : ($statusLog && $statusLog->status === 'STOP'
                                                ? 'bg-red-100 text-red-800'
                                                : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $statusLog->status ?? 'N/A' }} <!-- Menampilkan status jika ada -->
                                            </span>
                                            <i class="fas fa-cog text-xl"></i> <!-- Ikon mesin -->
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Efisiensi Mesin</h3>
                        <canvas id="efficiencyChart" height="200"></canvas>
                    </div>

                    <!-- Ringkasan Kinerja -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Kinerja</h3>
                        <div class="space-y-4 max-h-60 overflow-y-auto">
                            @foreach ($machines as $machine)
                                <div class="border-b pb-3">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium">{{ $machine->name }}</span>
                                        <span class="text-sm">Efisiensi:
                                            {{ number_format($machine->metrics->avg('efficiency'), 1) }}%</span>
                                    </div>
                                    <div class="mt-2 h-2 bg-gray-200 rounded">
                                        <div class="h-full bg-blue-500 rounded"
                                            style="width: {{ $machine->metrics->avg('efficiency') }}%"></div>
                                    </div>
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

            // Inisialisasi grafik
            const ctx = document.getElementById('monthlyIssuesChart').getContext('2d');
            new Chart(ctx, {
                type: 'line', // Menggunakan grafik garis
                data: {
                    labels: monthlyIssuesData.map(issue => issue.date), // Ambil tanggal dari data
                    datasets: [{
                        label: 'Jumlah Masalah',
                        data: monthlyIssuesData.map(issue => issue.count), // Ambil jumlah masalah dari data
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3 // Untuk membuat garis lebih halus
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Masalah'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Tanggal'
                            }
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

            // Inisialisasi grafik untuk efisiensi mesin
            const ctxEfficiency = document.getElementById('efficiencyChart').getContext('2d');
            new Chart(ctxEfficiency, {
                type: 'bar', // Grafik batang
                data: {
                    labels: efficiencyData.map(machine => machine.name), // Nama mesin
                    datasets: [{
                        label: 'Efisiensi (%)',
                        data: efficiencyData.map(machine => machine.efficiency), // Data efisiensi
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Efisiensi (%)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Nama Mesin'
                            }
                        }
                    }
                }
            });

            function populateEditForm(machine) {
                document.getElementById('name').value = machine.name;
                document.getElementById('code').value = machine.code;
                // Isi input lainnya sesuai kebutuhan
            }
        </script>

        @push('scripts')
        @endpush
    @endsection

    <style>
        /* Sembunyikan scrollbar tapi tetap bisa scroll */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }


        .scrollbar-hide {
            /* -ms-overflow-style: none; */
            /* scrollbar-width: none; */
        }
    </style>
