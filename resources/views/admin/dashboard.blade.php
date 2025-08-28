@extends('layouts.app')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    @include('components.sidebar')
    <!-- Main Content -->
    <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
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
                    <h1 class="text-xl font-semibold text-gray-800">Dashboard</h1>
                </div>
                <div class="flex items-center gap-x-4 relative">
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                class="w-8 h-8 rounded-full mr-2">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                            <a href="{{ route('user.profile') }}"
                                class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Overlay Scroll Down Info (modern, glassmorphism, animasi masuk & keluar) -->
        <div id="scroll-down-overlay" class="fixed left-1/2 bottom-8 transform -translate-x-1/2 bg-white bg-opacity-70 border border-blue-200 rounded-xl px-6 py-4 flex flex-col items-center z-50 cursor-pointer shadow-xl max-w-xs w-full backdrop-blur-md transition-all duration-500 opacity-0 translate-y-8" style="backdrop-filter: blur(8px);">
            <span class="text-base md:text-lg text-blue-700 font-semibold drop-shadow mb-1">Scroll ke bawah untuk melihat grafik lainnya</span>
            <span class="animate-bounce text-3xl text-blue-400 drop-shadow">&#8595;</span>
            <span class="text-xs text-blue-600 mt-1">Klik untuk menutup</span>
        </div>
        <main class="px-6 py-4">
            <!-- Baris 1: Kehadiran & Scorecard (2 kolom) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Kehadiran (Bar Chart) dengan Filter Unit -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-2">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Persentasi Kehadiran</h3>
                            <div class="text-xs text-gray-500">
                                Total: {{ $chartSummary['activity']['total'] ?? '-' }} | Rata-rata: {{ $chartSummary['activity']['avg'] ?? '-' }} peserta/hari
                            </div>
                        </div>
                        <div>
                            <label for="attendance-unit-filter" class="text-xs text-gray-600 mr-2">Filter Unit:</label>
                            <select id="attendance-unit-filter" class="border rounded px-2 py-1 text-sm">
                                <option value="all">Semua Unit</option>
                                @foreach($attendanceUnitLabels as $unitSource => $unitLabel)
                                    <option value="{{ $unitSource }}">{{ $unitLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <canvas id="activityChart" height="180"></canvas>
                </div>
                <!-- Score Daily Meeting (Line Chart) -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Score Daily Meeting</h3>
                        <div class="text-xs text-gray-500">
                            Rata-rata: {{ $chartSummary['meeting']['avg'] ?? '-' }}%
                        </div>
                    </div>
                    <canvas id="meetingChart" height="180"></canvas>
                </div>
            </div>
            <!-- Baris 2: Grafik utama status (3 kolom) -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <!-- Status SR (Pie/Doughnut) -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Presentasi Status SR</h3>
                        <div class="text-xs text-gray-500">
                            Open: {{ $chartSummary['sr']['open'] ?? '-' }} | Closed: {{ $chartSummary['sr']['closed'] ?? '-' }} | Closed %: {{ $chartSummary['sr']['closed_pct'] ?? '-' }}%
                        </div>
                    </div>
                    <canvas id="srChart" height="180"></canvas>
                </div>
                <!-- Status WO (Pie/Doughnut) -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Presentasi Status WO</h3>
                        <div class="text-xs text-gray-500">
                            Open: {{ $chartSummary['wo']['open'] ?? '-' }} | Closed: {{ $chartSummary['wo']['closed'] ?? '-' }} | Closed %: {{ $chartSummary['wo']['closed_pct'] ?? '-' }}%
                        </div>
                    </div>
                    <canvas id="woChart" height="180"></canvas>
                </div>
                <!-- WO Backlog (Bar) -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">WO Backlog Status</h3>
                        <div class="text-xs text-gray-500">
                            Open: {{ $chartSummary['backlog']['open'] ?? '-' }}
                        </div>
                    </div>
                    <canvas id="woBacklogChart" height="180"></canvas>
                </div>
            </div>
            <!-- Baris 3: Grafik tambahan (3 kolom) -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <!-- Tren Kehadiran per Unit (Multi-Line) -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Tren Kehadiran Harian per Unit</h3>
                        <div class="text-xs text-gray-500">Perbandingan kehadiran harian antar unit</div>
                    </div>
                    <canvas id="unitAttendanceTrendsChart" height="220"></canvas>
                </div>
                <!-- Distribusi Status Mesin -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Distribusi Status Mesin</h3>
                        <div class="text-xs text-gray-500">Jumlah mesin per status (akumulasi seluruh unit)</div>
                    </div>
                    <canvas id="machineStatusDistChart" height="180"></canvas>
                </div>
                <!-- WO/SR/Material per Bulan -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">WO, SR, Pengajuan Material per Bulan</h3>
                        <div class="text-xs text-gray-500">6 bulan terakhir</div>
                    </div>
                    <canvas id="monthlyCountsChart" height="180"></canvas>
                </div>
            </div>
            <!-- Baris 4: Grafik tambahan (2 kolom) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Penyelesaian WO/SR per Unit (Stacked Bar) -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Penyelesaian WO & SR per Unit</h3>
                        <div class="text-xs text-gray-500">Open vs Closed per unit</div>
                    </div>
                    <canvas id="woSrCompletionChart" height="180"></canvas>
                </div>
                <!-- Top 5 Material Paling Sering Diajukan + Komitmen & Pembahasan -->
                <div class="flex flex-col gap-6">
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Top 5 Material Paling Sering Diajukan</h3>
                            <div class="text-xs text-gray-500">Material dengan pengajuan terbanyak</div>
                        </div>
                        <canvas id="topMaterialsChart" height="90"></canvas>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Komitmen & Pembahasan Lain-lain per Status</h3>
                            <div class="text-xs text-gray-500">Open vs Closed (akumulasi seluruh unit)</div>
                        </div>
                        <canvas id="commitmentDiscussionStatusChart" height="90"></canvas>
                    </div>
                </div>
            </div>
            <!-- Baris 5: Status Pembahasan Lain-lain & Status Komitmen (2 kolom, PALING BAWAH) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Status Pembahasan Lain-lain (Pie) -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Status Pembahasan Lain-lain</h3>
                        <div class="text-xs text-gray-500">
                            Open: {{ $chartSummary['other_discussion']['open'] ?? '-' }} | Closed: {{ $chartSummary['other_discussion']['closed'] ?? '-' }} | Closed %: {{ $chartSummary['other_discussion']['closed_pct'] ?? '-' }}%
                        </div>
                    </div>
                    <canvas id="otherDiscussionChart" height="180"></canvas>
                </div>
                <!-- Status Komitmen (Pie) -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Status Komitmen</h3>
                        <div class="text-xs text-gray-500">
                            Open: {{ $chartSummary['commitment']['open'] ?? '-' }} | Closed: {{ $chartSummary['commitment']['closed'] ?? '-' }} | Closed %: {{ $chartSummary['commitment']['closed_pct'] ?? '-' }}%
                        </div>
                    </div>
                    <canvas id="commitmentChart" height="180"></canvas>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Overlay scroll down info animasi masuk & keluar
        const overlay = document.getElementById('scroll-down-overlay');
        if (overlay) {
            // Animasi masuk (fade-in + slide-up)
            setTimeout(() => {
                overlay.classList.remove('opacity-0', 'translate-y-8');
                overlay.classList.add('opacity-100', 'translate-y-0');
            }, 100);
            // Animasi keluar saat klik
            overlay.addEventListener('click', function() {
                overlay.classList.remove('opacity-100', 'translate-y-0');
                overlay.classList.add('opacity-0', 'translate-y-8');
                setTimeout(() => { overlay.style.display = 'none'; }, 500);
            });
        }
        const attendancePerUnit = @json($attendancePerUnit);
        const attendanceUnitLabels = @json($attendanceUnitLabels);
        const chartData = @json($chartData);
        let activityChartInstance;
        function renderAttendanceChart(unitSource) {
            const ctx = document.getElementById('activityChart').getContext('2d');
            if (activityChartInstance) activityChartInstance.destroy();
            let labels = [], data = [], label = '';
            if (unitSource === 'all') {
                labels = chartData.scoreCardData.dates || [];
                data = chartData.scoreCardData.counts || [];
                label = 'Jumlah Peserta Hadir (Total)';
            } else {
                // Mirip filter WO/SR per Unit: gunakan attendancePerUnit dan attendanceUnitLabels
                labels = Object.keys(attendancePerUnit[unitSource] || {});
                data = Object.values(attendancePerUnit[unitSource] || {});
                label = 'Jumlah Peserta Hadir (' + (attendanceUnitLabels[unitSource] || unitSource) + ')';
            }
            activityChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        title: { display: true, text: label }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true }
                    }
                }
            });
        }
        // Inisialisasi chart pertama kali (total)
        renderAttendanceChart('all');
        // Event listener untuk filter
        document.getElementById('attendance-unit-filter').addEventListener('change', function(e) {
            renderAttendanceChart(e.target.value);
        });
        // Meeting (Line)
        new Chart(document.getElementById('meetingChart'), {
            type: 'line',
            data: {
                labels: chartData.attendanceData.dates,
                datasets: [{
                    label: 'Score Rata-rata',
                    data: chartData.attendanceData.scores,
                    fill: false,
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'Score Rata-rata Daily Meeting' }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });
        // SR (Pie)
        new Chart(document.getElementById('srChart'), {
            type: 'pie',
            data: {
                labels: ['Open', 'Closed'],
                datasets: [{
                    data: chartData.srData.counts,
                    backgroundColor: ['#f59e42', '#10b981'],
                    borderColor: ['#f59e42', '#10b981'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Status Service Request' }
                }
            }
        });
        // WO (Doughnut)
        new Chart(document.getElementById('woChart'), {
            type: 'doughnut',
            data: {
                labels: ['Open', 'Closed'],
                datasets: [{
                    data: chartData.woData.counts,
                    backgroundColor: ['#f43f5e', '#6366f1'],
                    borderColor: ['#f43f5e', '#6366f1'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Status Work Order' }
                }
            }
        });
        // WO Backlog (Bar)
        new Chart(document.getElementById('woBacklogChart'), {
            type: 'bar',
            data: {
                labels: ['Open'],
                datasets: [{
                    label: 'WO Backlog',
                    data: chartData.woBacklogData.counts,
                    backgroundColor: ['#fbbf24'],
                    borderColor: ['#f59e42'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'WO Backlog Status' }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true }
                }
            }
        });
        // Other Discussion (Pie)
        new Chart(document.getElementById('otherDiscussionChart'), {
            type: 'pie',
            data: {
                labels: ['Open', 'Closed'],
                datasets: [{
                    data: chartData.otherDiscussionData.counts,
                    backgroundColor: ['#f472b6', '#818cf8'],
                    borderColor: ['#f472b6', '#818cf8'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Status Pembahasan Lain-lain' }
                }
            }
        });
        // Commitment (Pie)
        new Chart(document.getElementById('commitmentChart'), {
            type: 'pie',
            data: {
                labels: ['Open', 'Closed'],
                datasets: [{
                    data: chartData.commitmentData.counts,
                    backgroundColor: ['#facc15', '#22d3ee'],
                    borderColor: ['#facc15', '#22d3ee'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Status Komitmen' }
                }
            }
        });
        // 1. Tren Kehadiran per Unit (Multi-Line)
        const unitAttendanceTrends = @json($unitAttendanceTrends);
        const unitAttendanceLabels = Object.values(unitAttendanceTrends)[0] ? Object.keys(Object.values(unitAttendanceTrends)[0]) : [];
        const unitAttendanceDatasets = Object.entries(unitAttendanceTrends).map(([unit, data], idx) => ({
            label: unit,
            data: Object.values(data),
            borderColor: ['#2563eb','#16a34a','#f59e42','#f43f5e','#6366f1'][idx % 5],
            backgroundColor: 'rgba(0,0,0,0)',
            tension: 0.3
        }));
        new Chart(document.getElementById('unitAttendanceTrendsChart'), {
            type: 'line',
            data: {
                labels: unitAttendanceLabels,
                datasets: unitAttendanceDatasets
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Tren Kehadiran Harian per Unit' }
                },
                scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
            }
        });
        // 2. Distribusi Status Mesin
        const machineStatusDist = @json($machineStatusDist);
        new Chart(document.getElementById('machineStatusDistChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(machineStatusDist),
                datasets: [{
                    label: 'Jumlah Mesin',
                    data: Object.values(machineStatusDist),
                    backgroundColor: ['#2563eb','#16a34a','#f59e42','#f43f5e','#6366f1','#f472b6','#818cf8'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'Distribusi Status Mesin' }
                },
                scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
            }
        });
        // 3. Jumlah WO/SR/Pengajuan Material per Bulan
        const monthlyCounts = @json($monthlyCounts);
        new Chart(document.getElementById('monthlyCountsChart'), {
            type: 'bar',
            data: {
                labels: monthlyCounts.map(x => x.month),
                datasets: [
                    { label: 'WO', data: monthlyCounts.map(x => x.wo), backgroundColor: '#2563eb' },
                    { label: 'SR', data: monthlyCounts.map(x => x.sr), backgroundColor: '#16a34a' },
                    { label: 'Material', data: monthlyCounts.map(x => x.material), backgroundColor: '#f59e42' },
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'WO, SR, Pengajuan Material per Bulan' }
                },
                scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
            }
        });
        // 4. Penyelesaian WO/SR per Unit (Stacked Bar)
        const woSrCompletion = @json($woSrCompletion);
        const woSrUnits = Object.keys(woSrCompletion);
        new Chart(document.getElementById('woSrCompletionChart'), {
            type: 'bar',
            data: {
                labels: woSrUnits,
                datasets: [
                    { label: 'WO Open', data: woSrUnits.map(u => woSrCompletion[u].wo_open), backgroundColor: '#f59e42' },
                    { label: 'WO Closed', data: woSrUnits.map(u => woSrCompletion[u].wo_closed), backgroundColor: '#16a34a' },
                    { label: 'SR Open', data: woSrUnits.map(u => woSrCompletion[u].sr_open), backgroundColor: '#f43f5e' },
                    { label: 'SR Closed', data: woSrUnits.map(u => woSrCompletion[u].sr_closed), backgroundColor: '#6366f1' },
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Penyelesaian WO & SR per Unit' }
                },
                scales: { x: { stacked: true }, y: { beginAtZero: true, stacked: true } }
            }
        });
        // 5. Top 5 Material Paling Sering Diajukan
        const topMaterials = @json($topMaterials);
        new Chart(document.getElementById('topMaterialsChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(topMaterials),
                datasets: [{
                    label: 'Jumlah Pengajuan',
                    data: Object.values(topMaterials),
                    backgroundColor: '#2563eb',
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'Top 5 Material Paling Sering Diajukan' }
                },
                scales: { x: { beginAtZero: true } }
            }
        });
        // 6. Komitmen & Pembahasan Lain-lain per Status
        const commitmentDiscussionStatus = @json($commitmentDiscussionStatus);
        new Chart(document.getElementById('commitmentDiscussionStatusChart'), {
            type: 'bar',
            data: {
                labels: ['Komitmen Open', 'Komitmen Closed', 'Pembahasan Open', 'Pembahasan Closed'],
                datasets: [{
                    label: 'Jumlah',
                    data: [
                        commitmentDiscussionStatus.commitment.Open,
                        commitmentDiscussionStatus.commitment.Closed,
                        commitmentDiscussionStatus.discussion.Open,
                        commitmentDiscussionStatus.discussion.Closed
                    ],
                    backgroundColor: ['#f59e42','#16a34a','#f43f5e','#6366f1'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'Komitmen & Pembahasan Lain-lain per Status' }
                },
                scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
            }
        });
    });
</script>
@endpush