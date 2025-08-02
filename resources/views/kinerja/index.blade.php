@extends('layouts.app')

@section('styles')
    <style>
        .chart-card {
            background: linear-gradient(to bottom, #f9fafb, #fff);
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 1rem;
            text-align: center;
        }
        .no-data {
            color: #a0aec0;
            text-align: center;
            padding: 1rem 0;
        }
    </style>
@endsection

@section('content')
@include('components.navbar')

<div class="container mx-auto px-4 py-8 mt-24">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Kinerja Pemeliharaan</h1>

    <div class="chart-card">
        <div class="chart-title">Distribusi WO Closed per Unit (PM & CM)</div>
        <div class="grid md:grid-cols-2 gap-6">
            <!-- PM Chart -->
            <div class="text-center">
                <h3 class="text-md font-semibold text-blue-700 mb-2">Preventive Maintenance (PM)</h3>
                <canvas id="pmUnitChart" height="180"></canvas>
            </div>
            <!-- CM Chart -->
            <div class="text-center">
                <h3 class="text-md font-semibold text-pink-700 mb-2">Corrective Maintenance (CM)</h3>
                <canvas id="cmUnitChart" height="180"></canvas>
            </div>
        </div>
        <div class="chart-summary mt-4 text-center text-sm text-gray-600">
            Data ditampilkan berdasarkan unit kerja dengan status Work Order: <strong>Closed</strong>
        </div>
    </div>

    <div class="mt-12 border-2 border-gray-200 rounded-lg p-4 shadow-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Detail Work Order Closed</h2>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Tabel PM -->
            <div>
                <h3 class="text-lg font-semibold text-blue-700 mb-2">PM Closed</h3>
                @foreach($pmByUnit as $unit => $data)
                    <h4 class="font-medium text-gray-600 mt-2">{{ $unit }}</h4>
                    @if(count($data))
                        <ul class="list-disc ml-6 text-sm text-gray-700">
                            @foreach($data as $wo)
                                <li>ID: {{ $wo->id }} - {{ $wo->description }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-400 ml-6">Tidak ada data</p>
                    @endif
                @endforeach
            </div>

            <!-- Tabel CM -->
            <div>
                <h3 class="text-lg font-semibold text-pink-700 mb-2">CM Closed</h3>
                @foreach($cmByUnit as $unit => $data)
                    <h4 class="font-medium text-gray-600 mt-2">{{ $unit }}</h4>
                    @if(count($data))
                        <ul class="list-disc ml-6 text-sm text-gray-700">
                            @foreach($data as $wo)
                                <li>ID: {{ $wo->id }} - {{ $wo->description }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-400 ml-6">Tidak ada data</p>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const unitLabels = {!! json_encode($unitNames) !!};
        const pmData = {!! json_encode($pmPerUnit) !!};
        const cmData = {!! json_encode($cmPerUnit) !!};

        const pmColors = [
            'rgba(59, 130, 246, 0.7)',
            'rgba(96, 165, 250, 0.7)',
            'rgba(147, 197, 253, 0.7)',
            'rgba(191, 219, 254, 0.7)'
        ];
        const cmColors = [
            'rgba(249, 115, 115, 0.7)',
            'rgba(252, 165, 165, 0.7)',
            'rgba(254, 202, 202, 0.7)',
            'rgba(254, 226, 226, 0.7)'
        ];

        new Chart(document.getElementById('pmUnitChart').getContext('2d'), {
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
                plugins: {
                    legend: {
                        position: 'bottom'
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

        new Chart(document.getElementById('cmUnitChart').getContext('2d'), {
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
                plugins: {
                    legend: {
                        position: 'bottom'
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
