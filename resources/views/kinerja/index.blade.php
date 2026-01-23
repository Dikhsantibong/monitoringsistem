@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .dashboard-overlay {
            background: rgba(255, 255, 255, 0.95);
            min-height: 100vh;
            padding-bottom: 2rem;
        }
        
        .dashboard-container {
            max-width: 1600px;
            margin: 0 auto;
        }
        
        /* Header Stats */
        .header-stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            color: white;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
        }
        
        .stat-card.blue {
            --gradient-start: #4facfe;
            --gradient-end: #00f2fe;
        }
        
        .stat-card.purple {
            --gradient-start: #667eea;
            --gradient-end: #764ba2;
        }
        
        .stat-card.red {
            --gradient-start: #f093fb;
            --gradient-end: #f5576c;
        }
        
        .stat-card.green {
            --gradient-start: #4ade80;
            --gradient-end: #22c55e;
        }
        
        .stat-card.orange {
            --gradient-start: #fa709a;
            --gradient-end: #fee140;
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            opacity: 0.9;
        }
        
        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .stat-subtext {
            font-size: 0.75rem;
            opacity: 0.8;
        }
        
        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .chart-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .chart-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        .chart-large {
            grid-column: span 8;
        }
        
        .chart-medium {
            grid-column: span 6;
        }
        
        .chart-small {
            grid-column: span 4;
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
        }
        
        .chart-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .chart-title i {
            color: #667eea;
        }
        
        .chart-subtitle {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
        
        .chart-wrapper {
            position: relative;
            width: 100%;
            height: 320px;
        }
        
        .chart-wrapper.small {
            height: 240px;
        }
        
        /* Data Table */
        .data-table-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .table-wrapper {
            overflow-x: auto;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
        }
        
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .data-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .data-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: white;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .data-table th:first-child {
            border-top-left-radius: 0.5rem;
        }
        
        .data-table th:last-child {
            border-top-right-radius: 0.5rem;
        }
        
        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        
        .data-table tbody tr {
            transition: background-color 0.2s ease;
        }
        
        .data-table tbody tr:hover {
            background: #f8fafc;
        }
        
        .data-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .data-table tfoot tr {
            background: #f8fafc;
            font-weight: 700;
        }
        
        .data-table tfoot td {
            border-top: 2px solid #667eea;
            border-bottom: none;
        }
        
        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.375rem 0.875rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            min-width: 60px;
        }
        
        .badge-pm {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .badge-cm {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }
        
        .badge-total {
            background: linear-gradient(135deg, #4ade80, #22c55e);
            color: white;
        }
        
        /* Progress Bars */
        .progress-bar-container {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 9999px;
            overflow: hidden;
            margin-top: 0.5rem;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 9999px;
            transition: width 0.6s ease;
        }
        
        .progress-bar.pm {
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        
        .progress-bar.cm {
            background: linear-gradient(90deg, #f093fb, #f5576c);
        }
        
        /* Best Performance Badge */
        .best-unit-badge {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Responsive */
        @media (max-width: 1400px) {
            .header-stats {
                grid-template-columns: repeat(3, 1fr);
            }
            .chart-large, .chart-medium, .chart-small {
                grid-column: span 12;
            }
        }
        
        @media (max-width: 768px) {
            .header-stats {
                grid-template-columns: 1fr;
            }
            .stat-card {
                padding: 1.25rem;
            }
            .stat-value {
                font-size: 1.75rem;
            }
            .chart-wrapper {
                height: 280px;
            }
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeInUp 0.6s ease forwards;
        }
    </style>
@endsection

@section('content')
@include('components.navbar')

<div class="dashboard-overlay">
    <div class="dashboard-container py-8 mt-10 px-4">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                <i class="fas fa-chart-line mr-3 text-purple-600"></i>
                Dashboard Kinerja Pemeliharaan
            </h1>
            <p class="text-gray-600">Monitoring dan analisis work order pemeliharaan secara real-time</p>
        </div>
        
        <!-- Header Stats -->
        <div class="header-stats animate-fade-in">
            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-label">Total Work Order</div>
                <div class="stat-value">{{ number_format($totalWO) }}</div>
                <div class="stat-subtext">WO Completed & Closed</div>
            </div>
            
            <div class="stat-card purple">
                <div class="stat-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="stat-label">PM Closed</div>
                <div class="stat-value">{{ number_format($pmCount) }}</div>
                <div class="stat-subtext">{{ $pmPercentage }}% dari total WO</div>
            </div>
            
            <div class="stat-card red">
                <div class="stat-icon">
                    <i class="fas fa-wrench"></i>
                </div>
                <div class="stat-label">CM Closed</div>
                <div class="stat-value">{{ number_format($cmCount) }}</div>
                <div class="stat-subtext">{{ $cmPercentage }}% dari total WO</div>
            </div>
            
            <div class="stat-card green">
                <div class="stat-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stat-label">Rasio PM/CM</div>
                <div class="stat-value">{{ $pmCmRatio }}</div>
                <div class="stat-subtext">
                    @if($pmCmRatio >= 3)
                        <i class="fas fa-check-circle mr-1"></i>Target Tercapai
                    @else
                        <i class="fas fa-exclamation-circle mr-1"></i>Perlu Ditingkatkan
                    @endif
                </div>
            </div>
            
            <div class="stat-card orange">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-label">Best Performance</div>
                <div class="stat-value" style="font-size: 1rem;">{{ $bestPerformingUnit ?: 'N/A' }}</div>
                <div class="stat-subtext">
                    @if($maxRatio > 0)
                        Rasio: {{ number_format($maxRatio, 2) }}
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Charts Grid -->
        <div class="charts-grid">
            <!-- Trend Line Chart -->
            <div class="chart-card chart-large">
                <div class="chart-header">
                    <div>
                        <div class="chart-title">
                            <i class="fas fa-chart-area"></i>
                            Tren Pemeliharaan 6 Bulan Terakhir
                        </div>
                        <div class="chart-subtitle">Perbandingan PM dan CM per bulan</div>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
            
            <!-- Donut Chart -->
            <div class="chart-card chart-small">
                <div class="chart-header">
                    <div>
                        <div class="chart-title">
                            <i class="fas fa-chart-pie"></i>
                            Distribusi PM vs CM
                        </div>
                        <div class="chart-subtitle">Total WO: {{ $totalWO }}</div>
                    </div>
                </div>
                <div class="chart-wrapper small">
                    <canvas id="donutChart"></canvas>
                </div>
            </div>
            
            <!-- Horizontal Bar Chart -->
            <div class="chart-card chart-medium">
                <div class="chart-header">
                    <div>
                        <div class="chart-title">
                            <i class="fas fa-building"></i>
                            Performa per Unit Layanan
                        </div>
                        <div class="chart-subtitle">Total WO per unit</div>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="horizontalBarChart"></canvas>
                </div>
            </div>
            
            <!-- Stacked Bar Chart -->
            <div class="chart-card chart-medium">
                <div class="chart-header">
                    <div>
                        <div class="chart-title">
                            <i class="fas fa-layer-group"></i>
                            Komposisi PM & CM per Unit
                        </div>
                        <div class="chart-subtitle">Breakdown detail</div>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="stackedBarChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Data Table -->
        <div class="data-table-card">
            <div class="chart-header">
                <div>
                    <div class="chart-title">
                        <i class="fas fa-table"></i>
                        Detail Kinerja per Unit Layanan
                    </div>
                    <div class="chart-subtitle">Ringkasan lengkap dengan persentase dan progress</div>
                </div>
            </div>
            
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Unit Layanan</th>
                            <th class="text-center">PM Closed</th>
                            <th class="text-center">CM Closed</th>
                            <th class="text-center">Total WO</th>
                            <th class="text-center">PM/CM Ratio</th>
                            <th style="min-width: 200px;">Progress PM</th>
                            <th style="min-width: 200px;">Progress CM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unitNames as $i => $unit)
                        @php
                            $pmCountUnit = $pmPerUnit[$i] ?? 0;
                            $cmCountUnit = $cmPerUnit[$i] ?? 0;
                            $totalUnit = $totalPerUnit[$i] ?? 0;
                            $pmPercent = $totalUnit > 0 ? round(($pmCountUnit / $totalUnit) * 100, 1) : 0;
                            $cmPercent = $totalUnit > 0 ? round(($cmCountUnit / $totalUnit) * 100, 1) : 0;
                            $ratio = $cmCountUnit > 0 ? round($pmCountUnit / $cmCountUnit, 2) : 0;
                            $isBest = $unit === $bestPerformingUnit;
                        @endphp
                        <tr>
                            <td class="font-semibold">
                                {{ $unit }}
                                @if($isBest)
                                    <span class="best-unit-badge ml-2">
                                        <i class="fas fa-crown"></i>
                                        Best
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-pm">{{ $pmCountUnit }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-cm">{{ $cmCountUnit }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-total">{{ $totalUnit }}</span>
                            </td>
                            <td class="text-center font-bold text-lg">
                                {{ $ratio }}
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium w-12">{{ $pmPercent }}%</span>
                                    <div class="progress-bar-container flex-1">
                                        <div class="progress-bar pm" style="width: {{ $pmPercent }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium w-12">{{ $cmPercent }}%</span>
                                    <div class="progress-bar-container flex-1">
                                        <div class="progress-bar cm" style="width: {{ $cmPercent }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="font-bold">TOTAL</td>
                            <td class="text-center">
                                <span class="badge badge-pm">{{ $pmCount }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-cm">{{ $cmCount }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-total">{{ $totalWO }}</span>
                            </td>
                            <td class="text-center font-bold text-lg">
                                {{ $pmCmRatio }}
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold w-12">{{ $pmPercentage }}%</span>
                                    <div class="progress-bar-container flex-1">
                                        <div class="progress-bar pm" style="width: {{ $pmPercentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold w-12">{{ $cmPercentage }}%</span>
                                    <div class="progress-bar-container flex-1">
                                        <div class="progress-bar cm" style="width: {{ $cmPercentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const unitLabels = @json($unitNames);
    const pmData = @json($pmPerUnit);
    const cmData = @json($cmPerUnit);
    const totalData = @json($totalPerUnit);
    const pmCount = {{ $pmCount }};
    const cmCount = {{ $cmCount }};
    const monthlyTrend = @json($monthlyTrend);
    
    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    font: { size: 12, family: 'Inter, sans-serif' },
                    padding: 15
                }
            }
        }
    };
    
    // Trend Line Chart
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: monthlyTrend.map(m => m.label),
            datasets: [
                {
                    label: 'PM Closed',
                    data: monthlyTrend.map(m => m.pm),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7
                },
                {
                    label: 'CM Closed',
                    data: monthlyTrend.map(m => m.cm),
                    borderColor: '#f5576c',
                    backgroundColor: 'rgba(245, 87, 108, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }
            ]
        },
        options: {
            ...chartDefaults,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { font: { size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
    
    // Donut Chart
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            labels: ['PM Closed', 'CM Closed'],
            datasets: [{
                data: [pmCount, cmCount],
                backgroundColor: [
                    'rgba(102, 126, 234, 0.9)',
                    'rgba(245, 87, 108, 0.9)'
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            ...chartDefaults,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return context.label + ': ' + context.raw + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
    
    // Horizontal Bar Chart
    new Chart(document.getElementById('horizontalBarChart'), {
        type: 'bar',
        data: {
            labels: unitLabels,
            datasets: [{
                label: 'Total WO',
                data: totalData,
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(74, 222, 128, 0.8)',
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(245, 87, 108, 0.8)'
                ],
                borderWidth: 0,
                borderRadius: 8
            }]
        },
        options: {
            ...chartDefaults,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                y: {
                    grid: { display: false }
                }
            }
        }
    });
    
    // Stacked Bar Chart
    new Chart(document.getElementById('stackedBarChart'), {
        type: 'bar',
        data: {
            labels: unitLabels,
            datasets: [
                {
                    label: 'PM Closed',
                    data: pmData,
                    backgroundColor: 'rgba(102, 126, 234, 0.9)',
                    borderRadius: 6
                },
                {
                    label: 'CM Closed',
                    data: cmData,
                    backgroundColor: 'rgba(245, 87, 108, 0.9)',
                    borderRadius: 6
                }
            ]
        },
        options: {
            ...chartDefaults,
            scales: {
                x: { 
                    stacked: true,
                    grid: { display: false }
                },
                y: { 
                    stacked: true,
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                }
            }
        }
    });
});
</script>
@endsection