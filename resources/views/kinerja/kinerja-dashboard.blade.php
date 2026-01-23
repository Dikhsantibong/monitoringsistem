<div class="mt-5 px-4 fade-in">   
    <!-- Filter Section -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('kinerja.pemeliharaan') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <input type="hidden" name="tab" value="kinerja-tab"> <!-- Try to keep tab if possible via JS, or just default -->
            
            <div class="flex flex-col gap-1 w-full md:w-auto">
                <label for="start_date" class="text-xs font-semibold text-gray-600">Start Date</label>
                <input type="date" id="start_date" name="start_date" 
                       value="{{ $filterStartDate ?? date('Y-m-d', strtotime('-6 months')) }}"
                       class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex flex-col gap-1 w-full md:w-auto">
                <label for="end_date" class="text-xs font-semibold text-gray-600">End Date</label>
                <input type="date" id="end_date" name="end_date" 
                       value="{{ $filterEndDate ?? date('Y-m-d') }}"
                       class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-blue-700 transition h-[38px]">
                <i class="fas fa-filter mr-2"></i> Filter Date
            </button>
            
            @if(request('start_date') || request('end_date'))
                <a href="{{ route('kinerja.pemeliharaan') }}" class="text-gray-500 text-sm hover:text-gray-700 underline mb-2">Reset</a>
            @endif
        </form>
    </div>

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

    <!-- Detailed Metrics Section (Tables based on Image) -->
    <div class="mt-8 grid grid-cols-1 gap-8 fade-in">
        
        <!-- WO Terbit & Complete Table -->
        <div class="table-card">
            <div class="chart-header">
                <div class="chart-title">WO Terbit & Complete</div>
            </div>
            <div class="table-wrapper">
                <table class="data-table text-center">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center bg-gray-100">BULAN</th>
                            <th rowspan="2" class="text-center bg-gray-100">TERBIT</th>
                            <th colspan="12" class="text-center bg-gray-100">COMPLETE (Closed in Month)</th>
                            <th rowspan="2" class="text-center bg-gray-100">OPEN (End of Month)</th>
                        </tr>
                        <tr>
                            <!-- 12 Months placeholder headers, or simplify to create vs complete vs open -->
                            <th colspan="12" class="text-center text-xs text-gray-500">Total Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyTrendDetailed as $row)
                        <tr>
                            <td class="font-semibold">{{ $row['period'] }}</td>
                            <td>{{ $row['created'] }}</td>
                            <td colspan="12" class="bg-gray-50">{{ $row['completed_in_month'] }}</td>
                            <td class="text-blue-600 font-bold hover:underline cursor-pointer">{{ $row['open_end_of_month'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- WO OPEN & Status Matrix -->
        <div class="table-card">
            <div class="chart-header">
                <div class="chart-title">WO OPEN & Status</div>
            </div>
            <div class="table-wrapper">
                <table class="data-table text-center text-xs">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="text-left">STATUS</th>
                            <th>CM</th><th>EM</th><th>WR</th><th>RTF</th><th>PM</th><th>PDM</th><th>EJ</th><th>PAM</th><th>CP</th><th>OH</th><th>ADM</th><th>OP</th><th>KOSONG</th>
                            <th class="bg-gray-300">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statusBreakdown as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="text-left font-semibold">{{ $row['status'] }}</td>
                            <td>{{ $row['CM'] }}</td>
                            <td>{{ $row['EM'] }}</td>
                            <td>{{ $row['WR'] }}</td>
                            <td>{{ $row['RTF'] }}</td>
                            <td>{{ $row['PM'] }}</td>
                            <td>{{ $row['PDM'] }}</td>
                            <td>{{ $row['EJ'] }}</td>
                            <td>{{ $row['PAM'] }}</td>
                            <td>{{ $row['CP'] }}</td>
                            <td>{{ $row['OH'] }}</td>
                            <td>{{ $row['ADM'] }}</td>
                            <td>{{ $row['OP'] }}</td>
                            <td>{{ $row['KOSONG'] }}</td>
                            <td class="font-bold text-blue-700">{{ $row['total'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Specific Compliance & Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- PM Compliance -->
            <div class="bg-white p-4 rounded shadow border">
                <h4 class="font-bold text-sm mb-2">WO PM Compliance, Complete MH, Complete Log</h4>
                <table class="w-full text-xs border">
                    <tr class="bg-gray-100 font-semibold"><td>WO PM Compliance</td><td>Jumlah WO PM</td><td>%</td></tr>
                    <tr>
                        <td class="text-purple-600 font-bold p-1">{{ $metrics['pm_compliance']['val'] }}</td>
                        <td class="p-1">{{ $metrics['pm_compliance']['total'] }}</td>
                        <td class="p-1">{{ $metrics['pm_compliance']['rate'] }}%</td>
                    </tr>
                </table>
            </div>

            <!-- Non PM Compliance -->
            <div class="bg-white p-4 rounded shadow border">
                <h4 class="font-bold text-sm mb-2">WO NON PM Compliance</h4>
                <table class="w-full text-xs border">
                    <tr class="bg-gray-100 font-semibold"><td>WO Non PM Compliance</td><td>Jumlah WO Non PM</td><td>%</td></tr>
                    <tr>
                        <td class="text-blue-600 font-bold p-1">{{ $metrics['non_pm_compliance']['val'] }}</td>
                        <td class="p-1">{{ $metrics['non_pm_compliance']['total'] }}</td>
                        <td class="p-1">{{ $metrics['non_pm_compliance']['rate'] }}%</td>
                    </tr>
                </table>
            </div>

            <!-- Approved <= 7 Days -->
            <div class="bg-white p-4 rounded shadow border">
                <h4 class="font-bold text-sm mb-2">WO Non PM Approval <= 7 hari</h4>
                <table class="w-full text-xs border">
                    <tr class="bg-gray-100 font-semibold"><td>WO Inplanning</td><td>Jumlah WO</td><td>%</td></tr>
                    <tr>
                        <td class="text-purple-600 font-bold p-1">{{ $metrics['non_pm_approval']['val'] }}</td>
                        <td class="p-1">{{ $metrics['non_pm_approval']['total'] }}</td>
                        <td class="p-1">{{ $metrics['non_pm_approval']['rate'] }}%</td>
                    </tr>
                </table>
            </div>

            <!-- Planned Backlog -->
            <div class="bg-white p-4 rounded shadow border">
                <h4 class="font-bold text-sm mb-2">WO Planned Backlog</h4>
                <table class="w-full text-xs border">
                    <tr class="bg-gray-100 font-semibold"><td>Planned Labor</td><td>Available MH/mg</td><td>Weeks</td></tr>
                    <tr>
                        <td class="text-blue-600 font-bold p-1">{{ $metrics['planned_backlog']['labor'] }}</td>
                        <td class="p-1">{{ $metrics['planned_backlog']['avail'] }}</td>
                        <td class="p-1">{{ $metrics['planned_backlog']['weeks'] }}</td>
                    </tr>
                </table>
            </div>

            <!-- Reactive Work -->
            <div class="bg-white p-4 rounded shadow border">
                <h4 class="font-bold text-sm mb-2">Reactive Work</h4>
                <table class="w-full text-xs border">
                    <tr class="bg-gray-100 font-semibold"><td>WO Non Taktikal</td><td>Jumlah Semua WO</td><td>%</td></tr>
                    <tr>
                        <td class="p-1">{{ $metrics['reactive_work']['val'] }}</td>
                        <td class="p-1">{{ $metrics['reactive_work']['total'] }}</td>
                        <td class="text-blue-600 font-bold p-1">{{ $metrics['reactive_work']['rate'] }}%</td>
                    </tr>
                </table>
            </div>

            <!-- WO Ageing Site -->
            <div class="bg-white p-4 rounded shadow border">
                <h4 class="font-bold text-sm mb-2">Jumlah WO Ageing Site</h4>
                <table class="w-full text-xs border">
                    <tr class="bg-gray-100 font-semibold"><td>WO Ageing</td><td>Total WO Open</td><td>%</td></tr>
                    <tr>
                        <td class="p-1">{{ $metrics['ageing_site']['val'] }}</td>
                        <td class="p-1">{{ $metrics['ageing_site']['total'] }}</td>
                        <td class="text-blue-600 font-bold p-1">{{ $metrics['ageing_site']['rate'] }}%</td>
                    </tr>
                </table>
            </div>

             <!-- SR Open -->
             <div class="bg-white p-4 rounded shadow border">
                <h4 class="font-bold text-sm mb-2">Jumlah SR Open</h4>
                <table class="w-full text-xs border">
                    <tr class="bg-gray-100 font-semibold"><td>SR Open</td><td>Total SR</td><td>%</td></tr>
                    <tr>
                        <td class="p-1">{{ $metrics['sr_open']['val'] }}</td>
                        <td class="p-1">{{ $metrics['sr_open']['total'] }}</td>
                        <td class="text-blue-600 font-bold p-1">{{ $metrics['sr_open']['rate'] }}%</td>
                    </tr>
                </table>
            </div>
            
        </div>

    </div>
</div>
