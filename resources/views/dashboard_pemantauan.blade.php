

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-1">
        <!-- Tombol Kembali ke Homepage -->
        <div class="flex justify-end mb-4">
            <a href="{{ url('/') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                Kembali ke Homepage
            </a>
        </div>
        @if(isset($error))
            <div class="bg-red-500 text-white p-4 rounded-lg mb-4">
                {{ $error }}
            </div>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
            <div class="dashboard-card p-4 rounded-lg h-[300px]">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg">KESELURUHAN KONDISI MESIN</h2>
                </div>
                <div class="mt-4 flex justify-center items-center h-[200px]">
                    <div class="w-48 h-48">
                        <canvas id="oeeChart"></canvas>
                    </div>
                </div>
                <p class="text-center mt-2">Presentasi Mesin Gangguan</p>
            </div>
            <div class="col-span-2">
                <div class="dashboard-card p-4 rounded-lg h-[300px]">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg">KONDISI PEMELIHARAAN MESIN</h2>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-4 h-[200px]">
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-32">
                                <canvas id="availabilityChart"></canvas>
                            </div>
                            <p class="mt-2">Mothballed</p>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-32">
                                <canvas id="performanceChart"></canvas>
                            </div>
                            <p class="mt-2">Pemeliharaan</p>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-32">
                                <canvas id="qualityChart"></canvas>
                            </div>
                            <p class="mt-2">Overhaul</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div class="dashboard-card p-4 rounded-lg">
                <h3 class="text-lg">PRESENTASI TOTAL DAYA HILANG</h3>
                <div class="mt-2">
                    <p>Derating <span class="float-right">85%</span></p>
                    <div class="w-full bg-gray-300 rounded-full h-2.5 mb-4">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: 85%"></div>
                    </div>
                    <p>Gangguan <span class="float-right">50%</span></p>
                    <div class="w-full bg-gray-300 rounded-full h-2.5 mb-4">
                        <div class="bg-red-600 h-2.5 rounded-full" style="width: 50%"></div>
                    </div>
                </div>
            </div>
            <div class="dashboard-card p-4 rounded-lg col-span-2 overflow-auto">
                <h3 class="text-lg">Data Mesin</h3>
                <div class="mt-2 h-[200px] overflow-y-auto">
                    <table class="w-full border-gray-300">
                        <thead class="bg-gray-200 sticky top-0">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Nama Mesin Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Serial Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Tanggal Mulai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Target Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($machineData as $index => $machine)
                                <tr class="hover:bg-blue-800">
                                    <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">{{ $machine['type'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">{{ $machine['unit_name'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">{{ $machine['serial_number'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap border border-gray-200">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($machine['status'] == 'Mothballed')
                                                bg-blue-500
                                            @elseif($machine['status'] == 'Maintenance')
                                                bg-yellow-500
                                            @elseif($machine['status'] == 'Overhaul')
                                                bg-red-500
                                            @else
                                                bg-green-500
                                            @endif 
                                            text-white">
                                            {{ $machine['status'] ?? 'Normal' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">{{ $machine['tanggal_mulai'] ?? 'tidak ada data'}}</td>
                                    <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white"> {{ $machine[ 'target_selesai'] ?? 'tidak ada data' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center whitespace-nowrap border border-gray-200 text-white">
                                        Tidak ada data mesin
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
       
       
        

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <!-- Target vs Realisasi -->
            <div class="dashboard-card p-4 rounded-lg">
                <h3 class="text-lg mb-4">Target vs Realisasi</h3>
                <div class="h-48">
                    <canvas id="completionChart"></canvas>
                </div>
            </div>

            <!-- Progress Pekerjaan --> 
            <div class="dashboard-card p-4 rounded-lg">
                <h3 class="text-lg mb-4">Progress Pekerjaan</h3>
                <div class="h-48">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>

            <!-- Tren Penyelesaian -->
            <div class="dashboard-card p-4 rounded-lg">
                <h3 class="text-lg mb-4">Tren Penyelesaian</h3>
                <div class="h-48">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script>
           // Konfigurasi umum untuk chart
        const chartConfig = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#ffffff',
                        padding: 10,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        };

        // OEE Chart
        new Chart(document.getElementById('oeeChart'), {
            type: 'doughnut',
            data: {
                labels: ['Normal', 'Gangguan'],
                datasets: [{
                    data: [
                        {{ $chartData['percentages']['active'] }},
                        {{ $chartData['percentages']['fault'] }}
                    ],
                    backgroundColor: ['#22c55e', '#ef4444']
                }]
            },
            options: {
                ...chartConfig,
                cutout: '65%'
            }
        });

        // Tambahkan variabel untuk menyimpan instance chart
        let availabilityChartInstance;
        
        // Modifikasi availabilityChart
        availabilityChartInstance = new Chart(document.getElementById('availabilityChart'), {
            type: 'doughnut',
            data: {
                labels: ['Mothballed', 'Normal'],
                datasets: [{
                    data: [
                        {{ $maintenanceData['mothballed']['current'] }},
                        {{ $maintenanceData['mothballed']['total'] - $maintenanceData['mothballed']['current'] }}
                    ],
                    backgroundColor: ['#0ea5e9', '#e5e7eb']
                }]
            },
            options: {
                ...chartConfig,
                cutout: '65%',
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        
                        if (index === 0) { // Jika yang diklik adalah segment Mothballed
                            // Tambahkan loading indicator
                            const tbody = document.querySelector('table tbody');
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center whitespace-nowrap border border-gray-200 text-white">
                                        <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...
                                    </td>
                                </tr>
                            `;

                            fetch('/get-mothballed-machines')
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.error) {
                                        throw new Error(data.error);
                                    }
                                    updateTable(data);
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    const tbody = document.querySelector('table tbody');
                                    tbody.innerHTML = `
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-center whitespace-nowrap border border-gray-200 text-white">
                                                Terjadi kesalahan saat memuat data
                                            </td>
                                        </tr>
                                    `;
                                });
                        } else {
                            // Kembalikan ke tampilan tabel default
                            location.reload();
                        }
                    }
                }
            }
        });

        // Fungsi untuk memperbarui tabel
        function updateTable(machines) {
            const tbody = document.querySelector('table tbody');
            tbody.innerHTML = ''; // Kosongkan tabel

            if (!machines || machines.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center whitespace-nowrap border border-gray-200 text-white">
                            Tidak ada data mesin dengan status Mothballed
                        </td>
                    </tr>
                `;
                return;
            }

            machines.forEach((machine, index) => {
                const statusClass = machine.status === 'Mothballed' ? 'bg-blue-500' :
                                  machine.status === 'Maintenance' ? 'bg-yellow-500' :
                                  machine.status === 'Overhaul' ? 'bg-red-500' : 'bg-green-500';

                // Tambahkan pengecekan null dengan operator nullish coalescing
                const tanggalMulai = machine.tanggal_mulai ?? 'N/A';
                const targetSelesai = machine.target_selesai ?? 'N/A';

                tbody.innerHTML += `
                    <tr class="hover:bg-blue-800">
                        <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">${index + 1}</td>
                        <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">${machine.type ?? 'N/A'}</td>
                        <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">${machine.unit_name ?? 'N/A'}</td>
                        <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">${machine.serial_number ?? 'N/A'}</td>
                        <td class="px-4 py-2 whitespace-nowrap border border-gray-200">
                            <span class="px-2 py-1 text-xs rounded-full ${statusClass} text-white">
                                ${machine.status || 'Normal'}
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">${tanggalMulai}</td>
                        <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">${targetSelesai}</td>
                    </tr>
                `;
            });
        }

        // Maintenance Chart (performanceChart)
        new Chart(document.getElementById('performanceChart'), {
            type: 'doughnut',
            data: {
                labels: ['Maintenance', 'Normal'],
                datasets: [{
                    data: [
                        {{ $maintenanceData['maintenance']['current'] }},
                        {{ $maintenanceData['maintenance']['total'] - $maintenanceData['maintenance']['current'] }}
                    ],
                    backgroundColor: ['#ef4444', '#e5e7eb']
                }]
            },
            options: {  
                ...chartConfig,
                cutout: '65%',
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        
                        if (index === 0) { // Jika yang diklik adalah segment Maintenance
                            // Tambahkan loading indicator
                            const tbody = document.querySelector('table tbody');
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center whitespace-nowrap border border-gray-200 text-white">
                                        <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...
                                    </td>
                                </tr>
                            `;

                            fetch('/get-maintenance-machines')
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.error) {
                                        throw new Error(data.error);
                                    }
                                    updateTable(data);
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    const tbody = document.querySelector('table tbody');
                                    tbody.innerHTML = `
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-center whitespace-nowrap border border-gray-200 text-white">
                                                Terjadi kesalahan saat memuat data
                                            </td>
                                        </tr>
                                    `;
                                });
                        } else {
                            // Kembalikan ke tampilan tabel default
                            location.reload();
                        }
                    }
                }
            }
        });

        // Overhaul Chart
        new Chart(document.getElementById('qualityChart'), {
            type: 'doughnut',
            data: {
                labels: ['Overhaul', 'Normal'],
                datasets: [{
                    data: [
                        {{ $maintenanceData['overhaul']['current'] }},
                        {{ $maintenanceData['overhaul']['total'] - $maintenanceData['overhaul']['current'] }}
                    ],
                    backgroundColor: ['#f59e0b', '#e5e7eb']
                }]
            },
            options: {
                ...chartConfig,
                cutout: '65%',
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        
                        if (index === 0) { // Jika yang diklik adalah segment Overhaul
                            // Tambahkan loading indicator
                            const tbody = document.querySelector('table tbody');
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center whitespace-nowrap border border-gray-200 text-white">
                                        <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...
                                    </td>
                                </tr>
                            `;

                            fetch('/get-overhaul-machines')
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.error) {
                                        throw new Error(data.error);
                                    }
                                    updateTable(data);
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    const tbody = document.querySelector('table tbody');
                                    tbody.innerHTML = `
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-center whitespace-nowrap border border-gray-200 text-white">
                                                Terjadi kesalahan saat memuat data
                                            </td>
                                        </tr>
                                    `;
                                });
                        } else {
                            // Kembalikan ke tampilan tabel default
                            location.reload();
                        }
                    }
                }
            }
        });

        // Target vs Realisasi Chart
        @if($completionData->isNotEmpty())
        new Chart(document.getElementById('completionChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($completionData->pluck('machine_name')) !!},
                datasets: [{
                    label: 'Target (Hari)',
                    data: {!! json_encode($completionData->pluck('target_days')) !!},
                    backgroundColor: '#22c55e'
                }, {
                    label: 'Realisasi (Hari)',
                    data: {!! json_encode($completionData->pluck('actual_days')) !!},
                    backgroundColor: '#ef4444'
                }]
            },
            options: {
                ...chartConfig,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#ffffff' }
                    },
                    x: {
                        ticks: { color: '#ffffff' }
                    }
                }
            }
        });
        @endif

        // Progress Pekerjaan Chart
        @if($progressData->isNotEmpty())
        new Chart(document.getElementById('progressChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($progressData->pluck('machine_name')) !!},
                datasets: [{
                    label: 'Progress (%)',
                    data: {!! json_encode($progressData->pluck('progress')) !!},
                    backgroundColor: function(context) {
                        const progress = context.raw;
                        if (progress >= 80) return '#22c55e';      // Hijau
                        if (progress >= 50) return '#eab308';      // Kuning
                        return '#ef4444';                          // Merah
                    }
                }]
            },
            options: {
                ...chartConfig,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { color: '#ffffff' }
                    },
                    x: {
                        ticks: { color: '#ffffff' }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const data = $progressData[context.dataIndex];
                                return [
                                    `Progress: ${context.raw}%`,
                                    `Sisa Waktu: ${data.remaining_days} hari`
                                ];
                            }
                        }
                    }
                }
            }
        });
        @endif

        // Tren Penyelesaian Chart
        @if($trendData->isNotEmpty())
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($trendData->keys()) !!},
                datasets: [{
                    label: 'Tepat Waktu',
                    data: {!! json_encode($trendData->pluck('on_time')) !!},
                    borderColor: '#22c55e',
                    backgroundColor: '#22c55e',
                    fill: false
                }, {
                    label: 'Terlambat',
                    data: {!! json_encode($trendData->pluck('delayed')) !!},
                    borderColor: '#ef4444',
                    backgroundColor: '#ef4444',
                    fill: false
                }]
            },
            options: {
                ...chartConfig,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#ffffff' }
                    },
                    x: {
                        ticks: { color: '#ffffff' }
                    }
                }
            }
        });
        @endif
    </script>
    @push('scripts')
    @endpush
    <style>
        :root {
            --color-black: #000000;
            --color-dark-teal: #007991;
            --color-teal: #00BFB3;
            --color-white: #FFFFFF;
            --color-blue: #0066FF;
            --color-light-gray: #F5F5F5;
        }

        /* Styling untuk card */
        .dashboard-card {
            background-color: var(--color-dark-teal);
            color: var(--color-white);
            position: relative;
            overflow: hidden;
        }

        .dashboard-card canvas {
            max-width: 100%;
            max-height: 100%;
        }

        /* Styling untuk progress bars */
        .progress-bar-primary {
            background-color: var(--color-teal);
        }

        .progress-bar-secondary {
            background-color: var(--color-blue);
        }

        /* Status badges */
        .status-normal {
            background-color: var(--color-teal);
        }

        .status-maintenance {
            background-color: var(--color-blue);
        }

        .status-breakdown {
            background-color: var(--color-dark-teal);
        }

        /* Styling untuk scrollbar */
        .overflow-y-auto::-webkit-scrollbar {
            width: 8px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Memastikan container chart memiliki posisi relatif */
        .relative {
            position: relative;
        }
        
        /* Memastikan canvas chart mengisi container dengan benar */
        canvas {
            width: 100% !important;
            height: 100% !important;
        }

        /* Mencegah chart overflow */
        .h-[300px] {
            height: 300px;
            min-height: 300px;
            max-height: 300px;
        }

        .h-[200px] {
            height: 200px;
            min-height: 200px;
            max-height: 200px;
        }
    </style>
</body>
</html>
