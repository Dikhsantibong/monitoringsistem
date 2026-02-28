@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
        }
        .pku-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        .pku-header {
            text-align: center;
            padding: 2rem 0 1.5rem;
        }
        .pku-header h1 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #38bdf8, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        .pku-header p {
            color: #94a3b8;
            font-size: 0.95rem;
        }

        /* Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .summary-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 16px;
            padding: 1.25rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .summary-card:hover {
            transform: translateY(-4px);
            border-color: rgba(56, 189, 248, 0.4);
            box-shadow: 0 8px 32px rgba(56, 189, 248, 0.15);
        }
        .summary-card .icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .summary-card .value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.2;
        }
        .summary-card .label {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-top: 0.25rem;
            font-weight: 500;
        }
        .card-red .icon, .card-red .value { color: #f87171; }
        .card-blue .icon, .card-blue .value { color: #38bdf8; }
        .card-green .icon, .card-green .value { color: #4ade80; }
        .card-amber .icon, .card-amber .value { color: #fbbf24; }
        .card-purple .icon, .card-purple .value { color: #a78bfa; }

        /* Filter Bar */
        .filter-bar {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 16px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
        }
        .filter-bar label {
            color: #94a3b8;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .filter-bar select,
        .filter-bar button {
            background: rgba(15, 23, 42, 0.6);
            color: #e2e8f0;
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .filter-bar select:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2);
        }
        .filter-bar button {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        .filter-bar button:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* Section Cards */
        .section-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 16px;
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .section-header {
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.1), rgba(99, 102, 241, 0.1));
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        }
        .section-header h2 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #f1f5f9;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .section-header h2 i {
            color: #38bdf8;
        }
        .section-header p {
            color: #64748b;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            margin-left: 2rem;
        }

        /* Glass Table */
        .glass-table-wrapper {
            overflow-x: auto;
            padding: 1rem 1.5rem 1.5rem;
        }
        .glass-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.85rem;
        }
        .glass-table thead th {
            background: rgba(15, 23, 42, 0.7);
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
            position: sticky;
            top: 0;
            white-space: nowrap;
        }
        .glass-table tbody tr {
            transition: background 0.2s ease;
        }
        .glass-table tbody tr:hover {
            background: rgba(56, 189, 248, 0.05);
        }
        .glass-table tbody td {
            padding: 0.75rem 1rem;
            color: #e2e8f0;
            border-bottom: 1px solid rgba(148, 163, 184, 0.06);
            vertical-align: middle;
        }
        .glass-table .text-center { text-align: center; }

        /* Badges */
        .badge-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 0.2rem 0.65rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.8rem;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .badge-success {
            background: rgba(34, 197, 94, 0.2);
            color: #86efac;
            padding: 0.2rem 0.65rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.8rem;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .badge-warning {
            background: rgba(251, 191, 36, 0.2);
            color: #fde68a;
            padding: 0.2rem 0.65rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.8rem;
            border: 1px solid rgba(251, 191, 36, 0.3);
        }
        .badge-info {
            background: rgba(56, 189, 248, 0.2);
            color: #7dd3fc;
            padding: 0.2rem 0.65rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.8rem;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }
        .badge-neutral {
            background: rgba(148, 163, 184, 0.15);
            color: #cbd5e1;
            padding: 0.2rem 0.65rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        /* Status indicator */
        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
        }
        .status-dot.covered { background: #4ade80; box-shadow: 0 0 6px rgba(74, 222, 128, 0.4); }
        .status-dot.not-covered { background: #f87171; box-shadow: 0 0 6px rgba(248, 113, 113, 0.4); }

        /* Progress bar */
        .pm-progress {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .pm-progress-bar {
            flex: 1;
            height: 6px;
            background: rgba(148, 163, 184, 0.15);
            border-radius: 999px;
            overflow: hidden;
        }
        .pm-progress-bar .fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #22c55e, #4ade80);
            transition: width 0.6s ease;
        }
        .pm-progress-text {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 600;
            min-width: 40px;
            text-align: right;
        }

        /* Collapsible */
        .collapse-toggle {
            cursor: pointer;
            user-select: none;
            color: #38bdf8;
        }
        .collapse-toggle:hover {
            color: #7dd3fc;
        }
        .collapse-content {
            display: none;
        }
        .collapse-content.show {
            display: table-row;
        }

        /* Alert box */
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .alert-error i { color: #f87171; }

        /* Asset highlight row for repeat offenders */
        .repeat-high { border-left: 3px solid #f87171; }
        .repeat-medium { border-left: 3px solid #fbbf24; }

        /* Footer spacer */
        .footer-spacer { height: 3rem; }

        /* Responsive */
        @media (max-width: 768px) {
            .pku-header h1 { font-size: 1.5rem; }
            .summary-grid { grid-template-columns: repeat(2, 1fr); }
            .filter-bar { flex-direction: column; align-items: stretch; }
        }

        /* Tab navigation */
        .tab-nav {
            display: flex;
            gap: 0;
            margin-bottom: 0;
            border-bottom: 1px solid rgba(148, 163, 184, 0.15);
            background: rgba(30, 41, 59, 0.8);
            border-radius: 16px 16px 0 0;
            overflow: hidden;
        }
        .tab-btn {
            flex: 1;
            text-align: center;
            padding: 1rem 1.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }
        .tab-btn:hover { color: #94a3b8; background: rgba(56, 189, 248, 0.05); }
        .tab-btn.active {
            color: #38bdf8;
            border-bottom-color: #38bdf8;
            background: rgba(56, 189, 248, 0.08);
        }
        .tab-btn i { margin-right: 0.5rem; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
@endsection

@section('content')
    @include('components.loader')
    <div id="page-content" class="page-transition">
        <div class="w-full">
            @include('components.navbar')
            <div class="h-[80px]"></div>

            <div class="pku-container" style="padding-top: 1rem; padding-bottom: 2rem;">
                {{-- Header --}}
                <div class="pku-header">
                    <h1><i class="fas fa-heartbeat" style="-webkit-text-fill-color: #f87171; margin-right: 8px;"></i> Peta Kesehatan Unit</h1>
                    <p>Analisis Gangguan (CM), Antisipasi PM & Aset Gangguan Berulang — Data MAXIMO</p>
                </div>

                {{-- Error Alert --}}
                @if($error)
                    <div class="alert-error">
                        <i class="fas fa-exclamation-circle fa-lg"></i>
                        <span>{{ $error }}</span>
                    </div>
                @endif

                {{-- Filter --}}
                <form action="{{ route('peta-kesehatan-unit') }}" method="GET">
                    <div class="filter-bar">
                        <label><i class="fas fa-filter mr-1"></i> Periode Data</label>
                        <select name="months">
                            <option value="3" {{ $filterMonths == 3 ? 'selected' : '' }}>3 Bulan Terakhir</option>
                            <option value="6" {{ $filterMonths == 6 ? 'selected' : '' }}>6 Bulan Terakhir</option>
                            <option value="12" {{ $filterMonths == 12 ? 'selected' : '' }}>12 Bulan Terakhir</option>
                            <option value="24" {{ $filterMonths == 24 ? 'selected' : '' }}>24 Bulan Terakhir</option>
                        </select>
                        <button type="submit"><i class="fas fa-search mr-1"></i> Terapkan Filter</button>
                        <span style="color: #64748b; font-size: 0.8rem; margin-left: auto;">
                            <i class="far fa-calendar-alt mr-1"></i>
                            {{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}
                        </span>
                    </div>
                </form>

                {{-- Summary Cards --}}
                <div class="summary-grid">
                    <div class="summary-card card-red">
                        <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="value">{{ number_format($summary['total_cm_wo']) }}</div>
                        <div class="label">Total WO Gangguan (CM)</div>
                    </div>
                    <div class="summary-card card-blue">
                        <div class="icon"><i class="fas fa-microchip"></i></div>
                        <div class="value">{{ number_format($summary['total_assets_with_cm']) }}</div>
                        <div class="label">Aset Terdampak CM</div>
                    </div>
                    <div class="summary-card card-green">
                        <div class="icon"><i class="fas fa-shield-alt"></i></div>
                        <div class="value">{{ number_format($summary['assets_with_pm']) }}</div>
                        <div class="label">Aset Ter-cover PM</div>
                    </div>
                    <div class="summary-card card-amber">
                        <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
                        <div class="value">{{ number_format($summary['assets_without_pm']) }}</div>
                        <div class="label">Aset Tanpa PM</div>
                    </div>
                    <div class="summary-card card-purple">
                        <div class="icon"><i class="fas fa-redo-alt"></i></div>
                        <div class="value">{{ number_format($summary['recurring_assets']) }}</div>
                        <div class="label">Aset Gangguan Berulang</div>
                    </div>
                </div>

                {{-- Tab Navigation --}}
                <div class="tab-nav">
                    <button class="tab-btn active" onclick="switchTab('cm-tab')" id="btn-cm-tab">
                        <i class="fas fa-exclamation-triangle"></i> Aset Sering Gangguan
                    </button>
                    <button class="tab-btn" onclick="switchTab('pm-tab')" id="btn-pm-tab">
                        <i class="fas fa-shield-alt"></i> Antisipasi PM
                    </button>
                    <button class="tab-btn" onclick="switchTab('recurring-tab')" id="btn-recurring-tab">
                        <i class="fas fa-redo-alt"></i> Gangguan Berulang
                    </button>
                </div>

                {{-- ==================== TAB 1: ASET SERING CM ==================== --}}
                <div class="tab-content active" id="cm-tab">
                    <div class="section-card" style="border-radius: 0 0 16px 16px;">
                        <div class="section-header">
                            <h2><i class="fas fa-fire"></i> Aset Paling Sering Mengalami Gangguan (CM)</h2>
                            <p>Daftar aset dengan frekuensi Corrective Maintenance tertinggi berdasarkan ASSETNUM</p>
                        </div>
                        <div class="glass-table-wrapper">
                            <table class="glass-table" id="cmTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Asset Number</th>
                                        <th>Location</th>
                                        <th class="text-center">Jumlah CM</th>
                                        <th>Deskripsi Terakhir</th>
                                        <th>Terakhir Dilaporkan</th>
                                        <th class="text-center">Status PM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cmAssets as $i => $asset)
                                        @php
                                            $hasPm = isset($pmCoverage[$asset['assetnum']]);
                                            $rowClass = $asset['cm_count'] >= 5 ? 'repeat-high' : ($asset['cm_count'] >= 3 ? 'repeat-medium' : '');
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td class="text-center" style="color: #64748b;">{{ $i + 1 }}</td>
                                            <td>
                                                <span style="font-weight: 700; color: #f1f5f9;">{{ $asset['assetnum'] }}</span>
                                            </td>
                                            <td><span class="badge-neutral">{{ $asset['location'] }}</span></td>
                                            <td class="text-center">
                                                @if($asset['cm_count'] >= 5)
                                                    <span class="badge-danger">{{ $asset['cm_count'] }}×</span>
                                                @elseif($asset['cm_count'] >= 3)
                                                    <span class="badge-warning">{{ $asset['cm_count'] }}×</span>
                                                @else
                                                    <span class="badge-info">{{ $asset['cm_count'] }}×</span>
                                                @endif
                                            </td>
                                            <td style="max-width: 300px; white-space: normal; color: #cbd5e1; font-size: 0.8rem;">
                                                {{ \Illuminate\Support\Str::limit($asset['last_description'], 80) }}
                                            </td>
                                            <td style="color: #94a3b8; font-size: 0.8rem;">{{ $asset['last_report_date'] }}</td>
                                            <td class="text-center">
                                                @if($hasPm)
                                                    <span class="status-dot covered"></span>
                                                    <span style="color: #4ade80; font-size: 0.8rem;">Ter-cover</span>
                                                @else
                                                    <span class="status-dot not-covered"></span>
                                                    <span style="color: #f87171; font-size: 0.8rem;">Belum PM</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center" style="padding: 2rem; color: #64748b;">
                                                <i class="fas fa-database fa-2x mb-2" style="display: block;"></i>
                                                Tidak ada data CM ditemukan
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ==================== TAB 2: ANTISIPASI PM ==================== --}}
                <div class="tab-content" id="pm-tab">
                    <div class="section-card" style="border-radius: 0 0 16px 16px;">
                        <div class="section-header">
                            <h2><i class="fas fa-shield-alt"></i> Antisipasi PM untuk Aset CM</h2>
                            <p>Aset yang sering gangguan dan sudah diantisipasi dengan Preventive Maintenance</p>
                        </div>
                        <div class="glass-table-wrapper">
                            <table class="glass-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Asset Number</th>
                                        <th>Location</th>
                                        <th class="text-center">Jumlah CM</th>
                                        <th class="text-center">Total PM</th>
                                        <th class="text-center">PM Closed</th>
                                        <th class="text-center">PM Open</th>
                                        <th>Progress PM</th>
                                        <th>Deskripsi PM Terakhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $pmIndex = 0; @endphp
                                    @forelse($cmAssets as $asset)
                                        @php
                                            $pm = $pmCoverage[$asset['assetnum']] ?? null;
                                        @endphp
                                        <tr>
                                            <td class="text-center" style="color: #64748b;">{{ ++$pmIndex }}</td>
                                            <td>
                                                <span style="font-weight: 700; color: #f1f5f9;">{{ $asset['assetnum'] }}</span>
                                            </td>
                                            <td><span class="badge-neutral">{{ $asset['location'] }}</span></td>
                                            <td class="text-center">
                                                <span class="badge-danger">{{ $asset['cm_count'] }}×</span>
                                            </td>
                                            @if($pm)
                                                <td class="text-center">
                                                    <span class="badge-info">{{ $pm['pm_count'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge-success">{{ $pm['pm_closed'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($pm['pm_open'] > 0)
                                                        <span class="badge-warning">{{ $pm['pm_open'] }}</span>
                                                    @else
                                                        <span style="color: #64748b;">0</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $pmTotal = $pm['pm_count'] > 0 ? $pm['pm_count'] : 1;
                                                        $pmProg = round(($pm['pm_closed'] / $pmTotal) * 100);
                                                    @endphp
                                                    <div class="pm-progress">
                                                        <div class="pm-progress-bar">
                                                            <div class="fill" style="width: {{ $pmProg }}%"></div>
                                                        </div>
                                                        <span class="pm-progress-text">{{ $pmProg }}%</span>
                                                    </div>
                                                </td>
                                                <td style="max-width: 250px; white-space: normal; color: #cbd5e1; font-size: 0.8rem;">
                                                    {{ \Illuminate\Support\Str::limit($pm['last_pm_description'], 60) }}
                                                </td>
                                            @else
                                                <td class="text-center" colspan="5" style="color: #f87171; font-size: 0.8rem;">
                                                    <i class="fas fa-times-circle mr-1"></i> Belum ada PM untuk aset ini
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center" style="padding: 2rem; color: #64748b;">
                                                <i class="fas fa-database fa-2x mb-2" style="display: block;"></i>
                                                Tidak ada data ditemukan
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ==================== TAB 3: GANGGUAN BERULANG ==================== --}}
                <div class="tab-content" id="recurring-tab">
                    <div class="section-card" style="border-radius: 0 0 16px 16px;">
                        <div class="section-header">
                            <h2><i class="fas fa-redo-alt"></i> Aset Gangguan Berulang (≥ 2× CM)</h2>
                            <p>Detail riwayat WO gangguan berulang per aset — diurutkan berdasarkan ASSETNUM</p>
                        </div>
                        <div class="glass-table-wrapper">
                            @forelse($recurringAssets as $assetNum => $woList)
                                @php
                                    $cmCount = $cmAssets->firstWhere('assetnum', $assetNum)['cm_count'] ?? 0;
                                    $hasPm = isset($pmCoverage[$assetNum]);
                                @endphp
                                <div style="margin-bottom: 1.5rem; background: rgba(15, 23, 42, 0.4); border-radius: 12px; overflow: hidden; border: 1px solid rgba(148, 163, 184, 0.08);">
                                    <div style="padding: 0.75rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; background: rgba(15, 23, 42, 0.6);">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <span style="font-weight: 800; color: #f1f5f9; font-size: 0.95rem;">
                                                <i class="fas fa-cog mr-1" style="color: #38bdf8;"></i>
                                                {{ $assetNum }}
                                            </span>
                                            @if($cmCount >= 5)
                                                <span class="badge-danger">{{ $cmCount }}× CM</span>
                                            @elseif($cmCount >= 3)
                                                <span class="badge-warning">{{ $cmCount }}× CM</span>
                                            @else
                                                <span class="badge-info">{{ $cmCount }}× CM</span>
                                            @endif
                                        </div>
                                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                                            @if($hasPm)
                                                <span style="color: #4ade80; font-size: 0.8rem; font-weight: 600;">
                                                    <i class="fas fa-check-circle mr-1"></i> PM Tersedia
                                                </span>
                                            @else
                                                <span style="color: #f87171; font-size: 0.8rem; font-weight: 600;">
                                                    <i class="fas fa-times-circle mr-1"></i> Belum Ada PM
                                                </span>
                                            @endif
                                            <span class="badge-neutral">{{ $woList->first()['location'] ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <table class="glass-table" style="margin: 0;">
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
                                                    <td style="font-weight: 600; color: #93c5fd;">{{ $wo['wonum'] }}</td>
                                                    <td style="max-width: 350px; white-space: normal; color: #cbd5e1; font-size: 0.8rem;">
                                                        {{ \Illuminate\Support\Str::limit($wo['description'], 100) }}
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $st = strtoupper(trim($wo['status']));
                                                        @endphp
                                                        @if(in_array($st, ['COMP', 'CLOSE', 'CLOSED']))
                                                            <span class="badge-success">{{ $wo['status'] }}</span>
                                                        @elseif(in_array($st, ['INPRG', 'IN PROGRESS']))
                                                            <span class="badge-info">{{ $wo['status'] }}</span>
                                                        @elseif($st === 'APPR')
                                                            <span class="badge-warning">{{ $wo['status'] }}</span>
                                                        @else
                                                            <span class="badge-neutral">{{ $wo['status'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td style="color: #94a3b8; font-size: 0.8rem;">{{ $wo['reportdate'] }}</td>
                                                    <td style="color: #94a3b8; font-size: 0.8rem;">{{ $wo['statusdate'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @empty
                                <div style="text-align: center; padding: 3rem; color: #64748b;">
                                    <i class="fas fa-check-circle fa-3x" style="color: #4ade80; margin-bottom: 1rem; display: block;"></i>
                                    <p style="font-size: 1.1rem; font-weight: 600; color: #94a3b8;">Tidak ada aset dengan gangguan berulang</p>
                                    <p style="font-size: 0.85rem;">Semua aset hanya memiliki 1 atau 0 CM dalam periode ini</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="footer-spacer"></div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

            // Show selected tab
            document.getElementById(tabId).classList.add('active');
            document.getElementById('btn-' + tabId).classList.add('active');
        }
    </script>
@endsection
