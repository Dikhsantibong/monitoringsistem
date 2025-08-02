@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <style>
        /* Custom chart card style */
        .chart-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 2rem 1rem 1.5rem 1rem;
            margin-bottom: 2rem;
        }
        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #22223b;
            margin-bottom: 1rem;
            text-align: center;
        }
        .chart-summary {
            text-align: center;
            margin-top: 1rem;
            color: #4a5568;
            font-size: 1rem;
        }

        
    </style>
@endsection

@section('content')
@include('components.navbar')
<div class="container mx-auto px-4 py-8 mt-24">
    <h1 class="text-2xl font-bold mb-6">Kinerja Pemeliharaan</h1>

    <div class="chart-card">
        <div class="chart-title">Grafik Jumlah WO Closed: PM vs CM</div>
        <canvas id="pmcmChart" height="120"></canvas>
        <div class="chart-summary">
            <span class="font-semibold text-blue-700">PM Closed:</span> {{ $pmCount }} &nbsp;|&nbsp;
            <span class="font-semibold text-pink-700">CM Closed:</span> {{ $cmCount }}<br>
            <span class="text-sm text-gray-500">Data diambil dari Work Order yang sudah berstatus <b>Closed</b></span>
        </div>
    </div>

    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-2">PM (Preventive Maintenance) Closed</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded shadow">
                <thead>
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Deskripsi</th>
                        <th class="px-4 py-2">Jadwal Selesai</th>
                        <th class="px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pmClosed as $wo)
                        <tr>
                            <td class="border px-4 py-2">{{ $wo->id }}</td>
                            <td class="border px-4 py-2">{{ $wo->description }}</td>
                            <td class="border px-4 py-2">{{ $wo->schedule_finish }}</td>
                            <td class="border px-4 py-2">{{ $wo->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-400 py-4">Tidak ada data PM Closed</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h2 class="text-xl font-semibold mb-2">CM (Corrective Maintenance) Closed</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded shadow">
                <thead>
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Deskripsi</th>
                        <th class="px-4 py-2">Jadwal Selesai</th>
                        <th class="px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cmClosed as $wo)
                        <tr>
                            <td class="border px-4 py-2">{{ $wo->id }}</td>
                            <td class="border px-4 py-2">{{ $wo->description }}</td>
                            <td class="border px-4 py-2">{{ $wo->schedule_finish }}</td>
                            <td class="border px-4 py-2">{{ $wo->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-400 py-4">Tidak ada data CM Closed</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('pmcmChart').getContext('2d');
    const pmcmChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['PM Closed', 'CM Closed'],
            datasets: [{
                label: 'Jumlah WO Closed',
                data: [{{ $pmCount }}, {{ $cmCount }}],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.85)', // PM
                    'rgba(255, 99, 132, 0.85)'  // CM
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 2,
                borderRadius: 8,
                maxBarThickness: 60
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            return 'Jumlah: ' + context.parsed.y;
                        }
                    }
                },
                title: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#e2e8f0',
                        borderDash: [4, 4]
                    },
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 14
                        }
                    }
                }
            }
        }
    });
</script>
@endsection