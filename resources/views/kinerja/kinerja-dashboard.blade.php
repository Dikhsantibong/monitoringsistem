<div class="dashboard-container py-5 mt-5 px-[5px]">    
    <!-- Stats Grid -->
    <div class="stats-grid fade-in">
        <div class="stat-card blue">
            <div class="stat-content">
                <div class="stat-label">Total Work Order</div>
                <div class="stat-value">{{ number_format($totalWO) }}</div>
                <div class="stat-subtext">WO Completed & Closed</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
        
        <div class="stat-card purple">
            <div class="stat-content">
                <div class="stat-label">PM Closed</div>
                <div class="stat-value">{{ number_format($pmCount) }}</div>
                <div class="stat-subtext">{{ $pmPercentage }}% dari total WO</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-tools"></i>
            </div>
        </div>
        
        <div class="stat-card red">
            <div class="stat-content">
                <div class="stat-label">CM Closed</div>
                <div class="stat-value">{{ number_format($cmCount) }}</div>
                <div class="stat-subtext">{{ $cmPercentage }}% dari total WO</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-wrench"></i>
            </div>
        </div>
        
        <div class="stat-card green">
            <div class="stat-content">
                <div class="stat-label">Rasio PM/CM</div>
                <div class="stat-value">{{ $pmCmRatio }}</div>
                <div class="stat-subtext">
                    @if($pmCmRatio >= 3)
                        <span class="status-indicator success">
                            <i class="fas fa-check-circle"></i>
                            Target Tercapai
                        </span>
                    @else
                        <span class="status-indicator warning">
                            <i class="fas fa-exclamation-circle"></i>
                            Perlu Ditingkatkan
                        </span>
                    @endif
                </div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>
        
        <div class="stat-card orange">
            <div class="stat-content">
                <div class="stat-label">Best Performance</div>
                <div class="stat-value" style="font-size: 1rem; margin-top: 0.25rem;">{{ $bestPerformingUnit ?: 'N/A' }}</div>
                <div class="stat-subtext">
                    @if($maxRatio > 0)
                        Rasio: {{ number_format($maxRatio, 2) }}
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
                <div class="chart-subtitle">Perbandingan PM dan CM per bulan</div>
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
                    Distribusi PM vs CM
                </div>
                <div class="chart-subtitle">Total WO: {{ $totalWO }}</div>
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
                <div class="chart-subtitle">Total WO per unit</div>
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
                <div class="chart-subtitle">Breakdown detail</div>
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
            <div class="chart-subtitle">Ringkasan lengkap dengan persentase dan progress</div>
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
                        <th style="min-width: 180px;">Progress PM</th>
                        <th style="min-width: 180px;">Progress CM</th>
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
                        <td style="font-weight: 600;">
                            {{ $unit }}
                            @if($isBest)
                                <span class="best-badge">
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
                        <td class="text-center" style="font-weight: 600; font-size: 0.9375rem;">
                            {{ $ratio }}
                        </td>
                        <td>
                            <div class="progress-wrapper">
                                <span class="progress-label">{{ $pmPercent }}%</span>
                                <div class="progress-bar-container">
                                    <div class="progress-bar pm" style="width: {{ $pmPercent }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="progress-wrapper">
                                <span class="progress-label">{{ $cmPercent }}%</span>
                                <div class="progress-bar-container">
                                    <div class="progress-bar cm" style="width: {{ $cmPercent }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="font-weight: 700;">TOTAL</td>
                        <td class="text-center">
                            <span class="badge badge-pm">{{ $pmCount }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-cm">{{ $cmCount }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-total">{{ $totalWO }}</span>
                        </td>
                        <td class="text-center" style="font-weight: 700; font-size: 0.9375rem;">
                            {{ $pmCmRatio }}
                        </td>
                        <td>
                            <div class="progress-wrapper">
                                <span class="progress-label" style="font-weight: 600;">{{ $pmPercentage }}%</span>
                                <div class="progress-bar-container">
                                    <div class="progress-bar pm" style="width: {{ $pmPercentage }}%"></div>
                                </div>
                            </div>
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