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
                    <h1 class="text-xl font-semibold text-gray-800">Dasbor Pemantauan Mesin</h1>
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
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Tindakan Korektif</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-1">
                                    {{ $machines->sum(function ($machine) {
                                        return $machine->issues->where('status', 'closed')->count();
                                    }) }}
                                </p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-wrench text-green-500 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Akurasi Kerja</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-1">
                                    {{ number_format($machines->flatMap->metrics->avg('achievement_percentage'), 1) }}%
                                </p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-chart-line text-blue-500 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Masalah Aktif</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-1">
                                    {{ $machines->sum(function ($machine) {
                                        return $machine->issues->where('status', 'open')->count();
                                    }) }}
                                </p>
                            </div>
                            <div class="bg-red-100 p-3 rounded-full">
                                <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Peta Kesehatan Mesin -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Kategori Kesehatan Mesin</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach ($healthCategories as $category)
                                <div
                                    class="border rounded-lg p-4 {{ $category->open_issues > 0 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}">
                                    <h3 class="font-medium text-gray-800">{{ ucfirst($category->name) }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Masalah Aktif: {{ $category->open_issues }}
                                    </p>
                                </div>
                            @endforeach
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
                                    <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-800">{{ $machine->name }}</h3>
                                            <p class="text-sm text-gray-500">Kode: {{ $machine->code }}</p>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <!-- Status Badge -->
                                            <span
                                                class="px-3 py-1 rounded-full text-sm font-medium
                                        {{ $machine->status === 'START'
                                            ? 'bg-green-100 text-green-800'
                                            : ($machine->status === 'STOP'
                                                ? 'bg-red-100 text-red-800'
                                                : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $machine->status }}
                                            </span>

                                            <!-- Action Buttons -->
                                            <div class="flex space-x-2">
                                                <button onclick="updateMachineStatus({{ $machine->id }}, 'START')"
                                                    class="px-2 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">
                                                    Start
                                                </button>
                                                <button onclick="updateMachineStatus({{ $machine->id }}, 'STOP')"
                                                    class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">
                                                    Stop
                                                </button>
                                                <button onclick="editMachine({{ $machine->id }})"
                                                    class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">
                                                    Edit
                                                </button>
                                                <button onclick="deleteMachine({{ $machine->id }})"
                                                    class="px-2 py-1 text-xs bg-gray-500 text-white rounded hover:bg-gray-600">
                                                    Delete
                                                </button>
                                            </div>
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Tanggal
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Mesin
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Kategori</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Deskripsi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($recentIssues as $issue)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $issue->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $issue->machine->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $issue->category->name }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $issue->description }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $issue->status === 'open'
                                                ? 'bg-red-100 text-red-800'
                                                : ($issue->status === 'in_progress'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : 'bg-green-100 text-green-800') }}">
                                                    {{ ucfirst($issue->status) }}
                                                </span>
                                            </td>
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
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
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
            // Chart initialization
            const monthlyIssuesData = @json($monthlyIssues); // Ambil data dari controller

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

            // Modal functions
            function openNewIssueModal() {
                document.getElementById('newIssueModal').classList.remove('hidden');
            }

            function closeNewIssueModal() {
                document.getElementById('newIssueModal').classList.add('hidden');
            }

            // Close modal when clicking outside
            document.getElementById('newIssueModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeNewIssueModal();
                }
            });

            // DataTable initialization
            $(document).ready(function() {
                $('table').DataTable({
                    responsive: true,
                    pageLength: 10,
                    order: [
                        [0, 'desc']
                    ]
                });
            });

            function editMachine(machineId) {
                // Fetch machine details and open edit modal
                fetch(`/admin/machine-monitor/machines/${machineId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Populate edit form with machine data
                        document.getElementById('editMachineModal').classList.remove('hidden');
                    });
            }

            function deleteMachine(machineId) {
                if (confirm('Are you sure you want to delete this machine?')) {
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

            function refreshMachineStatus() {
                location.reload();
            }

            // Chart untuk Uptime/Downtime
            const ctx = document.getElementById('uptimeChart').getContext('2d');
            const uptimeData = @json($uptime);
            const labels = uptimeData.map(machine => machine.name);
            const uptimeValues = uptimeData.map(machine => machine.uptime);
            const downtimeValues = uptimeData.map(machine => machine.downtime);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Uptime',
                            data: uptimeValues,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        },
                        {
                            label: 'Downtime',
                            data: downtimeValues,
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Close modal when clicking outside
            document.getElementById('newMachineModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeNewMachineModal();
                }
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

        .scrollbar-hide {
            /* -ms-overflow-style: none; */
            /* scrollbar-width: none; */
        }
    </style>
