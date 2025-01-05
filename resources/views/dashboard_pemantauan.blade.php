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
            <div class="dashboard-card p-4 rounded-lg">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg">KESELURUHAN KONDISI MESIN</h2>
                </div>
                <div class="mt-4">
                    <div class="text-center">
                        <canvas id="oeeChart" class="w-32 h-32 mx-auto"></canvas>
                        <p class="mt-2">Presentasi Mesin Gangguan</p>
                    </div>
                </div>
            </div>
            <div class="col-span-2 grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="dashboard-card p-4 rounded-lg col-span-full">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg">KONDISI PEMELIHARAAN MESIN</h2>
                    </div>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div class="text-center">
                            <canvas id="availabilityChart" class="w-32 h-32 mx-auto"></canvas>
                            <p class="mt-2">Mothballed</p>
                        </div>
                        <div class="text-center">
                            <canvas id="performanceChart" class="w-32 h-32 mx-auto"></canvas>
                            <p class="mt-2">Pemeliharaan</p>
                        </div>
                        <div class="text-center">
                            <canvas id="qualityChart" class="w-32 h-32 mx-auto"></canvas>
                            <p class="mt-2">Overhaul </p>
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($machineData as $index => $machine)
                                <tr class="hover:bg-blue-800">
                                    <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">{{ $machine['type'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">{{ $machine['unit_name'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap border border-gray-200 text-white">{{ $machine['serial_number'] ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center whitespace-nowrap border border-gray-200 text-white">
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
            <!-- Availability Card -->
            <div class="dashboard-card p-4 rounded-lg">
                <h3 class="text-lg mb-4">Availability</h3>
                <div class="h-48"> <!-- Tambahkan container dengan tinggi tetap -->
                    <canvas id="availabilityBarChart"></canvas>
                </div>
            </div>

            <!-- Performance Card -->
            <div class="dashboard-card p-4 rounded-lg">
                <h3 class="text-lg mb-4">Performance</h3>
                <div class="h-48"> <!-- Tambahkan container dengan tinggi tetap -->
                    <canvas id="performanceBarChart"></canvas>
                </div>
            </div>

            <!-- Quality Card -->
            <div class="dashboard-card p-4 rounded-lg">
                <h3 class="text-lg mb-4">Quality</h3>
                <div class="h-48"> <!-- Tambahkan container dengan tinggi tetap -->
                    <canvas id="qualityBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script>
        // OEE Chart
        var ctxOEE = document.getElementById('oeeChart').getContext('2d');
        var oeeChart = new Chart(ctxOEE, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [90, 10],
                    backgroundColor: ['#22c55e', '#e5e7eb']
                }],
                labels: ['Aktif', 'Tidak Aktif']
            },
            options: {
                cutout: '70%',
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                var dataset = tooltipItem.dataset;
                                var total = dataset.data.reduce((a, b) => a + b, 0);
                                var currentValue = dataset.data[tooltipItem.dataIndex];
                                var percentage = Math.floor(((currentValue / total) * 100) + 0.5);
                                return currentValue + ' (' + percentage + '%)';
                            }
                        }
                    },
                    legend: { display: false }
                }
            }
        });

        // Availability Chart
        var ctxAvailability = document.getElementById('availabilityChart').getContext('2d');
        var availabilityChart = new Chart(ctxAvailability, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [50, 50],
                    backgroundColor: ['#0ea5e9', '#e5e7eb']
                }],
                labels: ['Tersedia', 'Tidak Tersedia']
            },
            options: {
                cutout: '70%',
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                var dataset = tooltipItem.dataset;
                                var total = dataset.data.reduce((a, b) => a + b, 0);
                                var currentValue = dataset.data[tooltipItem.dataIndex];
                                var percentage = Math.floor(((currentValue / total) * 100) + 0.5);
                                return currentValue + ' (' + percentage + '%)';
                            }
                        }
                    },
                    legend: { display: false }
                }
            }
        });

        // Performance Chart
        var ctxPerformance = document.getElementById('performanceChart').getContext('2d');
        var performanceChart = new Chart(ctxPerformance, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [50, 50],
                    backgroundColor: ['#ef4444', '#e5e7eb']
                }],
                labels: ['Kinerja Baik', 'Kinerja Buruk']
            },
            options: {
                cutout: '70%',
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                var dataset = tooltipItem.dataset;
                                var total = dataset.data.reduce((a, b) => a + b, 0);
                                var currentValue = dataset.data[tooltipItem.dataIndex];
                                var percentage = Math.floor(((currentValue / total) * 100) + 0.5);
                                return currentValue + ' (' + percentage + '%)';
                            }
                        }
                    },
                    legend: { display: false }
                }
            }
        });

        // Quality Chart
        var ctxQuality = document.getElementById('qualityChart').getContext('2d');
        var qualityChart = new Chart(ctxQuality, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [70, 30],
                    backgroundColor: ['#f59e0b', '#e5e7eb']
                }],
                labels: ['Kualitas Baik', 'Kualitas Buruk']
            },
            options: {
                cutout: '70%',
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                var dataset = tooltipItem.dataset;
                                var total = dataset.data.reduce((a, b) => a + b, 0);
                                var currentValue = dataset.data[tooltipItem.dataIndex];
                                var percentage = Math.floor(((currentValue / total) * 100) + 0.5);
                                return currentValue + ' (' + percentage + '%)';
                            }
                        }
                    },
                    legend: { display: false }
                }
            }
        });

        // Konfigurasi umum untuk semua chart
        const chartConfig = {
            responsive: true,
            maintainAspectRatio: false, // Penting: set false agar ukuran chart mengikuti container
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { 
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#FFFFFF'
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#FFFFFF'
                    }
                }
            }
        };

        // Availability Bar Chart
        new Chart(document.getElementById('availabilityBarChart'), {
            type: 'bar',
            data: {
                labels: ['Total Operative Mode Time', 'Run Time'],
                datasets: [{
                    data: [100, 75],
                    backgroundColor: ['#0ea5e9', '#0ea5e9']
                }]
            },
            options: {
                ...chartConfig,
                indexAxis: 'y'
            }
        });

        // Performance Bar Chart
        new Chart(document.getElementById('performanceBarChart'), {
            type: 'bar',
            data: {
                labels: ['Nominal Speed', 'Actual Speed'],
                datasets: [{
                    data: [100, 85],
                    backgroundColor: ['#ef4444', '#ef4444']
                }]
            },
            options: {
                ...chartConfig,
                indexAxis: 'y'
            }
        });

        // Quality Bar Chart
        new Chart(document.getElementById('qualityBarChart'), {
            type: 'bar',
            data: {
                labels: ['Product Output', 'Actual Good Product'],
                datasets: [{
                    data: [100, 90],
                    backgroundColor: ['#f59e0b', '#f59e0b']
                }]
            },
            options: {
                ...chartConfig,
                indexAxis: 'y'
            }
        });

        const chartColors = {
            primary: '#00BFB3',    // teal
            secondary: '#007991',  // dark teal
            accent: '#0066FF',    // blue
            background: '#FFFFFF' // white
        };

        // Update warna pada charts
        new Chart(document.getElementById('availabilityBarChart'), {
            type: 'bar',
            data: {
                labels: ['Total Operative Mode Time', 'Run Time'],
                datasets: [{
                    data: [100, 75],
                    backgroundColor: [chartColors.primary, chartColors.primary]
                }]
            },
            options: {
                ...chartConfig,
                indexAxis: 'y'
            }
        });

        new Chart(document.getElementById('performanceBarChart'), {
            type: 'bar',
            data: {
                labels: ['Nominal Speed', 'Actual Speed'],
                datasets: [{
                    data: [100, 85],
                    backgroundColor: [chartColors.secondary, chartColors.secondary]
                }]
            },
            options: {
                ...chartConfig,
                indexAxis: 'y'
            }
        });

        new Chart(document.getElementById('qualityBarChart'), {
            type: 'bar',
            data: {
                labels: ['Product Output', 'Actual Good Product'],
                datasets: [{
                    data: [100, 90],
                    backgroundColor: [chartColors.accent, chartColors.accent]
                }]
            },
            options: {
                ...chartConfig,
                indexAxis: 'y'
            }
        });

        // Update status badges
        document.querySelectorAll('.status-badge').forEach(badge => {
            const status = badge.dataset.status;
            if (status === 'normal') {
                badge.style.backgroundColor = chartColors.primary;
            } else if (status === 'maintenance') {
                badge.style.backgroundColor = chartColors.accent;
            } else {
                badge.style.backgroundColor = chartColors.secondary;
            }
        });
    </script>
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
    </style>
</body>
</html>