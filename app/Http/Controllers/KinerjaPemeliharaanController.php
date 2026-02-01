<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KinerjaPemeliharaanController extends Controller
{
    public function index(Request $request)
    {
        // Default range: Last 6 months if not provided
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : Carbon::now()->subMonths(6)->startOfDay();
            
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : Carbon::now()->endOfDay();

        // Get data for Kinerja Dashboard
        $kinerjaDashboardData = $this->getKinerjaDashboardData($startDate, $endDate);
        
        // Get data for KPI Dashboard
        $kpiDashboardData = $this->getKpiDashboardData($startDate, $endDate);
        
        // Merge all data
        $data = array_merge($kinerjaDashboardData, $kpiDashboardData);
        
        // Add date params for view as objects for format() calls in blade
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $data['filterStartDate'] = $startDate->format('Y-m-d'); // Keep for backward compatibility if needed
        $data['filterEndDate'] = $endDate->format('Y-m-d');
        
        return view('kinerja.index', $data);
    }

    public function detail(Request $request)
    {
        $type = $request->input('type');
        // Unify default range with index: Last 6 months if not provided
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : Carbon::now()->subMonths(6)->startOfDay();
            
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : Carbon::now()->endOfDay();

        $query = null;
        $title = "Detail Data";

        switch ($type) {
            case 'pm_compliance_total':
                $title = "Detail Jumlah WO PM";
                $query = DB::connection('oracle')->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->where('WORKTYPE', 'PM')
                    ->whereNotIn('STATUS', ['CAN', 'WSCH'])
                    ->whereBetween('REPORTDATE', [$startDate, $endDate]);
                break;
            case 'pm_compliance_val':
                $title = "Detail WO PM Compliant (Tepat/MH/Log)";
                $query = DB::connection('oracle')->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->where('WORKTYPE', 'PM')
                    ->whereIn('STATUS', ['COMP', 'CLOSE'])
                    ->whereBetween('REPORTDATE', [$startDate, $endDate])
                    ->whereNotNull('ACTFINISH')->whereNotNull('SCHEDSTART')->whereNotNull('SCHEDFINISH')->whereNotNull('ACTLABHRS')
                    ->whereRaw('ACTFINISH >= SCHEDSTART')->whereRaw('ACTFINISH <= SCHEDFINISH');
                break;
            case 'non_pm_compliance_total':
                $title = "Detail Jumlah WO Non PM (CM/EM)";
                $query = DB::connection('oracle')->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->whereIn('WORKTYPE', ['CM', 'EM'])
                    ->whereNotIn('STATUS', ['CAN', 'WSCH'])
                    ->whereBetween('REPORTDATE', [$startDate, $endDate]);
                break;
            case 'non_pm_compliance_val':
                $title = "Detail WO Non PM Selesai (COMP/CLOSE)";
                $query = DB::connection('oracle')->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->whereIn('WORKTYPE', ['CM', 'EM'])
                    ->whereIn('STATUS', ['COMP', 'CLOSE'])
                    ->whereBetween('REPORTDATE', [$startDate, $endDate]);
                break;
            case 'non_pm_approval_total':
                $title = "Detail Jumlah WO Approved (Approved/Comp/Close)";
                $query = DB::connection('oracle')->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->whereIn('WORKTYPE', ['CM', 'EM'])
                    ->whereIn('STATUS', ['APPR', 'COMP', 'CLOSE'])
                    ->whereBetween('REPORTDATE', [$startDate, $endDate]);
                break;
            case 'non_pm_approval_val':
                $title = "Detail WO Non PM Approved <= 7 Hari";
                $query = DB::connection('oracle')->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->whereIn('WORKTYPE', ['CM', 'EM'])
                    ->whereIn('STATUS', ['APPR', 'COMP', 'CLOSE'])
                    ->whereRaw("STATUSDATE - REPORTDATE <= 7")
                    ->whereBetween('REPORTDATE', [$startDate, $endDate]);
                break;
            case 'reactive_work_total':
                $title = "Detail Jumlah Semua WO (Reactive Basis)";
                $query = DB::connection('oracle')->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->whereBetween('REPORTDATE', [$startDate, $endDate]);
                break;
            case 'reactive_work_val':
                $title = "Detail WO Non Taktikal (EM/CR)";
                $query = DB::connection('oracle')->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->whereIn('WORKTYPE', ['EM', 'CR'])
                    ->whereBetween('REPORTDATE', [$startDate, $endDate]);
                break;
            case 'ageing_site_total':
                $title = "Detail Total WO Open (Ageing Basis)";
                $query = DB::connection('oracle')->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->whereIn('STATUS', ['WAPPR', 'APPR', 'INPRG'])
                    ->whereBetween('REPORTDATE', [$startDate, $endDate]);
                break;
            case 'ageing_site_val':
                $title = "Detail WO Ageing (> 365 Hari)";
                $query = DB::connection('oracle')->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->whereIn('STATUS', ['WAPPR', 'APPR', 'INPRG'])
                    ->where('REPORTDATE', '<=', Carbon::now()->subDays(365))
                    ->whereBetween('REPORTDATE', [$startDate, $endDate]);
                break;
            case 'sr_open_total':
                $title = "Detail Total SR";
                $query = DB::connection('oracle')->table('SR')
                    ->where('SITEID', 'KD')
                    ->whereBetween('REPORTDATE', [$startDate, $endDate]);
                break;
            case 'sr_open_val':
                $title = "Detail SR Open (QUEUED)";
                $query = DB::connection('oracle')->table('SR')
                    ->where('SITEID', 'KD')
                    ->where('STATUS', 'QUEUED')
                    ->whereBetween('REPORTDATE', [$startDate, $endDate]);
                break;
        }

        $results = $query ? $query->get() : collect();
        $isSR = str_contains($type, 'sr');

        return view('kinerja.detail', compact('results', 'title', 'isSR', 'startDate', 'endDate'));
    }
    
    private function getKinerjaDashboardData($startDate, $endDate)
    {
        // Query base builder
        $woQuery = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%');

        // Define status groups
        $closedStatuses = ['COMP', 'CLOSE'];
        $openStatuses = ['WAPPR', 'APPR', 'WSCH', 'WMATL', 'WPCOND', 'INPRG'];
        
        // PM Calculations (Closed within period)
        $pmClosed = (clone $woQuery)
            ->where('WORKTYPE', 'PM')
            ->whereIn('STATUS', $closedStatuses)
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();

        // PM Open (Created within period AND still open OR open status at end of period? 
        // Consistent with previous logic: Created >= startDate and Status IN Open)
        $pmOpen = (clone $woQuery)
            ->where('WORKTYPE', 'PM')
            ->whereIn('STATUS', $openStatuses)
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
            
        $pmTotal = $pmClosed + $pmOpen;
        $pmPercentage = $pmTotal > 0 ? round(($pmClosed / $pmTotal) * 100, 1) : 0;
        
        // CM Calculations
        $cmClosed = (clone $woQuery)
            ->where('WORKTYPE', 'CM')
            ->whereIn('STATUS', $closedStatuses)
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
            
        $cmOpen = (clone $woQuery)
            ->where('WORKTYPE', 'CM')
            ->whereIn('STATUS', $openStatuses)
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
            
        $cmTotal = $cmClosed + $cmOpen;
        $cmPercentage = $cmTotal > 0 ? round(($cmClosed / $cmTotal) * 100, 1) : 0;
        
        // Total stats
        $totalWOCount = $pmTotal + $cmTotal;
        $totalClosed = $pmClosed + $cmClosed;
        $totalOpen = $pmOpen + $cmOpen;
        
        // Get data per Location (using as Unit)
        // We need all locations to group them via PHP, so removing limit(10) and grouping in application layer
        $rawUnitData = (clone $woQuery)
            ->select('LOCATION', 
                DB::raw("SUM(CASE WHEN WORKTYPE = 'PM' AND STATUS IN ('" . implode("','", $closedStatuses) . "') THEN 1 ELSE 0 END) as pm_closed"),
                DB::raw("SUM(CASE WHEN WORKTYPE = 'PM' AND STATUS IN ('" . implode("','", $openStatuses) . "') THEN 1 ELSE 0 END) as pm_open"),
                DB::raw("SUM(CASE WHEN WORKTYPE = 'CM' AND STATUS IN ('" . implode("','", $closedStatuses) . "') THEN 1 ELSE 0 END) as cm_closed"),
                DB::raw("SUM(CASE WHEN WORKTYPE = 'CM' AND STATUS IN ('" . implode("','", $openStatuses) . "') THEN 1 ELSE 0 END) as cm_open"))
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->whereNotNull('LOCATION')
            ->groupBy('LOCATION')
            ->get();
        
        // Define groupings
        $groupDefinitions = [
            'ULPLTD KOLAKA' => ['KLKA', 'LANI', 'SABI', 'MIKU'],
            'ULPLTD BAU-BAU' => ['BBAU', 'RAHA', 'WANG', 'EREK', 'RONG', 'WINN'],
            'ULPLTD POASIA' => ['POAS'],
            'ULPLTD WUA-WUA' => ['WUAW', 'LANG']
        ];

        $groupedStats = [];
        // Initialize groups
        foreach ($groupDefinitions as $groupName => $codes) {
            $groupedStats[$groupName] = [
                'pm_closed' => 0, 'pm_open' => 0,
                'cm_closed' => 0, 'cm_open' => 0,
                'total_closed' => 0
            ];
        }

        // Process aggregation
        foreach ($rawUnitData as $row) {
            $loc = strtoupper($row->location);
            foreach ($groupDefinitions as $groupName => $codes) {
                foreach ($codes as $code) {
                    if (str_contains($loc, $code)) {
                        $groupedStats[$groupName]['pm_closed'] += $row->pm_closed;
                        $groupedStats[$groupName]['pm_open'] += $row->pm_open;
                        $groupedStats[$groupName]['cm_closed'] += $row->cm_closed;
                        $groupedStats[$groupName]['cm_open'] += $row->cm_open;
                        $groupedStats[$groupName]['total_closed'] += ($row->pm_closed + $row->cm_closed);
                        break 2; // Match found, move to next row
                    }
                }
            }
        }

        // Sort by Total Closed Descending
        uasort($groupedStats, function($a, $b) {
            return $b['total_closed'] <=> $a['total_closed'];
        });

        // Prepare arrays for view
        $unitNames = array_keys($groupedStats);
        $pmClosedPerUnit = [];
        $pmOpenPerUnit = [];
        $cmClosedPerUnit = [];
        $cmOpenPerUnit = [];

        // Find best performing group
        $bestPerformingUnit = '-';
        $maxRate = -1;

        foreach ($groupedStats as $name => $stats) {
            $pmClosedPerUnit[] = $stats['pm_closed'];
            $pmOpenPerUnit[] = $stats['pm_open'];
            $cmClosedPerUnit[] = $stats['cm_closed'];
            $cmOpenPerUnit[] = $stats['cm_open'];

            $uTotal = $stats['pm_closed'] + $stats['pm_open'] + $stats['cm_closed'] + $stats['cm_open'];
            $uClosed = $stats['pm_closed'] + $stats['cm_closed'];
            
            $rate = $uTotal > 0 ? ($uClosed / $uTotal) * 100 : 0;
            
            if ($rate > $maxRate && $uTotal > 0) {
                $maxRate = $rate;
                $bestPerformingUnit = $name;
            }
        }
        
        // Get monthly trend
        $monthlyTrend = [];
        $periodIterator = $startDate->copy()->startOfMonth();
        $endPeriod = $endDate->copy()->endOfMonth();
        
        // Protect against too long periods or infinite loops, limit to 24 months for chart safety
        $monthsDiff = $periodIterator->diffInMonths($endPeriod);
        if ($monthsDiff > 24) {
            $periodIterator = $endPeriod->copy()->subMonths(24)->startOfMonth();
        }

        while ($periodIterator <= $endPeriod) {
            $mStart = $periodIterator->copy()->startOfMonth();
            $mEnd = $periodIterator->copy()->endOfMonth();
            
            $pmMonthly = (clone $woQuery)
                ->where('WORKTYPE', 'PM')
                ->whereIn('STATUS', $closedStatuses)
                ->whereBetween('REPORTDATE', [$mStart, $mEnd])
                ->count();
            
            $cmMonthly = (clone $woQuery)
                ->where('WORKTYPE', 'CM')
                ->whereIn('STATUS', $closedStatuses)
                ->whereBetween('REPORTDATE', [$mStart, $mEnd])
                ->count();
                
            $monthlyTrend[] = [
                'label' => $mStart->format('M Y'),
                'pm' => $pmMonthly,
                'cm' => $cmMonthly
            ];
            
            $periodIterator->addMonth();
        }
        
        // Monthly Trend Detailed (Create vs Complete)
        $monthlyTrendDetailed = $this->getMonthlyTrendDetailed($woQuery, $closedStatuses, $startDate, $endDate); 

        // Status Breakdown (Respect date range)
        $statusBreakdown = $this->getStatusBreakdown($woQuery, $startDate, $endDate);

        // Additional Metrics (Respect date range)
        $metrics = $this->getAdditionalMetrics($woQuery, $closedStatuses, $openStatuses, $startDate, $endDate);

        return compact(
            'totalWOCount', 'totalClosed', 'totalOpen',
            'pmClosed', 'pmOpen', 'pmTotal', 'pmPercentage',
            'cmClosed', 'cmOpen', 'cmTotal', 'cmPercentage',
            'unitNames', 'pmClosedPerUnit', 'pmOpenPerUnit', 'cmClosedPerUnit', 'cmOpenPerUnit',
            'bestPerformingUnit', 'maxRate', 'monthlyTrend',
            'monthlyTrendDetailed', 'statusBreakdown', 'metrics'
        );
    }

    private function getMonthlyTrendDetailed($woQuery, $closedStatuses, $startDate, $endDate)
    {
        $months = [];
        $periodIterator = $startDate->copy()->startOfMonth();
        $endPeriod = $endDate->copy()->endOfMonth();
        
        // Protect against too long periods, limit to 18 months for this detailed table
        $monthsDiff = $periodIterator->diffInMonths($endPeriod);
        if ($monthsDiff > 18) {
            $periodIterator = $endPeriod->copy()->subMonths(18)->startOfMonth();
        }

        while ($periodIterator <= $endPeriod) {
            $months[] = $periodIterator->copy();
            $periodIterator->addMonth();
        }

        $trendData = [];
        foreach ($months as $month) {
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            $monthKey = $month->format('Ym'); // e.g. 202501
            
            // WO Terbit (Created)
            $created = (clone $woQuery)
                ->whereBetween('REPORTDATE', [$monthStart, $monthEnd])
                ->count();

            // WO Complete (Closed)
            $completed = (clone $woQuery)
                ->whereIn('STATUS', $closedStatuses)
                ->whereBetween('REPORTDATE', [$monthStart, $monthEnd])
                ->count();
            
            // Open (Accumulated until end of this month - simplified)
            // Real logic would be: Created before end of month AND (Not Closed OR Closed after end of month)
            $open = (clone $woQuery)
                ->where('REPORTDATE', '<=', $monthEnd)
                ->where(function($q) use ($monthEnd, $closedStatuses, $monthStart) {
                    $q->whereNotIn('STATUS', $closedStatuses)
                      ->orWhere('REPORTDATE', '>', $monthStart); // Simplified for trend
                })
                ->count();

            // Breakdown Completion by month relative to creation (simplified for now as just total completed in that month)
            // A clearer matrix like the image requires tracking "When was a WO created in Month X completed?" 
            // For now, we return simpler "Created vs Completed in Month" stats.
            
            $trendData[] = [
                'month_name' => $month->format('M'), // Jan
                'period' => $monthKey,
                'created' => $created,
                'completed_in_month' => $completed, // This is total completed in this month, regardless of creation
                'open_end_of_month' => $open
            ];
        }
        
        return $trendData;
    }

    private function getStatusBreakdown($woQuery, $startDate, $endDate)
    {
        // Columns from the image: CM, EM, WR, RTF, PM, PDM, EJ, PAM, CP, OH, ADM, OP, KOSONG
        $types = ['CM', 'EM', 'WR', 'RTF', 'PM', 'PDM', 'EJ', 'PAM', 'CP', 'OH', 'ADM', 'OP'];
        
        // Rows from image: APPR, CLOSE, COMP, INPRG, WAPPR, WENG, WJOBCARD, WMATL, WMATSHUT, WPROC, WSCH
        $statuses = ['APPR', 'CLOSE', 'COMP', 'INPRG', 'WAPPR', 'WENG', 'WJOBCARD', 'WMATL', 'WMATSHUT', 'WPROC', 'WSCH'];
        
        $matrix = [];
        
        // Initialize matrix
        foreach ($statuses as $status) {
            $row = ['status' => $status, 'total' => 0];
            foreach ($types as $type) {
                $row[$type] = 0;
            }
            $row['KOSONG'] = 0; // For worktype null/empty
            $matrix[$status] = $row;
        }

        // Aggregate query (Filtered by Range: Either STATUSDATE or REPORTDATE in range)
        $data = (clone $woQuery)
            ->select('STATUS', 'WORKTYPE', DB::raw('COUNT(*) as count'))
            ->whereIn('STATUS', $statuses)
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->groupBy('STATUS', 'WORKTYPE')
            ->get();

        foreach ($data as $d) {
            $s = $d->status; // Uppercase from DB usually
            $t = $d->worktype;
            
            if (isset($matrix[$s])) {
                if ($t && in_array($t, $types)) {
                    $matrix[$s][$t] += $d->count;
                } else {
                    $matrix[$s]['KOSONG'] += $d->count;
                }
                $matrix[$s]['total'] += $d->count;
            }
        }

        return array_values($matrix);
    }

    private function getAdditionalMetrics($woQuery, $closedStatuses, $openStatuses, $startDate, $endDate)
    {
        // 1. PM Compliance
        // Numerator: Compliant & Closed (Only closed/comp can have actuals)
        $pmCompliant = (clone $woQuery)
            ->where('WORKTYPE', 'PM')
            ->whereIn('STATUS', $closedStatuses)
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->whereNotNull('ACTFINISH')->whereNotNull('SCHEDSTART')->whereNotNull('SCHEDFINISH')->whereNotNull('ACTLABHRS')
            ->whereRaw('ACTFINISH >= SCHEDSTART')->whereRaw('ACTFINISH <= SCHEDFINISH')
            ->count();
        // Denominator: Total PM Issued in Period (Open + Closed), excluding CAN/WSCH
        $pmTotal = (clone $woQuery)
            ->where('WORKTYPE', 'PM')
            ->whereNotIn('STATUS', ['CAN', 'WSCH'])
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
        $pmComplianceRate = $pmTotal > 0 ? round(($pmCompliant / $pmTotal) * 100, 2) : 0;

        // 2. Non PM Compliance
        $nonPmTotal = (clone $woQuery)
            ->whereIn('WORKTYPE', ['CM', 'EM'])
            ->whereNotIn('STATUS', ['CAN', 'WSCH'])
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
        $nonPmCompliant = (clone $woQuery)
            ->whereIn('WORKTYPE', ['CM', 'EM'])
            ->whereIn('STATUS', $closedStatuses)
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
        $nonPmComplianceRate = $nonPmTotal > 0 ? round(($nonPmCompliant / $nonPmTotal) * 100, 2) : 0;

        // 3. Non PM Approval <= 7 Days
        $nonPmApproved = (clone $woQuery)
            ->whereIn('WORKTYPE', ['CM', 'EM'])
            ->whereIn('STATUS', ['APPR', 'COMP', 'CLOSE'])
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
        $nonPmFastAppr = (clone $woQuery)
            ->whereIn('WORKTYPE', ['CM', 'EM'])
            ->whereIn('STATUS', ['APPR', 'COMP', 'CLOSE'])
            ->whereRaw("STATUSDATE - REPORTDATE <= 7")
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
        $nonPmFastApprRate = $nonPmApproved > 0 ? round(($nonPmFastAppr / $nonPmApproved) * 100, 2) : 0;

        // 4. Planned Backlog (Manhours)
        $backlogHrs = (clone $woQuery)->whereIn('STATUS', ['WAPPR', 'WSCH', 'WMATL'])->whereBetween('REPORTDATE', [$startDate, $endDate])->sum('ESTLABHRS');
        $availableHrs = 2660; // From image example
        $backlogWeeks = $availableHrs > 0 ? round($backlogHrs / $availableHrs, 2) : 0;

        // 5. Wrench Time (Placeholder)
        $wrenchTime = 83.68;

        // 6. Reactive Work
        // Logic: Non-Tactical / Total WO in Period
        // Non-Tactical: EM, CR
        $nonTactical = (clone $woQuery)->whereIn('WORKTYPE', ['EM', 'CR'])->whereBetween('REPORTDATE', [$startDate, $endDate])->count();
        $allWo = (clone $woQuery)->whereBetween('REPORTDATE', [$startDate, $endDate])->count();
        $reactiveRate = $allWo > 0 ? round(($nonTactical / $allWo) * 100, 2) : 0;

        // 7. WO Ageing Site (> 365 days open) -- Snapshot
        $ageingSite = (clone $woQuery)->whereIn('STATUS', ['WAPPR', 'APPR', 'INPRG'])
            ->where('REPORTDATE', '<=', Carbon::now()->subDays(365))
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
        $totalOpen = (clone $woQuery)->whereIn('STATUS', ['WAPPR', 'APPR', 'INPRG'])->whereBetween('REPORTDATE', [$startDate, $endDate])->count();
        $ageingSiteRate = $totalOpen > 0 ? round(($ageingSite / $totalOpen) * 100, 2) : 0;

        // 8. WO Ageing OH (Overhaul) -- Snapshot
        $ageingOh = (clone $woQuery)->where('WORKTYPE', 'OH')->whereIn('STATUS', ['WAPPR', 'APPR', 'INPRG'])
            ->where('REPORTDATE', '<=', Carbon::now()->subDays(365))
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
        $totalOhOpen = (clone $woQuery)->where('WORKTYPE', 'OH')->whereIn('STATUS', ['WAPPR', 'APPR', 'INPRG'])->whereBetween('REPORTDATE', [$startDate, $endDate])->count();
        $ageingOhRate = $totalOhOpen > 0 ? round(($ageingOh / $totalOhOpen) * 100, 2) : 0;
        
        // 9. SR Open -- Snapshot within range
        $srOpen = DB::connection('oracle')->table('SR')->where('SITEID', 'KD')->whereIn('STATUS', ['QUEUED'])->whereBetween('REPORTDATE', [$startDate, $endDate])->count();
        $srTotal = DB::connection('oracle')->table('SR')->where('SITEID', 'KD')->whereBetween('REPORTDATE', [$startDate, $endDate])->count();
        $srOpenRate = $srTotal > 0 ? round(($srOpen / $srTotal) * 100, 2) : 0;

        return [
            'pm_compliance' => ['val' => $pmCompliant, 'total' => $pmTotal, 'rate' => $pmComplianceRate],
            'non_pm_compliance' => ['val' => $nonPmCompliant, 'total' => $nonPmTotal, 'rate' => $nonPmComplianceRate],
            'non_pm_approval' => ['val' => $nonPmFastAppr, 'total' => $nonPmApproved, 'rate' => $nonPmFastApprRate],
            'planned_backlog' => ['labor' => $backlogHrs, 'avail' => $availableHrs, 'weeks' => $backlogWeeks],
            'wrench_time' => $wrenchTime,
            'reactive_work' => ['val' => $nonTactical, 'total' => $allWo, 'rate' => $reactiveRate],
            'mttr' => '-', // Placeholder
            'non_pm_jobplan' => ['val' => 0, 'total' => 56, 'rate' => 0], // Placeholder
            'non_pm_aptw' => ['val' => 45, 'total' => 53, 'rate' => 84.91], // Placeholder
            'ageing_site' => ['val' => $ageingSite, 'total' => $totalOpen, 'rate' => $ageingSiteRate],
            'ageing_oh' => ['val' => $ageingOh, 'total' => $totalOhOpen, 'rate' => $ageingOhRate],
            'sr_open' => ['val' => $srOpen, 'total' => $srTotal, 'rate' => $srOpenRate],
        ];
    }
    
    private function getKpiDashboardData($startDate, $endDate)
    {
        // I6.6 - PM Compliance
        $pmCompliance = $this->calculatePmCompliance($startDate, $endDate);
        
        // I6.7 - WO Planned Backlog
        $plannedBacklog = $this->calculatePlannedBacklog($startDate, $endDate);
        
        // I6.8 - Schedule Compliance (Non Tactical)
        $scheduleCompliance = $this->calculateScheduleCompliance($startDate, $endDate);
        
        // I6.9 - Rework (Jaminan Kualitas)
        $rework = $this->calculateRework($startDate, $endDate);
        
        // I6.10.1 - Reactive Work
        $reactiveWork = $this->calculateReactiveWork($startDate, $endDate);
        
        // I6.10.2 - WR/SR Open/Queued
        $wrSrOpen = $this->calculateWrSrOpen($startDate, $endDate);
        
        // I6.10.3 - WO Ageing
        $woAgeing = $this->calculateWoAgeing($startDate, $endDate);
        
        // I6.10.4 - Post Implementation Review
        $postImplReview = $this->calculatePostImplReview($startDate, $endDate);
        
        return compact(
            'pmCompliance',
            'plannedBacklog',
            'scheduleCompliance',
            'rework',
            'reactiveWork',
            'wrSrOpen',
            'woAgeing',
            'postImplReview'
        );
    }
    
    // I6.6 - PM Compliance
    private function calculatePmCompliance($startDate, $endDate)
    {
        // Numerator: Compliant & Closed (Only closed/comp can have actuals)
        $pmCompliant = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->where('WORKTYPE', 'PM')
            ->whereIn('STATUS', ['COMP', 'CLOSE'])
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->whereNotNull('ACTFINISH')->whereNotNull('SCHEDSTART')->whereNotNull('SCHEDFINISH')->whereNotNull('ACTLABHRS')
            ->whereRaw('ACTFINISH >= SCHEDSTART')->whereRaw('ACTFINISH <= SCHEDFINISH')
            ->count();
            
        // Denominator: Total PM Issued in Period (Open + Closed), excluding CAN/WSCH
        $pmTotal = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->where('WORKTYPE', 'PM')
            ->whereNotIn('STATUS', ['CAN', 'WSCH'])
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
            
        $percentage = $pmTotal > 0 ? round(($pmCompliant / $pmTotal) * 100, 2) : 0;
        
        // Level Determination
        $level = 1;
        $desc = "PM Compliance 0 - 70%";
        if ($percentage == 100) { $level = 5; $desc = "PM Compliance = 100%"; }
        elseif ($percentage > 90) { $level = 4; $desc = "PM Compliance > 90%"; }
        elseif ($percentage > 80) { $level = 3; $desc = "PM Compliance > 80%"; }
        elseif ($percentage > 70) { $level = 2; $desc = "PM Compliance > 70%"; }
        
        return [
            'total' => $pmTotal,
            'compliant' => $pmCompliant,
            'percentage' => $percentage,
            'level' => $level,
            'description' => $desc
        ];
    }
    
    // I6.7 - WO Planned Backlog
    private function calculatePlannedBacklog($startDate, $endDate)
    {
        // Planned Backlog (Manhours)
        $backlogHrs = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->whereIn('STATUS', ['WAPPR', 'WSCH', 'WMATL'])
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->sum('ESTLABHRS');
            
        $availableHrs = 2660; // From image example
        $weeks = $availableHrs > 0 ? round($backlogHrs / $availableHrs, 2) : 0;
        
        // Level Determination
        $level = 1;
        $desc = "≥ 8 Minggu";
        if ($weeks < 4) { $level = 5; $desc = "< 4 Minggu"; }
        elseif ($weeks < 6) { $level = 4; $desc = "4 - < 6 Minggu"; }
        elseif ($weeks < 8) { $level = 3; $desc = "6 - < 8 Minggu"; }
        elseif ($weeks >= 8) { $level = 2; $desc = "≥ 8 Minggu"; }
        
        // Level 1 if no data or very high
        if ($backlogHrs == 0) { $level = 1; $desc = "Tidak terukur/tidak ada data"; }
        
        return [
            'total_manhours' => $backlogHrs,
            'planned_hours' => $backlogHrs,
            'ready_hours' => 0,
            'weeks' => $weeks,
            'level' => $level,
            'description' => $desc
        ];
    }
    
    // I6.8 - Schedule Compliance (Non Tactical)
    private function calculateScheduleCompliance($startDate, $endDate)
    {
        // Non Tactical: CR, EM, EJ, NM, SF
        $nonTacticalTypes = ['CR', 'EM', 'EJ', 'NM', 'SF'];
        
        $woBase = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->whereIn('WORKTYPE', $nonTacticalTypes)
            ->whereIn('STATUS', ['COMP', 'CLOSE'])
            ->whereBetween('REPORTDATE', [$startDate, $endDate]);
            
        $totalNonTactical = (clone $woBase)->count();
        
        $compliant = (clone $woBase)
            ->whereNotNull('ACTFINISH')
            ->whereNotNull('SCHEDSTART')
            ->whereNotNull('SCHEDFINISH')
            ->whereNotNull('ACTLABHRS')
            ->whereRaw('ACTFINISH >= SCHEDSTART')
            ->whereRaw('ACTFINISH <= SCHEDFINISH')
            ->count();
            
        $percentage = $totalNonTactical > 0 ? round(($compliant / $totalNonTactical) * 100, 2) : 0;
        
        // Level Determination
        $level = 1;
        $desc = "0% - 30%";
        if ($percentage > 80) { $level = 5; $desc = "> 80%"; }
        elseif ($percentage > 70) { $level = 4; $desc = "> 70% - ≤ 80%"; }
        elseif ($percentage > 50) { $level = 3; $desc = "> 50% - ≤ 70%"; }
        elseif ($percentage > 30) { $level = 2; $desc = "> 30% - ≤ 50%"; }
        
        return [
            'total' => $totalNonTactical,
            'compliant' => $compliant,
            'percentage' => $percentage,
            'level' => $level,
            'description' => $desc
        ];
    }
    
    // I6.9 - Rework
    private function calculateRework($startDate, $endDate)
    {
        // Rework: Repeated WO (same Asset, same Problem) within 30 days
        // Scope: CM, EM (User requested CM and EM, previously code used CR and EM, assuming CM/EM based on request)
        // Note: Code usually treats CM similar to CR (Corrective). User said "CM dan EM". 
        // Checking previous code: 'non_pm_compliance' uses CM, EM. 'reactive_work' uses EM, CR.
        // Let's stick to CM, EM as requested.
        
        // Denominator: Total WO (CM and EM)
        $totalCmEm = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->whereIn('WORKTYPE', ['CM', 'EM'])
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
            
        // Numerator: WO that has a predecessor within 30 days
        $reworkCount = DB::connection('oracle')->table('WORKORDER as W1')
            ->where('W1.SITEID', 'KD')
            ->where('W1.WONUM', 'LIKE', 'WO%')
            ->whereIn('W1.WORKTYPE', ['CM', 'EM'])
            ->whereBetween('W1.REPORTDATE', [$startDate, $endDate])
            ->whereNotNull('W1.ASSETNUM')
            ->whereNotNull('W1.PROBLEMCODE')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('WORKORDER as W2')
                    ->whereColumn('W2.ASSETNUM', 'W1.ASSETNUM')
                    ->whereColumn('W2.PROBLEMCODE', 'W1.PROBLEMCODE')
                    ->where('W2.SITEID', 'KD')
                    ->where('W2.WONUM', 'LIKE', 'WO%')
                    ->whereIn('W2.WORKTYPE', ['CM', 'EM'])
                    ->whereColumn('W2.REPORTDATE', '<', 'W1.REPORTDATE')
                    ->whereRaw('W2.REPORTDATE >= W1.REPORTDATE - 30');
            })
            ->count();

        $percentage = $totalCmEm > 0 ? round(($reworkCount / $totalCmEm) * 100, 2) : 0;
        
        // Level
        $level = 1;
        $desc = "> 20%";
        if ($percentage <= 5) { $level = 5; $desc = "≤ 5%"; }
        elseif ($percentage <= 10) { $level = 4; $desc = "5% < - 10%"; }
        elseif ($percentage <= 15) { $level = 3; $desc = "10% < - 15%"; }
        elseif ($percentage <= 20) { $level = 2; $desc = "15% < - 20%"; }
        
        return [
            'total' => $totalCmEm,
            'rework' => $reworkCount,
            'percentage' => $percentage,
            'level' => $level,
            'description' => $desc
        ];
    }
    
    // I6.10.1 - Reactive Work
    private function calculateReactiveWork($startDate, $endDate)
    {
        // 6. Reactive Work
        // Logic: Non-Tactical / Total WO in Period
        // Non-Tactical: EM, CR
        $nonTactical = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->whereIn('WORKTYPE', ['EM', 'CR'])
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
            
        $allWo = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
            
        $percentage = $allWo > 0 ? round(($nonTactical / $allWo) * 100, 2) : 0;
        
        // Level
        $level = 1; 
        $desc = "> 20%";
        if ($percentage <= 5) { $level = 5; $desc = "≤ 5%"; }
        elseif ($percentage <= 10) { $level = 4; $desc = "5% < - 10%"; }
        elseif ($percentage <= 15) { $level = 3; $desc = "10% < - 15%"; }
        elseif ($percentage <= 20) { $level = 2; $desc = "15% < - 20%"; }
        
        return [
            'tactical_closed' => 0, // Unused in new logic
            'non_tactical' => $nonTactical,
            'percentage' => $percentage,
            'level' => $level,
            'description' => $desc
        ];
    }
    
    // I6.10.2 - WR/SR Open/Queued
    private function calculateWrSrOpen($startDate, $endDate)
    {
        // 9. SR Open -- Snapshot within range
        $srOpen = DB::connection('oracle')->table('SR')
            ->where('SITEID', 'KD')
            ->whereIn('STATUS', ['QUEUED'])
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
            
        $srTotal = DB::connection('oracle')->table('SR')
            ->where('SITEID', 'KD')
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
            
        $percentage = $srTotal > 0 ? round(($srOpen / $srTotal) * 100, 2) : 0;
        
        // Level
        $level = 1;
        $desc = "> 5%";
        if ($percentage == 0) { $level = 5; $desc = "0%"; }
        elseif ($percentage <= 1) { $level = 4; $desc = "≤ 1%"; }
        elseif ($percentage <= 2) { $level = 3; $desc = "≤ 2%"; }
        elseif ($percentage <= 5) { $level = 2; $desc = "≤ 5%"; }
        
        return [
            'total' => $srTotal,
            'overdue' => $srOpen,
            'percentage' => $percentage,
            'level' => $level,
            'description' => $desc
        ];
    }
    
    // I6.10.3 - WO Ageing
    private function calculateWoAgeing($startDate, $endDate)
    {
        // 7. WO Ageing Site (> 365 days open) -- Snapshot
        // Statuses: WAPPR, APPR, INPRG
        
        $statuses = ['WAPPR', 'APPR', 'INPRG'];
        
        $totalOpen = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->whereIn('STATUS', $statuses)
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();

        $ageingSite = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->whereIn('STATUS', $statuses)
            ->where('REPORTDATE', '<=', Carbon::now()->subDays(365))
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
            
        $percentage = $totalOpen > 0 ? round(($ageingSite / $totalOpen) * 100, 2) : 0;
        
        // Level
        $level = 1;
        $desc = "> 20%";
        if ($percentage <= 5) { $level = 5; $desc = "≤ 5%"; }
        elseif ($percentage <= 10) { $level = 4; $desc = "5% < - 10%"; }
        elseif ($percentage <= 15) { $level = 3; $desc = "10% < - 15%"; }
        elseif ($percentage <= 20) { $level = 2; $desc = "15% < - 20%"; }
        
        return [
            'total_open' => $totalOpen,
            'old_open' => $ageingSite,
            'percentage' => $percentage,
            'level' => $level,
            'description' => $desc
        ];
    }
    
    // I6.10.4 - Post Implementation Review
    private function calculatePostImplReview($startDate, $endDate)
    {
        // Placeholder for Project Review
        
        $totalProjects = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->whereIn('WORKTYPE', ['PJ', 'AI']) 
            ->whereIn('STATUS', ['COMP', 'CLOSE'])
            ->whereBetween('REPORTDATE', [$startDate, $endDate])
            ->count();
            
        $reviewed = 0; 
        $percentage = 0;
        
        $level = 1;
        $desc = "Data tidak tersedia";
        
        return [
            'total' => $totalProjects,
            'reviewed' => $reviewed,
            'percentage' => $percentage,
            'level' => $level,
            'description' => $desc
        ];
    }
}