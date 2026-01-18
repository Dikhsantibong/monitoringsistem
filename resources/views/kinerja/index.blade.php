@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <style>
        body {
            background: #f4f6fa;
        }
        .dashboard-bg {
            background: #f4f6fa;
            min-height: 100vh;
            padding-bottom: 2rem;
        }
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .summary-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
        }
        .summary-card.total { border-left-color: #3b82f6; }
        .summary-card.pm { border-left-color: #2563eb; }
        .summary-card.cm { border-left-color: #ef4444; }
        .summary-card.ratio { border-left-color: #10b981; }
        .summary-card-label {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }
        .summary-card-value {
            font-size: 2rem;
            font-weight: bold;
            color: #1e293b;
        }
        .chart-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .chart-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .chart-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
            text-align: center;
        }
        .data-table {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }
        .data-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th {
            background: #f8fafc;
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
        }
        .data-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .data-table tr:hover {
            background: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .badge-pm {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-cm {
            background: #fee2e2;
            color: #991b1b;
        }
        @media (max-width: 768px) {
            .chart-section {
                grid-template-columns: 1fr;
            }
            .summary-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endsection

@section('content')
@include('components.navbar')
<div class="dashboard-bg">
    <div class="container mx-auto py-6 mt-10 px-4" style="max-width:100vw;">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Kinerja Pemeliharaan</h1>
        
        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card total">
                <div class="summary-card-label">Total WO Closed</div>
                <div class="summary-card-value">{{ $pmCount + $cmCount }}</div>
            </div>
            <div class="summary-card pm">
                <div class="summary-card-label">Total WO PM Closed</div>
                <div class="summary-card-value">{{ $pmCount }}</div>
            </div>
            <div class="summary-card cm">
                <div class="summary-card-label">Total WO CM Closed</div>
                <div class="summary-card-value">{{ $cmCount }}</div>
            </div>
            <div class="summary-card ratio">
                <div class="summary-card-label">Rasio PM/CM</div>
                <div class="summary-card-value">
                    @if($cmCount > 0)
                        {{ number_format($pmCount / $cmCount, 2) }}
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="chart-section">
            <!-- Grafik Donut Distribusi PM vs CM -->
            <div class="chart-card">
                <div class="chart-title">Distribusi PM vs CM</div>
                <canvas id="pmcmDonutChart" height="250"></canvas>
            </div>
            
            <!-- Grafik Bar PM & CM per Unit -->
            <div class="chart-card">
                <div class="chart-title">Distribusi per Unit Layanan</div>
                <canvas id="unitBarChart" height="250"></canvas>
            </div>
        </div>

        <!-- Data Table -->
        <div class="data-table">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Detail Kinerja per Unit</h2>
            <table>
                <thead>
                    <tr>
                        <th>Unit Layanan</th>
                        <th class="text-center">PM Closed</th>
                        <th class="text-center">CM Closed</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">% PM</th>
                        <th class="text-center">% CM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unitNames as $i => $unit)
                    @php
                        $pmCountUnit = $pmPerUnit[$i] ?? 0;
                        $cmCountUnit = $cmPerUnit[$i] ?? 0;
                        $totalUnit = $pmCountUnit + $cmCountUnit;
                        $pmPercent = $totalUnit > 0 ? round(($pmCountUnit / $totalUnit) * 100, 1) : 0;
                        $cmPercent = $totalUnit > 0 ? round(($cmCountUnit / $totalUnit) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="font-medium">{{ $unit }}</td>
                        <td class="text-center">
                            <span class="badge badge-pm">{{ $pmCountUnit }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-cm">{{ $cmCountUnit }}</span>
                        </td>
                        <td class="text-center font-semibold">{{ $totalUnit }}</td>
                        <td class="text-center">{{ $pmPercent }}%</td>
                        <td class="text-center">{{ $cmPercent }}%</td>
                    </tr>
                    @endforeach
                    <tr class="bg-gray-50 font-semibold">
                        <td>Total</td>
                        <td class="text-center">{{ $pmCount }}</td>
                        <td class="text-center">{{ $cmCount }}</td>
                        <td class="text-center">{{ $pmCount + $cmCount }}</td>
                        <td class="text-center">
                            @if(($pmCount + $cmCount) > 0)
                                {{ number_format(100 * $pmCount / ($pmCount + $cmCount), 1) }}%
                            @else
                                0%
                            @endif
                        </td>
                        <td class="text-center">
                            @if(($pmCount + $cmCount) > 0)
                                {{ number_format(100 * $cmCount / ($pmCount + $cmCount), 1) }}%
                            @else
                                0%
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var unitLabels = @json($unitNames);
        var pmData = @json($pmPerUnit);
        var cmData = @json($cmPerUnit);
        var pmCount = {{ $pmCount }};
        var cmCount = {{ $cmCount }};
        
        // Donut PM vs CM
        var pmcmDonutCtx = document.getElementById('pmcmDonutChart').getContext('2d');
        new Chart(pmcmDonutCtx, {
            type: 'doughnut',
            data: {
                labels: ['PM', 'CM'],
                datasets: [{
                    data: [pmCount, cmCount],
                    backgroundColor: [
                        'rgba(37, 99, 235, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: '#fff',
                    borderWidth: 3
                }]
            },
            options: {
                cutout: '60%',
                plugins: {
                    legend: { 
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + context.raw + ' WO (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        
        // Bar Chart PM & CM per Unit
        var unitBarCtx = document.getElementById('unitBarChart').getContext('2d');
        new Chart(unitBarCtx, {
            type: 'bar',
            data: {
                labels: unitLabels,
                datasets: [
                    {
                        label: 'PM Closed',
                        data: pmData,
                        backgroundColor: 'rgba(37, 99, 235, 0.8)',
                        borderColor: 'rgba(37, 99, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'CM Closed',
                        data: cmData,
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw + ' WO';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
