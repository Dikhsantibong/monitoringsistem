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
            padding: 0 24px 1rem;
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
            color: #d32f2f;
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
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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

        .stat-card.red .stat-value { color: #d32f2f; }
        .stat-card.red .stat-icon-wrapper { background: #ffebee; color: #d32f2f; }

        .stat-card.blue .stat-value { color: #1976d2; }
        .stat-card.blue .stat-icon-wrapper { background: #e3f2fd; color: #1976d2; }

        .stat-card.green .stat-value { color: #388e3c; }
        .stat-card.green .stat-icon-wrapper { background: #e8f5e9; color: #388e3c; }

        .stat-card.orange .stat-value { color: #f57c00; }
        .stat-card.orange .stat-icon-wrapper { background: #fff3e0; color: #f57c00; }

        .stat-card.purple .stat-value { color: #7b1fa2; }
        .stat-card.purple .stat-icon-wrapper { background: #f3e5f5; color: #7b1fa2; }

        /* Filter Bar */
        .filter-card {
            background: white;
            border: 1px solid #e1e4e8;
            border-radius: 6px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .filter-card form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
        }

        .filter-card label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #495057;
        }

        .filter-card select {
            border: 1px solid #e1e4e8;
            border-radius: 4px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            color: #495057;
        }

        .filter-card select:focus {
            outline: none;
            border-color: #1976d2;
            box-shadow: 0 0 0 2px rgba(25, 118, 210, 0.15);
        }

        .filter-btn {
            background: #1976d2;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 0.5rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .filter-btn:hover {
            background: #1565c0;
        }

        .filter-info {
            font-size: 0.8rem;
            color: #868e96;
            margin-left: auto;
        }

        /* Table Card */
        .table-card {
            background: white;
            border: 1px solid #e1e4e8;
            border-radius: 6px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
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

        /* Data Table */
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

        .badge-cm {
            background: #ffebee;
            color: #d32f2f;
        }

        .badge-pm {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge-warning {
            background: #fff3e0;
            color: #f57c00;
        }

        .badge-success {
            background: #e8f5e9;
            color: #388e3c;
        }

        .badge-neutral {
            background: #f1f3f5;
            color: #495057;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }

        .badge-info {
            background: #e3f2fd;
            color: #1976d2;
        }

        /* Tabs Styling */
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

        .tab-pane {
            display: none;
            animation: fadeIn 0.4s ease forwards;
        }

        .tab-pane.active {
            display: block;
        }

        /* Status Indicator */
        .status-covered {
            color: #388e3c;
            font-weight: 600;
            font-size: 0.8125rem;
        }

        .status-not-covered {
            color: #d32f2f;
            font-weight: 600;
            font-size: 0.8125rem;
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

        /* Recurring Asset Group */
        .asset-group {
            background: white;
            border: 1px solid #e1e4e8;
            border-radius: 6px;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .asset-group-header {
            background: #f8f9fa;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
            border-bottom: 1px solid #e1e4e8;
        }

        .asset-group-header .asset-name {
            font-weight: 700;
            color: #1a202c;
            font-size: 0.95rem;
        }

        /* Alert */
        .alert-error {
            background: #ffebee;
            border: 1px solid #ffcdd2;
            color: #d32f2f;
            padding: 1rem 1.25rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #868e96;
        }

        .empty-state i {
            font-size: 2.5rem;
            color: #dee2e6;
            margin-bottom: 1rem;
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 1.5rem;
            }

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

            .filter-card form {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-info {
                margin-left: 0;
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

        /* Row severity */
        .row-critical { border-left: 3px solid #d32f2f; }
        .row-warning { border-left: 3px solid #f57c00; }
    </style>
@endsection

@section('content')
@include('components.navbar')

<div class="dashboard-overlay">
    {{-- Page Header --}}
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-heartbeat"></i>
            Peta Kesehatan Unit
        </h1>
        <p class="page-subtitle">Analisis Gangguan (CM), Antisipasi PM & Aset Gangguan Berulang — Data MAXIMO</p>
    </div>

    {{-- Error Alert --}}
    @if($error)
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ $error }}</span>
        </div>
    @endif

    {{-- Filter --}}
    <div class="filter-card fade-in">
        <form action="{{ route('peta-kesehatan-unit') }}" method="GET">
            <label><i class="fas fa-filter mr-1"></i> Periode Data</label>
            <select name="months">
                <option value="3" {{ $filterMonths == 3 ? 'selected' : '' }}>3 Bulan Terakhir</option>
                <option value="6" {{ $filterMonths == 6 ? 'selected' : '' }}>6 Bulan Terakhir</option>
                <option value="12" {{ $filterMonths == 12 ? 'selected' : '' }}>12 Bulan Terakhir</option>
                <option value="24" {{ $filterMonths == 24 ? 'selected' : '' }}>24 Bulan Terakhir</option>
            </select>
            <button type="submit" class="filter-btn"><i class="fas fa-search mr-1"></i> Terapkan Filter</button>
            <span class="filter-info">
                <i class="far fa-calendar-alt mr-1"></i>
                {{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}
            </span>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="stats-grid fade-in">
        <div class="stat-card red">
            <div class="stat-content">
                <div class="stat-label">Total WO Gangguan (CM)</div>
                <div class="stat-value">{{ number_format($summary['total_cm_wo']) }}</div>
                <div class="stat-subtext">Corrective Maintenance</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>

        <div class="stat-card blue">
            <div class="stat-content">
                <div class="stat-label">Aset Terdampak CM</div>
                <div class="stat-value">{{ number_format($summary['total_assets_with_cm']) }}</div>
                <div class="stat-subtext">Aset unik dengan CM</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-microchip"></i>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-content">
                <div class="stat-label">Aset Ter-cover PM</div>
                <div class="stat-value">{{ number_format($summary['assets_with_pm']) }}</div>
                <div class="stat-subtext">Diantisipasi PM</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>

        <div class="stat-card orange">
            <div class="stat-content">
                <div class="stat-label">Aset Tanpa PM</div>
                <div class="stat-value">{{ number_format($summary['assets_without_pm']) }}</div>
                <div class="stat-subtext">Belum ada antisipasi</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-exclamation-circle"></i>
            </div>
        </div>

        <div class="stat-card purple">
            <div class="stat-content">
                <div class="stat-label">Gangguan Berulang</div>
                <div class="stat-value">{{ number_format($summary['recurring_assets']) }}</div>
                <div class="stat-subtext">Aset ≥ 2× CM</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-redo-alt"></i>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="tabs-nav">
        <button class="tab-button active" data-tab="cm-tab">
            <i class="fas fa-exclamation-triangle"></i>
            Aset Sering Gangguan
        </button>
        <button class="tab-button" data-tab="pm-tab">
            <i class="fas fa-shield-alt"></i>
            Antisipasi PM
        </button>
        <button class="tab-button" data-tab="recurring-tab">
            <i class="fas fa-redo-alt"></i>
            Gangguan Berulang
        </button>
    </div>

    {{-- ==================== TAB 1: ASET SERING CM ==================== --}}
    <div id="cm-tab" class="tab-pane {{ request()->has('cm_page') || (!request()->has('pm_page') && !request()->has('recurring_page')) ? 'active' : '' }} fade-in">
        <div class="table-card">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-fire"></i>
                    Aset Paling Sering Mengalami Gangguan (CM)
                </div>
                <div class="chart-subtitle">Frekuensi Corrective Maintenance tertinggi per ASSETNUM — Halaman {{ $cmAssetsPaginator->currentPage() }} dari {{ $cmAssetsPaginator->lastPage() }}</div>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Asset Number</th>
                            <th>Location</th>
                            <th class="text-center">Jumlah CM</th>
                            <th class="text-center">Status PM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cmAssets as $i => $asset)
                            @php
                                $hasPm = isset($pmCoverage[$asset['assetnum']]);
                                $rowClass = $asset['cm_count'] >= 5 ? 'row-critical' : ($asset['cm_count'] >= 3 ? 'row-warning' : '');
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td class="text-center" style="color: #868e96;">{{ ($cmAssetsPaginator->currentPage() - 1) * $cmAssetsPaginator->perPage() + $i + 1 }}</td>
                                <td style="font-weight: 600;">{{ $asset['assetnum'] }}</td>
                                <td><span class="badge-neutral">{{ $asset['location'] }}</span></td>
                                <td class="text-center">
                                    @if($asset['cm_count'] >= 5)
                                        <span class="badge badge-cm">{{ $asset['cm_count'] }}×</span>
                                    @elseif($asset['cm_count'] >= 3)
                                        <span class="badge badge-warning">{{ $asset['cm_count'] }}×</span>
                                    @else
                                        <span class="badge badge-info">{{ $asset['cm_count'] }}×</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($hasPm)
                                        <span class="status-covered">
                                            <i class="fas fa-check-circle mr-1"></i> Ter-cover PM
                                        </span>
                                    @else
                                        <span class="status-not-covered">
                                            <i class="fas fa-times-circle mr-1"></i> Belum PM
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-database"></i>
                                        <p>Tidak ada data CM ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $cmAssetsPaginator->appends(request()->except('cm_page'))->links() }}
            </div>
        </div>
    </div>

    {{-- ==================== TAB 2: ANTISIPASI PM ==================== --}}
    <div id="pm-tab" class="tab-pane {{ request()->has('pm_page') ? 'active' : '' }}">
        <div class="table-card">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-shield-alt"></i>
                    Antisipasi PM untuk Aset yang Sering CM
                </div>
                <div class="chart-subtitle">Detail PM coverage untuk setiap aset yang sering gangguan (Tersinkronisasi dengan Tab 1)</div>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Asset Number</th>
                            <th>Location</th>
                            <th class="text-center">Jml CM</th>
                            <th class="text-center">Total PM</th>
                            <th class="text-center">PM Closed</th>
                            <th class="text-center">PM Open</th>
                            <th style="min-width: 140px;">Progress PM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cmAssets as $i => $asset)
                            @php
                                $pm = $pmCoverage[$asset['assetnum']] ?? null;
                            @endphp
                            <tr>
                                <td class="text-center" style="color: #868e96;">{{ ($cmAssetsPaginator->currentPage() - 1) * $cmAssetsPaginator->perPage() + $i + 1 }}</td>
                                <td style="font-weight: 600;">{{ $asset['assetnum'] }}</td>
                                <td><span class="badge-neutral">{{ $asset['location'] }}</span></td>
                                <td class="text-center">
                                    <span class="badge badge-cm">{{ $asset['cm_count'] }}×</span>
                                </td>
                                @if($pm)
                                    <td class="text-center">
                                        <span class="badge badge-pm">{{ $pm['pm_count'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ $pm['pm_closed'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($pm['pm_open'] > 0)
                                            <span class="badge badge-warning">{{ $pm['pm_open'] }}</span>
                                        @else
                                            <span style="color: #868e96;">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $pmTotal = max($pm['pm_count'], 1);
                                            $pmProg = round(($pm['pm_closed'] / $pmTotal) * 100);
                                        @endphp
                                        <div class="progress-wrapper">
                                            <span class="progress-label">{{ $pmProg }}%</span>
                                            <div class="progress-bar-container">
                                                <div class="progress-bar pm" style="width: {{ $pmProg }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                @else
                                    <td class="text-center" colspan="4">
                                        <span class="status-not-covered">
                                            <i class="fas fa-times-circle mr-1"></i> Belum ada PM untuk aset ini
                                        </span>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="fas fa-database"></i>
                                        <p>Tidak ada data ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $cmAssetsPaginator->appends(request()->except('cm_page'))->links() }}
            </div>
        </div>
    </div>

    {{-- ==================== TAB 3: GANGGUAN BERULANG ==================== --}}
    <div id="recurring-tab" class="tab-pane {{ request()->has('recurring_page') ? 'active' : '' }}">
        <div class="table-card">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-redo-alt"></i>
                    Aset Gangguan Berulang (≥ 2× CM)
                </div>
                <div class="chart-subtitle">Detail riwayat WO gangguan berulang per ASSETNUM — Halaman {{ $recurringAssetsPaginator->currentPage() }} dari {{ $recurringAssetsPaginator->lastPage() }}</div>
            </div>

            @forelse($recurringAssets as $assetNum => $woList)
                @php
                    // Optimization: we already have cm_count in the paginator items if we wanted, 
                    // but since details are separate, we'll use a count from the list
                    $cmCount = count($woList);
                    $hasPm = isset($pmCoverage[$assetNum]);
                @endphp
                <div class="asset-group">
                    <div class="asset-group-header">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span class="asset-name">
                                <i class="fas fa-cog mr-1" style="color: #1976d2;"></i>
                                {{ $assetNum }}
                            </span>
                            @if($cmCount >= 5)
                                <span class="badge badge-cm">{{ $cmCount }}× CM</span>
                            @elseif($cmCount >= 3)
                                <span class="badge badge-warning">{{ $cmCount }}× CM</span>
                            @else
                                <span class="badge badge-info">{{ $cmCount }}× CM</span>
                            @endif
                        </div>
                        <div style="display: flex; gap: 0.75rem; align-items: center;">
                            @if($hasPm)
                                <span class="status-covered"><i class="fas fa-check-circle mr-1"></i> PM Tersedia</span>
                            @else
                                <span class="status-not-covered"><i class="fas fa-times-circle mr-1"></i> Belum Ada PM</span>
                            @endif
                            <span class="badge-neutral">{{ $woList->first()['location'] ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="table-wrapper" style="border: none; border-radius: 0;">
                        <table class="data-table" style="margin: 0;">
                            <thead>
                                <tr>
                                    <th>WO Number</th>
                                    <th>Deskripsi</th>
                                    <th class="text-center">Status</th>
                                    <th>Tanggal Lapor</th>
                                    <th>Status Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($woList as $wo)
                                    <tr>
                                        <td style="font-weight: 600; color: #1976d2;">{{ $wo['wonum'] }}</td>
                                        <td style="max-width: 350px; color: #495057; font-size: 0.8125rem;">
                                            {{ \Illuminate\Support\Str::limit($wo['description'], 100) }}
                                        </td>
                                        <td class="text-center">
                                            @php $st = strtoupper(trim($wo['status'])); @endphp
                                            @if(in_array($st, ['COMP', 'CLOSE', 'CLOSED']))
                                                <span class="badge badge-success">{{ $wo['status'] }}</span>
                                            @elseif(in_array($st, ['INPRG', 'IN PROGRESS']))
                                                <span class="badge badge-info">{{ $wo['status'] }}</span>
                                            @elseif($st === 'APPR')
                                                <span class="badge badge-warning">{{ $wo['status'] }}</span>
                                            @else
                                                <span class="badge-neutral">{{ $wo['status'] }}</span>
                                            @endif
                                        </td>
                                        <td style="color: #868e96; font-size: 0.8125rem;">{{ $wo['reportdate'] }}</td>
                                        <td style="color: #868e96; font-size: 0.8125rem;">{{ $wo['statusdate'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-check-circle" style="color: #388e3c;"></i>
                    <p style="font-weight: 600; color: #495057; font-size: 1rem;">Tidak ada aset dengan gangguan berulang</p>
                    <p>Semua aset hanya memiliki 1 atau 0 CM dalam periode ini</p>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $recurringAssetsPaginator->appends(request()->except('recurring_page'))->links() }}
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.getAttribute('data-tab');

            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));

            button.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });

    // If there's a specific page in URL, make sure the right tab is active
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('pm_page')) {
        setActiveTab('pm-tab');
    } else if (urlParams.has('recurring_page')) {
        setActiveTab('recurring-tab');
    } else if (urlParams.has('cm_page')) {
        setActiveTab('cm-tab');
    }

    function setActiveTab(tabId) {
        tabButtons.forEach(btn => {
            if (btn.getAttribute('data-tab') === tabId) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        tabPanes.forEach(pane => {
            if (pane.id === tabId) {
                pane.classList.add('active');
            } else {
                pane.classList.remove('active');
            }
        });
    }
});
</script>
@endsection
