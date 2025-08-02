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
        .dashboard-flex-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.2rem;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            max-width: 98vw;
            align-items: stretch;
        }
        .chart-card {
            background: linear-gradient(135deg, #e0e7ff 0%, #f1f5f9 100%);
            border: 1px solid #dbeafe;
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(59,130,246,0.07);
            padding: 1.2rem 1.2rem 1rem 1.2rem;
            min-width: 0;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 0;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            width: 100%;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .stat-card {
            background: linear-gradient(135deg, #f0fdf4 0%, #f9fafb 100%);
            border: 1px solid #bbf7d0;
            border-radius: 0.75rem;
            box-shadow: 0 1px 4px rgba(16,185,129,0.07);
            min-width: 0;
            width: 100%;
            align-items: center;
            justify-content: center;
            display: flex;
            flex-direction: column;
            padding: 0.7rem 0.5rem 0.5rem 0.5rem;
            min-height: 90px;
        }
        .stat-title {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 0.2rem;
            text-align: center;
        }
        .stat-value {
            font-size: 1.7rem;
            font-weight: bold;
            color: #2563eb;
            text-align: center;
        }
        .stat-card:nth-child(2n) .stat-value { color: #f59e42; }
        .stat-card:nth-child(3n) .stat-value { color: #10b981; }
        .stat-card:nth-child(4n) .stat-value { color: #ef4444; }
        .stat-card:nth-child(5n) .stat-value { color: #eab308; }
        .stat-card:nth-child(6n) .stat-value { color: #6366f1; }
        .stat-card:nth-child(7n) .stat-value { color: #0ea5e9; }
        .stat-card:nth-child(8n) .stat-value { color: #f472b6; }
        .chart-title {
            font-size: 1.08rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.7rem;
            text-align: center;
        }
        @media (max-width: 1200px) {
            .dashboard-flex-row {
                gap: 0.7rem;
            }
            .chart-card {
                width: 98vw;
                min-width: 0;
            }
        }
        @media (max-width: 900px) {
            .dashboard-flex-row {
                flex-direction: column;
                gap: 0.7rem;
            }
            .chart-card {
                width: 100%;
                min-width: 0;
            }
        }
        @media (max-width: 600px) {
            .stat-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
@include('components.navbar')
<div class="dashboard-bg">
    <div class="container mx-auto py-10 mt-10" style="max-width:100vw;">
        <div class="dashboard-flex-row">
            <!-- Grafik Donut Distribusi PM vs CM -->
            <div class="chart-card">
                <div class="chart-title font-semibold mb-2">Distribusi Total WO Closed (PM vs CM)</div>
                <canvas id="pmcmDonutChart" width="220" height="220" style="max-width:220px;max-height:220px;"></canvas>
            </div>
            <!-- Grafik Line Trend WO Closed (dummy data) -->
            <div class="chart-card">
                <div class="chart-title font-semibold mb-2">Trend WO Closed (Bulan Ini)</div>
                <canvas id="woTrendChart" width="320" height="220" style="max-width:320px;max-height:220px;"></canvas>
            </div>
            <!-- Grafik Donut PM per Unit -->
            <div class="chart-card">
                <div class="chart-title font-semibold mb-2">Distribusi WO Closed per Unit (PM)</div>
                <canvas id="pmUnitChart" width="220" height="220" style="max-width:220px;max-height:220px;"></canvas>
            </div>
            <!-- Grafik Donut CM per Unit -->
            <div class="chart-card">
                <div class="chart-title font-semibold mb-2">Distribusi WO Closed per Unit (CM)</div>
                <canvas id="cmUnitChart" width="220" height="220" style="max-width:220px;max-height:220px;"></canvas>
            </div>
        </div>
        <div class="stat-grid mt-8">
            <div class="stat-card">
                <div class="stat-title">Total WO Closed</div>
                <div class="stat-value">{{ $pmCount + $cmCount }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Total WO PM Closed</div>
                <div class="stat-value">{{ $pmCount }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Total WO CM Closed</div>
                <div class="stat-value">{{ $cmCount }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Total Unit</div>
                <div class="stat-value">{{ count($unitNames) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Rasio PM/CM</div>
                <div class="stat-value">
                    @if($cmCount > 0)
                        {{ number_format($pmCount / $cmCount, 2) }}
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-title">% PM dari Total</div>
                <div class="stat-value">
                    @if(($pmCount + $cmCount) > 0)
                        {{ number_format(100 * $pmCount / ($pmCount + $cmCount), 1) }}%
                    @else
                        0%
                    @endif
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-title">% CM dari Total</div>
                <div class="stat-value">
                    @if(($pmCount + $cmCount) > 0)
                        {{ number_format(100 * $cmCount / ($pmCount + $cmCount), 1) }}%
                    @else
                        0%
                    @endif
                </div>
            </div>
            @foreach($unitNames as $i => $unit)
                <div class="stat-card">
                    <div class="stat-title">PM Closed - {{ $unit }}</div>
                    <div class="stat-value">{{ $pmPerUnit[$i] }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">CM Closed - {{ $unit }}</div>
                    <div class="stat-value">{{ $cmPerUnit[$i] }}</div>
                </div>
            @endforeach
            @php
                $statCardCount = 7 + (count($unitNames) * 2);
                $remainder = $statCardCount % 4;
            @endphp
            @if($remainder > 0)
                @for($i = 0; $i < 4 - $remainder; $i++)
                    <div class="stat-card" style="background:transparent;border:none;box-shadow:none;"></div>
                @endfor
            @endif
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
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(249, 115, 115, 0.7)'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                cutout: '65%',
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw + ' WO';
                            }
                        }
                    }
                }
            }
        });
        // Dummy trend data (bisa diganti dengan data asli jika ada)
        var trendLabels = ['1', '5', '10', '15', '20', '25', '30'];
        var trendData = [2, 5, 7, 4, 8, 6, 9];
        var woTrendCtx = document.getElementById('woTrendChart').getContext('2d');
        new Chart(woTrendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'WO Closed',
                    data: trendData,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { title: { display: true, text: 'Tanggal' } },
                    y: { title: { display: true, text: 'WO Closed' }, beginAtZero: true }
                }
            }
        });
        // PM per Unit
        var pmColors = [
            'rgba(59, 130, 246, 0.7)',
            'rgba(96, 165, 250, 0.7)',
            'rgba(147, 197, 253, 0.7)',
            'rgba(191, 219, 254, 0.7)'
        ];
        var pmCtx = document.getElementById('pmUnitChart').getContext('2d');
        new Chart(pmCtx, {
            type: 'doughnut',
            data: {
                labels: unitLabels,
                datasets: [{
                    label: 'PM',
                    data: pmData,
                    backgroundColor: pmColors,
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                cutout: '65%',
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw + ' WO';
                            }
                        }
                    }
                }
            }
        });
        // CM per Unit
        var cmColors = [
            'rgba(249, 115, 115, 0.7)',
            'rgba(252, 165, 165, 0.7)',
            'rgba(254, 202, 202, 0.7)',
            'rgba(254, 226, 226, 0.7)'
        ];
        var cmCtx = document.getElementById('cmUnitChart').getContext('2d');
        new Chart(cmCtx, {
            type: 'doughnut',
            data: {
                labels: unitLabels,
                datasets: [{
                    label: 'CM',
                    data: cmData,
                    backgroundColor: cmColors,
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                cutout: '65%',
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw + ' WO';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
