<div class="mt-5 px-4 fade-in">   
    <!-- Stats Grid -->
    <div class="stats-grid fade-in">
        <div class="stat-card blue">
            <div class="stat-content">
                <div class="stat-label">Total Work Order</div>
                <div class="stat-value">{{ number_format($totalClosed) }}</div>
                <div class="stat-subtext">WO Completed & Closed</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
        
        <div class="stat-card purple">
            <div class="stat-content">
                <div class="stat-label">PM Closed</div>
                <div class="stat-value">{{ number_format($pmClosed) }}</div>
                <div class="stat-subtext">{{ $pmPercentage }}% Completion Rate</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-tools"></i>
            </div>
        </div>
        
        <div class="stat-card red">
            <div class="stat-content">
                <div class="stat-label">CM Closed</div>
                <div class="stat-value">{{ number_format($cmClosed) }}</div>
                <div class="stat-subtext">{{ $cmPercentage }}% Completion Rate</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-wrench"></i>
            </div>
        </div>
        
        <div class="stat-card green">
            <div class="stat-content">
                <div class="stat-label">Total Open WO</div>
                <div class="stat-value">{{ number_format($totalOpen) }}</div>
                <div class="stat-subtext">
                    PM: {{ number_format($pmOpen) }} | CM: {{ number_format($cmOpen) }}
                </div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-box-open"></i>
            </div>
        </div>
        
        <div class="stat-card orange">
            <div class="stat-content">
                <div class="stat-label">Best Performance</div>
                <div class="stat-value" style="font-size: 1rem; margin-top: 0.25rem;">{{ $bestPerformingUnit ?: 'N/A' }}</div>
                <div class="stat-subtext">
                    @if($maxRate >= 0)
                        Completion: {{ number_format($maxRate, 1) }}%
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-trophy"></i>
            </div>
        </div>
    </div>
    
    <!-- Charts Grid -->
    <div class="charts-grid">
        <!-- Trend Line Chart -->
        <div class="chart-card chart-large">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-chart-area"></i>
                    Tren Pemeliharaan 6 Bulan Terakhir
                </div>
                <div class="chart-subtitle">History PM dan CM (Closed) per bulan</div>
            </div>
            <div class="chart-wrapper">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
        
        <!-- Donut Chart -->
        <div class="chart-card chart-small">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-chart-pie"></i>
                    Komposisi Closed WO
                </div>
                <div class="chart-subtitle">Total WO Closed: {{ $totalClosed }}</div>
            </div>
            <div class="chart-wrapper small">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
        
        <!-- Horizontal Bar Chart -->
        <div class="chart-card chart-medium">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-building"></i>
                    Performa per Unit Layanan
                </div>
                <div class="chart-subtitle">Total WO Closed per unit</div>
            </div>
            <div class="chart-wrapper">
                <canvas id="horizontalBarChart"></canvas>
            </div>
        </div>
        
        <!-- Stacked Bar Chart -->
        <div class="chart-card chart-medium">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-layer-group"></i>
                    Komposisi PM & CM per Unit
                </div>
                <div class="chart-subtitle">Breakdown Closed WO</div>
            </div>
            <div class="chart-wrapper">
                <canvas id="stackedBarChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="table-card">
        <div class="chart-header">
            <div class="chart-title">
                <i class="fas fa-table"></i>
                Detail Kinerja per Unit Layanan
            </div>
            <div class="chart-subtitle">Ringkasan progress maintenance (Open vs Closed)</div>
        </div>
        
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="align-middle">Unit Layanan</th>
                        <th colspan="3" class="text-center border-b">Preventive Maintenance (PM)</th>
                        <th colspan="3" class="text-center border-b">Corrective Maintenance (CM)</th>
                    </tr>
                    <tr>
                        <th class="text-center text-xs">Closed</th>
                        <th class="text-center text-xs">Open</th>
                        <th class="text-center text-xs" style="min-width: 120px;">Progress</th>
                        <th class="text-center text-xs">Closed</th>
                        <th class="text-center text-xs">Open</th>
                        <th class="text-center text-xs" style="min-width: 120px;">Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unitNames as $i => $unit)
                    @php
                        $pmC = $pmClosedPerUnit[$i] ?? 0;
                        $pmO = $pmOpenPerUnit[$i] ?? 0;
                        $pmT = $pmC + $pmO;
                        $pmProg = $pmT > 0 ? round(($pmC / $pmT) * 100, 1) : 0;
                        
                        $cmC = $cmClosedPerUnit[$i] ?? 0;
                        $cmO = $cmOpenPerUnit[$i] ?? 0;
                        $cmT = $cmC + $cmO;
                        $cmProg = $cmT > 0 ? round(($cmC / $cmT) * 100, 1) : 0;
                        
                        $isBest = $unit === $bestPerformingUnit;
                    @endphp
                    <tr>
                        <td style="font-weight: 600;">
                            {{ $unit }}
                            @if($isBest)
                                <span class="best-badge">
                                    <i class="fas fa-crown"></i>
                                    Best
                                </span>
                            @endif
                        </td>
                        
                        <!-- PM Columns -->
                        <td class="text-center">
                            <span class="badge badge-pm">{{ $pmC }}</span>
                        </td>
                        <td class="text-center">
                            <span class="text-gray-500 font-medium">{{ $pmO }}</span>
                        </td>
                        <td>
                            <div class="progress-wrapper">
                                <span class="progress-label">{{ $pmProg }}%</span>
                                <div class="progress-bar-container">
                                    <div class="progress-bar pm" style="width: {{ $pmProg }}%"></div>
                                </div>
                            </div>
                        </td>
                        
                        <!-- CM Columns -->
                        <td class="text-center">
                            <span class="badge badge-cm">{{ $cmC }}</span>
                        </td>
                        <td class="text-center">
                            <span class="text-gray-500 font-medium">{{ $cmO }}</span>
                        </td>
                        <td>
                            <div class="progress-wrapper">
                                <span class="progress-label">{{ $cmProg }}%</span>
                                <div class="progress-bar-container">
                                    <div class="progress-bar cm" style="width: {{ $cmProg }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="font-weight: 700;">TOTAL</td>
                        
                        <!-- PM Total -->
                        <td class="text-center">
                            <span class="badge badge-pm">{{ $pmClosed }}</span>
                        </td>
                        <td class="text-center">
                            <span class="text-gray-600 font-bold">{{ $pmOpen }}</span>
                        </td>
                        <td>
                            <div class="progress-wrapper">
                                <span class="progress-label" style="font-weight: 600;">{{ $pmPercentage }}%</span>
                                <div class="progress-bar-container">
                                    <div class="progress-bar pm" style="width: {{ $pmPercentage }}%"></div>
                                </div>
                            </div>
                        </td>
                        
                        <!-- CM Total -->
                        <td class="text-center">
                            <span class="badge badge-cm">{{ $cmClosed }}</span>
                        </td>
                         <td class="text-center">
                            <span class="text-gray-600 font-bold">{{ $cmOpen }}</span>
                        </td>
                        <td>
                            <div class="progress-wrapper">
                                <span class="progress-label" style="font-weight: 600;">{{ $cmPercentage }}%</span>
                                <div class="progress-bar-container">
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
