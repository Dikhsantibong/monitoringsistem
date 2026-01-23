@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            background: #f8f9fa;
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            color: #2c3e50;
            line-height: 1.6;
        }
        
        .dashboard-overlay {
            min-height: 100vh;
            padding-bottom: 2rem;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 0.375rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .page-title i {
            color: #1976d2;
            font-size: 1.5rem;
        }
        
        .page-subtitle {
            font-size: 0.875rem;
            color: #6c757d;
            font-weight: 400;
        }
        
        /* Stat Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #e1e4e8;
            border-radius: 6px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: box-shadow 0.2s ease;
        }
        
        .stat-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        
        .stat-content {
            flex: 1;
        }
        
        .stat-label {
            font-size: 0.8125rem;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 0.375rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .stat-value {
            font-size: 1.75rem;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 0.25rem;
            line-height: 1;
        }
        
        .stat-subtext {
            font-size: 0.75rem;
            color: #868e96;
        }
        
        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .stat-icon-wrapper i {
            font-size: 1.25rem;
        }
        
        .stat-card.blue .stat-icon-wrapper {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .stat-card.purple .stat-icon-wrapper {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        
        .stat-card.red .stat-icon-wrapper {
            background: #ffebee;
            color: #d32f2f;
        }
        
        .stat-card.green .stat-icon-wrapper {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .stat-card.orange .stat-icon-wrapper {
            background: #fff3e0;
            color: #f57c00;
        }
        
        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .chart-card {
            background: white;
            border: 1px solid #e1e4e8;
            border-radius: 6px;
            padding: 1.25rem;
            transition: box-shadow 0.2s ease;
        }
        
        .chart-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e1e4e8;
        }
        
        .chart-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1a202c;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
        }
        
        .chart-title i {
            color: #1976d2;
            font-size: 0.875rem;
        }
        
        .chart-subtitle {
            font-size: 0.8125rem;
            color: #6c757d;
            font-weight: 400;
        }
        
        .chart-wrapper {
            position: relative;
            width: 100%;
            height: 300px;
        }
        
        .chart-wrapper.small {
            height: 240px;
        }
        
        /* Data Table */
        .table-card {
            background: white;
            border: 1px solid #e1e4e8;
            border-radius: 6px;
            padding: 1.25rem;
        }
        
        .table-wrapper {
            overflow-x: auto;
            border-radius: 6px;
            border: 1px solid #e1e4e8;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        
        .data-table thead {
            background: #f8f9fa;
        }
        
        .data-table th {
            padding: 0.875rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #495057;
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 2px solid #e1e4e8;
        }
        
        .data-table td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #f1f3f5;
            color: #495057;
        }
        
        .data-table tbody tr {
            transition: background-color 0.15s ease;
        }
        
        .data-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .data-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .data-table tfoot tr {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .data-table tfoot td {
            border-top: 2px solid #e1e4e8;
            border-bottom: none;
            padding: 1rem;
        }
        
        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.8125rem;
            font-weight: 500;
            min-width: 50px;
        }
        
        .badge-pm {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-cm {
            background: #ffebee;
            color: #d32f2f;
        }
        
        .badge-total {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .best-badge {
            background: #fff3e0;
            color: #f57c00;
            padding: 0.25rem 0.625rem;
            border-radius: 4px;
            font-size: 0.6875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            margin-left: 0.5rem;
        }
        
        .best-badge i {
            font-size: 0.75rem;
        }
        
        /* Progress Bars */
        .progress-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .progress-label {
            font-size: 0.8125rem;
            font-weight: 500;
            color: #495057;
            min-width: 38px;
        }
        
        .progress-bar-container {
            flex: 1;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: 3px;
            transition: width 0.4s ease;
        }
        
        .progress-bar.pm {
            background: #1976d2;
        }
        
        .progress-bar.cm {
            background: #d32f2f;
        }
        
        /* Status Indicator */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-indicator.success {
            color: #388e3c;
        }
        
        .status-indicator.warning {
            color: #f57c00;
        }
        
        .status-indicator i {
            font-size: 0.875rem;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .chart-large, .chart-medium, .chart-small {
                grid-column: span 12;
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .stat-value {
                font-size: 1.5rem;
            }
            
            .data-table {
                font-size: 0.8125rem;
            }
            
            .data-table th,
            .data-table td {
                padding: 0.75rem 0.875rem;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.4s ease forwards;
        }

        /* Tabs Styling */
        .tabs-container {
            margin-bottom: 2rem;
        }

        .tabs-nav {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #e1e4e8;
            padding-bottom: 0;
        }

        .tab-button {
            background: transparent;
            border: none;
            padding: 0.875rem 1.5rem;
            font-size: 0.9375rem;
            font-weight: 500;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            bottom: -2px;
        }

        .tab-button i {
            font-size: 1rem;
        }

        .tab-button:hover {
            color: #1976d2;
            background: rgba(25, 118, 210, 0.05);
        }

        .tab-button.active {
            color: #1976d2;
            border-bottom-color: #1976d2;
            font-weight: 600;
        }

        .tabs-content {
            position: relative;
        }

        .tab-pane {
            display: none;
            animation: fadeIn 0.4s ease forwards;
        }

        .tab-pane.active {
            display: block;
        }

        /* Responsive tabs */
        @media (max-width: 768px) {
            .tabs-nav {
                flex-direction: column;
                gap: 0;
                border-bottom: none;
            }

            .tab-button {
                border-bottom: 1px solid #e1e4e8;
                border-left: 3px solid transparent;
                bottom: 0;
                padding: 1rem 1.25rem;
            }

            .tab-button.active {
                border-bottom-color: #e1e4e8;
                border-left-color: #1976d2;
            }
        }
    </style>
@endsection

@section('content')
@include('components.navbar')

<div class="dashboard-overlay">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-chart-line"></i>
            Dashboard Kinerja Pemeliharaan
        </h1>
        <p class="page-subtitle">Monitoring dan analisis work order pemeliharaan secara real-time</p>
    </div>

    <!-- Tabs Navigation -->
    <div class="tabs-container">
        <div class="tabs-nav">
            <button class="tab-button active" data-tab="kinerja-tab">
                <i class="fas fa-chart-bar"></i>
                Kinerja Dashboard
            </button>
            <button class="tab-button" data-tab="kpi-tab">
                <i class="fas fa-tachometer-alt"></i>
                KPI Dashboard
            </button>
        </div>

        <!-- Tab Content -->
        <div class="tabs-content">
            <!-- Kinerja Dashboard Tab -->
            <div id="kinerja-tab" class="tab-pane active">
                @include('kinerja.kinerja-dashboard')
            </div>

            <!-- KPI Dashboard Tab -->
            <div id="kpi-tab" class="tab-pane">
                @include('kinerja.kpi-dashboard')
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
                    font: { 
                        size: 11,
                        family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                        weight: '500'
                    },
                    padding: 12,
                    usePointStyle: true,
                    pointStyle: 'circle'
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
                    borderColor: '#1976d2',
                    backgroundColor: 'rgba(25, 118, 210, 0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#1976d2',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'CM Closed',
                    data: monthlyTrend.map(m => m.cm),
                    borderColor: '#d32f2f',
                    backgroundColor: 'rgba(211, 47, 47, 0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#d32f2f',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            ...chartDefaults,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { 
                        color: '#f1f3f5',
                        drawBorder: false
                    },
                    ticks: { 
                        font: { size: 11 },
                        color: '#6c757d',
                        padding: 8
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { 
                        font: { size: 11 },
                        color: '#6c757d',
                        padding: 8
                    }
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
                backgroundColor: ['#1976d2', '#d32f2f'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            ...chartDefaults,
            cutout: '70%',
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        font: { size: 11, weight: '500' }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return context.label + ': ' + context.raw.toLocaleString() + ' (' + percentage + '%)';
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
                backgroundColor: ['#1976d2', '#388e3c', '#f57c00', '#d32f2f'],
                borderWidth: 0,
                borderRadius: 4,
                barThickness: 32
            }]
        },
        options: {
            ...chartDefaults,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { 
                        color: '#f1f3f5',
                        drawBorder: false
                    },
                    ticks: {
                        font: { size: 11 },
                        color: '#6c757d',
                        padding: 8
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 11 },
                        color: '#495057',
                        padding: 8
                    }
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
                    backgroundColor: '#1976d2',
                    borderRadius: 4,
                    barThickness: 40
                },
                {
                    label: 'CM Closed',
                    data: cmData,
                    backgroundColor: '#d32f2f',
                    borderRadius: 4,
                    barThickness: 40
                }
            ]
        },
        options: {
            ...chartDefaults,
            scales: {
                x: { 
                    stacked: true,
                    grid: { display: false },
                    ticks: {
                        font: { size: 11 },
                        color: '#495057',
                        padding: 8
                    }
                },
                y: { 
                    stacked: true,
                    beginAtZero: true,
                    grid: { 
                        color: '#f1f3f5',
                        drawBorder: false
                    },
                    ticks: {
                        font: { size: 11 },
                        color: '#6c757d',
                        padding: 8
                    }
                }
            }
        }
    });
});
</script>
@endsection