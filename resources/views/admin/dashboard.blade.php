@extends('layouts.app')

@push('styles')
    <!-- Tambahkan Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Pastikan konten utama dapat di-scroll */
        .main-content {
            overflow-y: auto;
            /* Izinkan scroll vertikal */
            height: calc(100vh - 64px);
            /* Sesuaikan tinggi dengan mengurangi tinggi header */
        }
    </style>
@endpush

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
      @include('components.sidebar')
        <!-- Main Content -->
        <div id="main-content" class="flex-1 main-content">
            <!-- Header -->
            <header class="bg-white shadow-sm">
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

                        <h1 class="text-xl font-semibold text-gray-800">Dashboard Admin</h1>
                    </div>

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
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Dashboard', 'url' => null]]" />
            </div>

            <!-- Dashboard Content -->
            <main class="px-6">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Card 1 -->
                    <a href="{{ route('admin.daftar_hadir.rekapitulasi') }}">
                        <div class="bg-blue-500 rounded-lg shadow p-6 flex items-center">
                            <i class="fa-solid fa-users text-white text-3xl mr-3"></i>
                            <div class="flex-1">
                                <h3 class="text-white text-md font-medium">PRESENTASI KEHADIRAN </h3>
                            </div>
                            <p class="text-2xl font-bold text-white" id="total-users">
                                {{ $totalUsers }}
                            </p>
                        </div>
                    </a>
                    <!-- Card 2 -->
                    <a href="{{ route('admin.laporan.sr_wo_closed') }}">
                        <div class="bg-green-500 rounded-lg shadow p-6 flex items-center">
                            <i class="fa-solid fa-calendar-check text-white text-3xl mr-3"></i>
                            <div class="flex-1">
                                <h3 class="text-white text-md font-medium">TOTAL SR/WO CLOSED</h3>
                            </div>
                            <p class="text-2xl font-bold text-white" id="today-meetings">
                                {{ $totalClosedSRWO }}
                            </p>
                        </div>
                    </a>
                    <!-- Card 3 -->
                    <div onclick="window.location.href='{{ route('admin.pembangkit.report') }}'"
                        class="bg-yellow-500 rounded-lg shadow p-6 flex items-center cursor-pointer hover:bg-yellow-600 transition-colors">
                        <i class="fa-solid fa-cogs text-white text-3xl mr-3"></i>
                        <div class="flex-1">
                            <h3 class="text-white text-md font-medium">JUMLAH MESIN GANGGUAN</h3>
                        </div>
                        <p class="text-2xl font-bold text-white" id="machine-issues">
                            @php
                                $gangguanCount = \App\Models\MachineStatusLog::where('status', 'Gangguan')
                                    ->whereDate('tanggal', now())
                                    ->count();
                            @endphp
                            {{ $gangguanCount }}
                        </p>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Card Ketepatan Waktu -->
                    <div class="bg-white rounded-lg shadow p-6" style="height: 400px;">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Ketepatan Waktu</h3>
                            <div class="flex space-x-2">
                                <button onclick="toggleChartType('activityChart', 'line')"
                                    class="p-2 hover:bg-gray-100 rounded-lg" title="Tampilkan Grafik Garis">
                                    <i class="fas fa-chart-line"></i>
                                </button>
                                <button onclick="toggleChartType('activityChart', 'bar')"
                                    class="p-2 hover:bg-gray-100 rounded-lg" title="Tampilkan Grafik Batang">
                                    <i class="fas fa-chart-bar"></i>
                                </button>
                            </div>
                        </div>
                        <div style="height: 300px;">
                            <canvas id="activityChart"></canvas>
                        </div>
                    </div>

                    <!-- Card Kehadiran Rapat -->
                    <div class="bg-white rounded-lg shadow p-6" style="height: 400px;">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Kehadiran Rapat</h3>
                            <div class="flex space-x-2">
                                <button onclick="toggleChartType('meetingChart', 'line')"
                                    class="p-2 hover:bg-gray-100 rounded-lg" title="Tampilkan Grafik Garis">
                                    <i class="fas fa-chart-line"></i>
                                </button>
                                <button onclick="toggleChartType('meetingChart', 'bar')"
                                    class="p-2 hover:bg-gray-100 rounded-lg" title="Tampilkan Grafik Batang">
                                    <i class="fas fa-chart-bar"></i>
                                </button>
                            </div>
                        </div>
                        <div style="height: 300px;">
                            <canvas id="meetingChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities Table -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
                            <button onclick="exportActivities()"
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                <i class="fas fa-download mr-2"></i>Ekspor
                            </button>
                        </div>
                        <div class="overflow-x-auto border-2 border-gray-200 rounded-lg">
                            <table id="activities-table" class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Aktivitas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Pengguna</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($recentActivities as $activity)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $activity->description }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $activity->user->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $activity->created_at->diffForHumans() }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $activity->status_color }}">
                                                    {{ $activity->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="{{ asset('js/toggle.js') }}"></script>
    <script>
        // Inisialisasi DataTables
        $(document).ready(function() {
            $('#activities-table').DataTable({
                responsive: true,
                pageLength: 10,
                order: [
                    [2, 'desc']
                ]
            });
        });

        let activityChart, meetingChart;

        // Inisialisasi charts dengan data sementara
        document.addEventListener('DOMContentLoaded', function() {
            // Data sementara untuk 7 hari terakhir
            const sampleData = {
                dates: [
                    '2024-02-14', '2024-02-15', '2024-02-16',
                    '2024-02-17', '2024-02-18', '2024-02-19', '2024-02-20'
                ],
                scores: [85, 90, 88, 92, 87, 91, 89],
                attendance: [12, 15, 13, 14, 16, 15, 14]
            };

            // Format tanggal untuk label
            const formattedDates = sampleData.dates.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('id-ID', {
                    weekday: 'short',
                    month: 'short',
                    day: 'numeric'
                });
            });

            // Data untuk Ketepatan Waktu
            const activityData = {
                labels: formattedDates,
                datasets: [{
                    label: 'Skor Ketepatan Waktu',
                    data: sampleData.scores,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    tension: 0.1,
                    fill: true
                }]
            };

            // Data untuk Kehadiran Rapat
            const meetingData = {
                labels: formattedDates,
                datasets: [{
                    label: 'Jumlah Peserta',
                    data: sampleData.attendance,
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.5)',
                    tension: 0.1,
                    fill: true
                }]
            };

            // Konfigurasi umum untuk chart
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + (this.chart.config.type === 'activityChart' ? '%' :
                                    ' orang');
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                }
            };

            // Inisialisasi Chart Aktivitas
            activityChart = new Chart(document.getElementById('activityChart'), {
                type: 'line',
                data: activityData,
                options: {
                    ...commonOptions,
                    scales: {
                        ...commonOptions.scales,
                        y: {
                            ...commonOptions.scales.y,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });

            // Inisialisasi Chart Meeting
            meetingChart = new Chart(document.getElementById('meetingChart'), {
                type: 'line',
                data: meetingData,
                options: {
                    ...commonOptions,
                    scales: {
                        ...commonOptions.scales,
                        y: {
                            ...commonOptions.scales.y,
                            ticks: {
                                callback: function(value) {
                                    return value + ' orang';
                                }
                            }
                        }
                    }
                }
            });
        });

        // Fungsi untuk mengubah tipe chart
        function toggleChartType(chartId, newType) {
            const chart = chartId === 'activityChart' ? activityChart : meetingChart;

            // Simpan data dan options yang ada
            const data = chart.data;
            const options = chart.options;

            // Destroy chart lama
            chart.destroy();

            // Sesuaikan options berdasarkan tipe chart
            if (newType === 'bar') {
                // Untuk chart batang, hilangkan tension dan ubah fill
                data.datasets[0].tension = 0;
                data.datasets[0].fill = false;
            } else {
                // Untuk chart garis, tambahkan tension dan fill
                data.datasets[0].tension = 0.1;
                data.datasets[0].fill = true;
            }

            // Buat chart baru dengan tipe yang berbeda
            const newChart = new Chart(document.getElementById(chartId), {
                type: newType,
                data: data,
                options: {
                    ...options,
                    scales: {
                        ...options.scales,
                        y: {
                            ...options.scales.y,
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + (chartId === 'activityChart' ? '%' : ' orang');
                                }
                            }
                        }
                    }
                }
            });

            // Update referensi chart
            if (chartId === 'activityChart') {
                activityChart = newChart;
            } else {
                meetingChart = newChart;
            }
        }

        // Fungsi untuk export aktivitas
        function exportActivities() {
            window.location.href = '{{ route('admin.activities.export') }}';
        }

        // Fungsi untuk toggle dropdown
        function toggleDropdown() {
            w
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Tutup dropdown ketika mengklik di luar
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = event.target.closest('button');

            if (!button && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    @push('scripts')
    @endpush
@endsection
