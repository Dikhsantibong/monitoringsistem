@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

    <style>
        .dashboard-flex-row {
            display: flex;
            gap: 2.5rem;
            justify-content: center;
            align-items: flex-start;
            flex-wrap: nowrap;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            max-width: 98vw;
        }
        .chart-detail-card,
        .detail-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 1.2rem 1.2rem 1rem 1.2rem;
            min-width: 0;
            width: 48%;
            max-width: 900px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        .chart-detail-card {
            align-items: center;
        }
        .detail-card {
            align-items: stretch;
        }
        .chart-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 0.7rem;
            text-align: center;
        }
        .mini-charts-row {
            display: flex;
            gap: 1.2rem;
            justify-content: center;
            margin-bottom: 1.2rem;
            width: 100%;
        }
        .mini-chart-card {
            background: linear-gradient(to bottom, #f9fafb, #fff);
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 0.7rem 0.7rem 0.5rem 0.7rem;
            width: 160px;
            min-width: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .mini-chart-title {
            font-size: 0.98rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        .chart-summary {
            margin-top: 0.5rem;
            text-align: center;
            font-size: 0.85rem;
            color: #6b7280;
        }
        .detail-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.7rem;
            text-align: center;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
        }
        .container {
            max-width: 100vw !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        @media (max-width: 1400px) {
            .dashboard-flex-row {
                gap: 1.2rem;
            }
            .chart-detail-card, .detail-card {
                width: 49%;
                max-width: none;
            }
        }
        @media (max-width: 1024px) {
            .dashboard-flex-row {
                flex-direction: column;
                align-items: stretch;
                gap: 1.5rem;
            }
            .chart-detail-card, .detail-card {
                width: 100%;
                min-width: 0;
                max-width: 100%;
            }
            .mini-charts-row {
                flex-direction: column;
                gap: 0.7rem;
            }
        }
        @media (max-width: 600px) {
            .mini-chart-card {
                width: 100%;
                max-width: 100%;
            }
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
@include('components.navbar')

<div class="container mx-auto py-10 mt-24" style="max-width:100vw;">
    <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Dashboard Kinerja Pemeliharaan</h1>

    <div class="dashboard-flex-row">
        <!-- Card Grafik Gabungan -->
        <div class="chart-detail-card">
            <div class="chart-title">Distribusi WO Closed per Unit (PM &amp; CM)</div>
            <div class="mini-charts-row">
                <!-- PM Chart -->
                <div class="mini-chart-card">
                    <div class="mini-chart-title text-blue-700">Preventive Maintenance (PM)</div>
                    <canvas id="pmUnitChart" width="120" height="120" style="max-width:120px;max-height:120px;"></canvas>
                </div>
                <!-- CM Chart -->
                <div class="mini-chart-card">
                    <div class="mini-chart-title text-pink-700">Corrective Maintenance (CM)</div>
                    <canvas id="cmUnitChart" width="120" height="120" style="max-width:120px;max-height:120px;"></canvas>
                </div>
            </div>
            <div class="chart-summary">
                Data ditampilkan berdasarkan unit kerja dengan status Work Order: <strong>Closed</strong>
            </div>
        </div>

        <!-- Card Detail -->
        <div class="detail-card">
            <div class="detail-title">Detail Work Order Closed</div>
            <div class="detail-grid">
                <!-- Tabel PM -->
                <div>
                    <h3 class="text-base font-semibold text-blue-700 mb-1">PM Closed</h3>
                    @foreach($pmByUnit as $unit => $data)
                        <h4 class="font-medium text-gray-600 mt-1 text-sm">{{ $unit }}</h4>
                        @if(count($data))
                            <ul class="list-disc ml-5 text-xs text-gray-700">
                                @foreach($data as $wo)
                                    <li>NO WO: {{ $wo->id }} - {{ $wo->description }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-gray-400 ml-5">Tidak ada data</p>
                        @endif
                    @endforeach
                </div>

                <!-- Tabel CM -->
                <div>
                    <h3 class="text-base font-semibold text-pink-700 mb-1">CM Closed</h3>
                    @foreach($cmByUnit as $unit => $data)
                        <h4 class="font-medium text-gray-600 mt-1 text-sm">{{ $unit }}</h4>
                        @if(count($data))
                            <ul class="list-disc ml-5 text-xs text-gray-700">
                                @foreach($data as $wo)
                                    <li>NO WO: {{ $wo->id }} - {{ $wo->description }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-gray-400 ml-5">Tidak ada data</p>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data dari backend
        var unitLabels = {!! json_encode($unitNames) !!};
        var pmData = {!! json_encode($pmPerUnit) !!};
        var cmData = {!! json_encode($cmPerUnit) !!};

        var pmColors = [
            'rgba(59, 130, 246, 0.7)',
            'rgba(96, 165, 250, 0.7)',
            'rgba(147, 197, 253, 0.7)',
            'rgba(191, 219, 254, 0.7)'
        ];
        var cmColors = [
            'rgba(249, 115, 115, 0.7)',
            'rgba(252, 165, 165, 0.7)',
            'rgba(254, 202, 202, 0.7)',
            'rgba(254, 226, 226, 0.7)'
        ];

        // PM Chart
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
                    legend: {
                        display: false
                    },
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

        // CM Chart
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
                    legend: {
                        display: false
                    },
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