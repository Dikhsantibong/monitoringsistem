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

        /* Styling untuk container grafik */
        .chart-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 500px !important; /* Tinggi container */
            margin: 0 auto;
            padding: 20px;
        }

        /* Styling untuk canvas grafik */
        #srChart, #woChart, #woBacklogChart {
            max-width: 400px !important;
            max-height: 400px !important;
            margin: 0 auto;
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
                            <a href="{{ route('logout') }}" 
                               class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                               onclick="event.preventDefault(); 
                                        document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                                <input type="hidden" name="redirect" value="{{ route('homepage') }}">
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
                <!-- Charts -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Card Ketepatan Waktu -->
                    <div class="bg-white rounded-lg shadow p-6" style="height: 400px;">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Persentasi Kehadiran</h3>
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
                            <h3 class="text-lg font-semibold text-gray-800">Score Daily Meeting</h3>
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

                <!-- Diagram SR dan WO -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Card SR Status -->
                    <div class="bg-white rounded-lg shadow p-6" style="height: 400px;">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Presentasi Status SR</h3>
                            <div class="flex space-x-2">
                                <button onclick="toggleChartType('srChart', 'pie')" 
                                    class="p-2 hover:bg-gray-100 rounded-lg" title="Tampilkan Grafik Pie">
                                    <i class="fas fa-chart-pie"></i>
                                </button>
                                <button onclick="toggleChartType('srChart', 'doughnut')"
                                    class="p-2 hover:bg-gray-100 rounded-lg" title="Tampilkan Grafik Donat">
                                    <i class="fas fa-circle-notch"></i>
                                </button>
                            </div>
                        </div>
                        <div class="relative" style="height: 300px;">
                            <canvas id="srChart"></canvas>
                            <div id="srStats" class="absolute bottom-0 left-0 text-sm text-gray-600 p-2"></div>
                        </div>
                    </div>

                    <!-- Card WO Status -->
                    <div class="bg-white rounded-lg shadow p-6" style="height: 400px;">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Presentasi Status WO</h3>
                            <div class="flex space-x-2">
                                <button onclick="toggleChartType('woChart', 'pie')"
                                    class="p-2 hover:bg-gray-100 rounded-lg" title="Tampilkan Grafik Pie">
                                    <i class="fas fa-chart-pie"></i>
                                </button>
                                <button onclick="toggleChartType('woChart', 'doughnut')"
                                    class="p-2 hover:bg-gray-100 rounded-lg" title="Tampilkan Grafik Donat">
                                    <i class="fas fa-circle-notch"></i>
                                </button>
                            </div>
                        </div>
                        <div class="relative" style="height: 300px;">
                            <canvas id="woChart"></canvas>
                            <div id="woStats" class="absolute bottom-0 left-0 text-sm text-gray-600 p-2"></div>
                        </div>
                    </div>

                    <!-- Card WO Backlog Status -->
                    <div class="bg-white rounded-lg shadow p-6" style="height: 400px;">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">WO Backlog Status</h3>
                            <div class="flex space-x-2">
                                <button onclick="toggleChartType('woBacklogChart', 'pie')"
                                    class="p-2 hover:bg-gray-100 rounded-lg" title="Tampilkan Grafik Pie">
                                    <i class="fas fa-chart-pie"></i>
                                </button>
                                <button onclick="toggleChartType('woBacklogChart', 'doughnut')"
                                    class="p-2 hover:bg-gray-100 rounded-lg" title="Tampilkan Grafik Donat">
                                    <i class="fas fa-circle-notch"></i>
                                </button>
                            </div>
                        </div>
                        <div class="relative" style="height: 300px;">
                            <canvas id="woBacklogChart"></canvas>
                            <div id="woBacklogStats" class="absolute bottom-0 left-0 text-sm text-gray-600 p-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Diagram Pembahasan dan Komitmen -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Card Pembahasan Lain-lain Status -->
                    <div class="bg-white rounded-lg shadow p-6" style="height: 400px;">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Status Pembahasan Lain-lain</h3>
                            <div class="flex space-x-2">
                                <button onclick="toggleChartType('otherDiscussionChart', 'pie')" 
                                    class="p-2 hover:bg-gray-100 rounded-lg" title="Tampilkan Grafik Pie">
                                    <i class="fas fa-chart-pie"></i>
                                </button>
                                <button onclick="toggleChartType('otherDiscussionChart', 'doughnut')"
                                    class="p-2 hover:bg-gray-100 rounded-lg" title="Tampilkan Grafik Donat">
                                    <i class="fas fa-circle-notch"></i>
                                </button>
                            </div>
                        </div>
                        <div class="relative" style="height: 300px;">
                            <canvas id="otherDiscussionChart"></canvas>
                            <div id="otherDiscussionStats" class="absolute bottom-0 left-0 text-sm text-gray-600 p-2"></div>
                        </div>
                    </div>

                    <!-- Card Komitmen Status -->
                    <div class="bg-white rounded-lg shadow p-6" style="height: 400px;">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Status Komitmen</h3>
                            <div class="flex space-x-2">
                                <button onclick="toggleChartType('commitmentChart', 'pie')"
                                    class="p-2 hover:bg-gray-100 rounded-lg" title="Tampilkan Grafik Pie">
                                    <i class="fas fa-chart-pie"></i>
                                </button>
                                <button onclick="toggleChartType('commitmentChart', 'doughnut')"
                                    class="p-2 hover:bg-gray-100 rounded-lg" title="Tampilkan Grafik Donat">
                                    <i class="fas fa-circle-notch"></i>
                                </button>
                            </div>
                        </div>
                        <div class="relative" style="height: 300px;">
                            <canvas id="commitmentChart"></canvas>
                            <div id="commitmentStats" class="absolute bottom-0 left-0 text-sm text-gray-600 p-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities Table -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
                            {{-- <button onclick="exportActivities()"
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                <i class="fas fa-download mr-2"></i>Ekspor
                            </button> --}}
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

        let activityChart, meetingChart, srChart, woChart, woBacklogChart, otherDiscussionChart, commitmentChart;

        // Inisialisasi charts dengan data sementara
        document.addEventListener('DOMContentLoaded', function() {
            // Data dari controller
            const chartData = @json($chartData);
            
            // Format tanggal untuk label
            const formattedDates = chartData.scoreCardData.dates.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('id-ID', {
                    weekday: 'short',
                    month: 'short',
                    day: 'numeric'
                });
            });

            // Data untuk Ketepatan Waktu (Activity Chart)
            const activityData = {
                labels: formattedDates,
                datasets: [{
                    label: 'Persentase Kehadiran',
                    data: chartData.scoreCardData.scores,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    tension: 0.1,
                    fill: true
                }]
            };

            // Data untuk Score Peserta
            const meetingData = {
                labels: formattedDates,
                datasets: [{
                    label: 'Score Daily Meeting (%)',
                    data: chartData.attendanceData.scores.map(score => {
                        // Pastikan hasil tidak lebih dari 100%
                        const percentage = Math.round((score / 1000) * 100);
                        return Math.min(percentage, 100); // Membatasi nilai maksimum ke 100
                    }),
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
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Kehadiran: ${context.parsed.y}%`;
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
                            max: 100, // Set maksimum skala Y ke 100
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });

            // Data untuk SR Chart
            const srData = {
                labels: ['Open', 'Closed'],
                datasets: [{
                    data: chartData.srData.counts,
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)', // Merah untuk Open
                        'rgba(14, 165, 233, 0.8)'  // Biru langit untuk Closed
                    ],
                    borderColor: [
                        'rgb(239, 68, 68)',
                        'rgb(14, 165, 233)'
                    ],
                    borderWidth: 1
                }]
            };

            // Data untuk WO Chart
            const woData = {
                labels: ['Open', 'Closed'],
                datasets: [{
                    data: chartData.woData.counts,
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)', // Merah untuk Open
                        'rgba(14, 165, 233, 0.8)'  // Biru langit untuk Closed
                    ],
                    borderColor: [
                        'rgb(239, 68, 68)',
                        'rgb(14, 165, 233)'
                    ],
                    borderWidth: 1
                }]
            };

            // Ganti konfigurasi pieOptions dengan yang lebih optimal
            const pieOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        align: 'center',
                        labels: {
                            padding: 15,
                            boxWidth: 12,
                            font: {
                                size: 11
                            },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    const dataset = data.datasets[0];
                                    const total = dataset.data.reduce((acc, value) => acc + value, 0);
                                    
                                    return data.labels.map((label, i) => {
                                        const value = dataset.data[i];
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label}: ${value} (${percentage}%)`,
                                            fillStyle: dataset.backgroundColor[i],
                                            strokeStyle: dataset.borderColor[i],
                                            lineWidth: 1,
                                            hidden: isNaN(dataset.data[i]) || chart.getDatasetMeta(0).data[i].hidden,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        left: 20,
                        right: 20,
                        top: 20,
                        bottom: 40
                    }
                }
            };

            // Update inisialisasi chart dengan konfigurasi baru
            srChart = new Chart(document.getElementById('srChart'), {
                type: 'pie',
                data: srData,
                options: {
                    ...pieOptions,
                    plugins: {
                        ...pieOptions.plugins,
                        title: {
                            display: true,
                            text: 'Status SR',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                }
            });

            woChart = new Chart(document.getElementById('woChart'), {
                type: 'pie',
                data: woData,
                options: {
                    ...pieOptions,
                    plugins: {
                        ...pieOptions.plugins,
                        title: {
                            display: true,
                            text: 'Status WO',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                }
            });

            // Update data untuk WO Backlog Chart
            const woBacklogData = {
                labels: ['Open'],
                datasets: [{
                    data: chartData.woBacklogData.counts,
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)'  // Merah untuk Open
                    ],
                    borderColor: [
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 1
                }]
            };

            // Update stats untuk WO Backlog
            const woBacklogStats = document.getElementById('woBacklogStats');
            const openBacklogs = chartData.woBacklogData.counts[0];
            woBacklogStats.innerHTML = `Open: ${openBacklogs}`;

            woBacklogChart = new Chart(document.getElementById('woBacklogChart'), {
                type: 'pie',
                data: woBacklogData,
                options: {
                    ...pieOptions,
                    plugins: {
                        ...pieOptions.plugins,
                        title: {
                            display: true,
                            text: 'WO Backlog Priority',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                }
            });

            // Data untuk Other Discussion Chart
            const otherDiscussionData = {
                labels: ['Open', 'Closed'],
                datasets: [{
                    data: chartData.otherDiscussionData.counts,
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)', // Merah untuk Open
                        'rgba(14, 165, 233, 0.8)'  // Biru langit untuk Closed
                    ],
                    borderColor: [
                        'rgb(239, 68, 68)',
                        'rgb(14, 165, 233)'
                    ],
                    borderWidth: 1
                }]
            };

            // Data untuk Commitment Chart
            const commitmentData = {
                labels: ['Open', 'Closed'],
                datasets: [{
                    data: chartData.commitmentData.counts,
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)', // Merah untuk Open
                        'rgba(14, 165, 233, 0.8)'  // Biru langit untuk Closed
                    ],
                    borderColor: [
                        'rgb(239, 68, 68)',
                        'rgb(14, 165, 233)'
                    ],
                    borderWidth: 1
                }]
            };

            // Debug: Log data komitmen
            console.log('Commitment Data:', chartData.commitmentData);

            // Inisialisasi Other Discussion Chart
            otherDiscussionChart = new Chart(document.getElementById('otherDiscussionChart'), {
                type: 'pie',
                data: otherDiscussionData,
                options: {
                    ...pieOptions,
                    plugins: {
                        ...pieOptions.plugins,
                        title: {
                            display: true,
                            text: 'Status Pembahasan Lain-lain',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                }
            });

            // Inisialisasi Commitment Chart
            commitmentChart = new Chart(document.getElementById('commitmentChart'), {
                type: 'pie',
                data: commitmentData,
                options: {
                    ...pieOptions,
                    plugins: {
                        ...pieOptions.plugins,
                        title: {
                            display: true,
                            text: 'Status Komitmen',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    const label = context.label || '';
                                    return `${label}: ${value}`;
                                }
                            }
                        }
                    }
                }
            });

            // Update stats untuk commitment
            const commitmentStats = document.getElementById('commitmentStats');
            if (commitmentStats) {
                const commitmentTotal = commitmentData.datasets[0].data.reduce((a, b) => a + b, 0);
                commitmentStats.innerHTML = `Total: ${commitmentTotal} | Open: ${commitmentData.datasets[0].data[0]} | Closed: ${commitmentData.datasets[0].data[1]}`;
            }
        });

        // Fungsi untuk mengubah tipe chart
        function toggleChartType(chartId, newType) {
            const chart = chartId === 'srChart' ? srChart : 
                         chartId === 'woChart' ? woChart :
                         chartId === 'woBacklogChart' ? woBacklogChart :
                         chartId === 'otherDiscussionChart' ? otherDiscussionChart :
                         chartId === 'commitmentChart' ? commitmentChart :
                         chartId === 'activityChart' ? activityChart : meetingChart;

            if (['srChart', 'woChart', 'woBacklogChart', 'otherDiscussionChart', 'commitmentChart'].includes(chartId)) {
                // Untuk chart SR, WO, dan WO Backlog
                const data = chart.data;
                const options = chart.options;
                chart.destroy();
                
                const newChart = new Chart(document.getElementById(chartId), {
                    type: newType,
                    data: data,
                    options: options
                });

                if (chartId === 'srChart') {
                    srChart = newChart;
                } else if (chartId === 'woChart') {
                    woChart = newChart;
                } else if (chartId === 'woBacklogChart') {
                    woBacklogChart = newChart;
                } else if (chartId === 'otherDiscussionChart') {
                    otherDiscussionChart = newChart;
                } else {
                    commitmentChart = newChart;
                }
            } else {
                // Existing logic untuk activity dan meeting charts
                // ... (kode yang sudah ada)
            }
        }

        // Fungsi untuk export aktivitas
        function exportActivities() {
            window.location.href = '{{ route('admin.activities.export') }}';
        }

        // Fungsi untuk toggle dropdown
        function toggleDropdown() {
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
