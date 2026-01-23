<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KinerjaPemeliharaanController extends Controller
{
    public function index()
    {
        // Get data for Kinerja Dashboard
        $kinerjaDashboardData = $this->getKinerjaDashboardData();
        
        // Get data for KPI Dashboard
        $kpiDashboardData = $this->getKpiDashboardData();
        
        // Merge all data
        $data = array_merge($kinerjaDashboardData, $kpiDashboardData);
        
        return view('kinerja.index', $data);
    }
    
    private function getKinerjaDashboardData()
    {
        // Get data for 6 months
        $startDate = Carbon::now()->subMonths(6);
        
        // Query base builder
        $woQuery = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD');

        // Get PM and CM counts (Closed)
        // Assuming 'COMP' and 'CLOSE' are closed statuses in Maximo
        $closedStatuses = ['COMP', 'CLOSE'];
        
        $pmCount = (clone $woQuery)
            ->where('WORKTYPE', 'PM')
            ->whereIn('STATUS', $closedStatuses)
            ->where('STATUSDATE', '>=', $startDate)
            ->count();
        
        $cmCount = (clone $woQuery)
            ->where('WORKTYPE', 'CM')
            ->whereIn('STATUS', $closedStatuses)
            ->where('STATUSDATE', '>=', $startDate)
            ->count();
        
        $totalWO = $pmCount + $cmCount;
        
        // Calculate percentages
        $pmPercentage = $totalWO > 0 ? round(($pmCount / $totalWO) * 100, 1) : 0;
        $cmPercentage = $totalWO > 0 ? round(($cmCount / $totalWO) * 100, 1) : 0;
        
        // Calculate PM/CM Ratio
        $pmCmRatio = $cmCount > 0 ? round($pmCount / $cmCount, 2) : 0;
        
        // Get data per Location (using as Unit)
        // Clean location (take first part if needed) or just raw location
        // Using raw query for grouping as it's more reliable across DB drivers for aggregates
        $unitData = (clone $woQuery)
            ->select('LOCATION', 
                DB::raw("SUM(CASE WHEN WORKTYPE = 'PM' THEN 1 ELSE 0 END) as pm_count"),
                DB::raw("SUM(CASE WHEN WORKTYPE = 'CM' THEN 1 ELSE 0 END) as cm_count"),
                DB::raw("COUNT(*) as total_count"))
            ->whereIn('STATUS', $closedStatuses)
            ->where('STATUSDATE', '>=', $startDate)
            ->whereNotNull('LOCATION')
            ->groupBy('LOCATION')
            ->orderBy('total_count', 'desc')
            ->limit(10) // Limit to top 10 locations to avoid clutter
            ->get();
        
        $unitNames = $unitData->pluck('location')->toArray();
        $pmPerUnit = $unitData->pluck('pm_count')->toArray();
        $cmPerUnit = $unitData->pluck('cm_count')->toArray();
        $totalPerUnit = $unitData->pluck('total_count')->toArray();
        
        // Find best performing unit (Highest PM/CM Ratio)
        $bestPerformingUnit = '-';
        $maxRatio = 0;
        foreach ($unitData as $unit) {
            $ratio = $unit->cm_count > 0 ? $unit->pm_count / $unit->cm_count : 0;
            if ($ratio > $maxRatio) {
                $maxRatio = $ratio;
                $bestPerformingUnit = $unit->location;
            }
        }
        
        // Get monthly trend
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $pmMonthly = (clone $woQuery)
                ->where('WORKTYPE', 'PM')
                ->whereIn('STATUS', $closedStatuses)
                ->whereBetween('STATUSDATE', [$monthStart, $monthEnd])
                ->count();
            
            $cmMonthly = (clone $woQuery)
                ->where('WORKTYPE', 'CM')
                ->whereIn('STATUS', $closedStatuses)
                ->whereBetween('STATUSDATE', [$monthStart, $monthEnd])
                ->count();
            
            $monthlyTrend[] = [
                'label' => $month->format('M Y'),
                'pm' => $pmMonthly,
                'cm' => $cmMonthly
            ];
        }
        
        return compact(
            'totalWO', 'pmCount', 'cmCount', 'pmPercentage', 'cmPercentage', 
            'pmCmRatio', 'unitNames', 'pmPerUnit', 'cmPerUnit', 'totalPerUnit',
            'bestPerformingUnit', 'maxRatio', 'monthlyTrend'
        );
    }
    
    private function getKpiDashboardData()
    {
        // I6.6 - PM Compliance
        $pmCompliance = $this->calculatePmCompliance();
        
        // I6.7 - WO Planned Backlog
        $plannedBacklog = $this->calculatePlannedBacklog();
        
        // I6.8 - Schedule Compliance (Non Tactical)
        $scheduleCompliance = $this->calculateScheduleCompliance();
        
        // I6.9 - Rework (Jaminan Kualitas)
        $rework = $this->calculateRework();
        
        // I6.10.1 - Reactive Work
        $reactiveWork = $this->calculateReactiveWork();
        
        // I6.10.2 - WR/SR Open/Queued
        $wrSrOpen = $this->calculateWrSrOpen();
        
        // I6.10.3 - WO Ageing
        $woAgeing = $this->calculateWoAgeing();
        
        // I6.10.4 - Post Implementation Review
        $postImplReview = $this->calculatePostImplReview();
        
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
    private function calculatePmCompliance()
    {
        // PM Compliance filters:
        // 1. Maintenance Type = PM
        // 2. Status = COMP or CLOSE
        
        $woBase = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WORKTYPE', 'PM')
            ->whereIn('STATUS', ['COMP', 'CLOSE']);
            
        $totalPmClosed = (clone $woBase)->count();
        
        // Compliant if:
        // 1. ActFinish between SchedStart and SchedFinish
        // 2. Has Actual Date, Completion (assumed status), Man Hour
        $pmCompliant = (clone $woBase)
            ->whereNotNull('ACTFINISH')
            ->whereNotNull('SCHEDSTART')
            ->whereNotNull('SCHEDFINISH')
            ->whereNotNull('ACTLABHRS')
            ->whereRaw('ACTFINISH >= SCHEDSTART')
            ->whereRaw('ACTFINISH <= SCHEDFINISH')
            ->count();
        
        $percentage = $totalPmClosed > 0 ? round(($pmCompliant / $totalPmClosed) * 100, 2) : 0;
        
        // Level Determination
        $level = 1;
        $desc = "PM Compliance 0 - 70%";
        if ($percentage == 100) { $level = 5; $desc = "PM Compliance = 100%"; }
        elseif ($percentage > 90) { $level = 4; $desc = "PM Compliance > 90%"; }
        elseif ($percentage > 80) { $level = 3; $desc = "PM Compliance > 80%"; }
        elseif ($percentage > 70) { $level = 2; $desc = "PM Compliance > 70%"; }
        
        return [
            'total' => $totalPmClosed,
            'compliant' => $pmCompliant,
            'percentage' => $percentage,
            'level' => $level,
            'description' => $desc
        ];
    }
    
    // I6.7 - WO Planned Backlog
    private function calculatePlannedBacklog()
    {
        // Planned Work: Non OH, Status in identification phase (WAPPR, WSCH, WMATL)
        // Ready Work: Non OH, Ready for execution (APPR)
        // Using ESTLABHRS for manhours
        
        $plannedManhours = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WORKTYPE', '!=', 'OH')
            ->whereIn('STATUS', ['WAPPR', 'WSCH', 'WMATL', 'WPCOND'])
            ->sum('ESTLABHRS');
            
        $readyManhours = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WORKTYPE', '!=', 'OH')
            ->where('STATUS', 'APPR')
            ->sum('ESTLABHRS');
            
        $totalManhours = $plannedManhours + $readyManhours;
        
        // Crew Capacity (Example: 400 hours/week)
        $crewCapacity = 400; 
        
        $weeks = $crewCapacity > 0 ? round($totalManhours / $crewCapacity, 2) : 0;
        
        // Level Determination
        $level = 1;
        $desc = "≥ 8 Minggu";
        if ($weeks < 4) { $level = 5; $desc = "< 4 Minggu"; }
        elseif ($weeks < 6) { $level = 4; $desc = "4 - < 6 Minggu"; }
        elseif ($weeks < 8) { $level = 3; $desc = "6 - < 8 Minggu"; }
        elseif ($weeks >= 8) { $level = 2; $desc = "≥ 8 Minggu"; }
        
        // Level 1 if no data or very high
        if ($totalManhours == 0) { $level = 1; $desc = "Tidak terukur/tidak ada data"; }
        
        return [
            'total_manhours' => $totalManhours,
            'planned_hours' => $plannedManhours,
            'ready_hours' => $readyManhours,
            'weeks' => $weeks,
            'level' => $level,
            'description' => $desc
        ];
    }
    
    // I6.8 - Schedule Compliance (Non Tactical)
    private function calculateScheduleCompliance()
    {
        // Non Tactical: CR, EM, EJ, NM, SF
        $nonTacticalTypes = ['CR', 'EM', 'EJ', 'NM', 'SF'];
        
        $woBase = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->whereIn('WORKTYPE', $nonTacticalTypes)
            ->whereIn('STATUS', ['COMP', 'CLOSE']);
            
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
    private function calculateRework()
    {
        // Rework: Repeated WO (same Asset, same Problem) within 1 month
        // Scope: CR, EM
        
        $oneMonthAgo = Carbon::now()->subMonth();
        
        // Total CR+EM in last month
        $totalCrEm = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->whereIn('WORKTYPE', ['CR', 'EM'])
            ->where('REPORTDATE', '>=', $oneMonthAgo)
            ->count();
            
        // Simplified rework count
        $reworkCount = 0;
        
        if ($totalCrEm > 0) {
            // Count incidents where same Asset + Problem appears more than once
             $duplicatesDetailed = DB::connection('oracle')->select("
                SELECT SUM(cnt - 1) as rework_incidents
                FROM (
                    SELECT COUNT(*) as cnt
                    FROM WORKORDER 
                    WHERE SITEID = 'KD' 
                    AND WORKTYPE IN ('CR', 'EM')
                    AND REPORTDATE >= TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')
                    AND ASSETNUM IS NOT NULL 
                    AND PROBLEMCODE IS NOT NULL
                    GROUP BY ASSETNUM, PROBLEMCODE
                    HAVING COUNT(*) > 1
                )
            ", [$oneMonthAgo->format('Y-m-d H:i:s')]);
            
            $reworkCount = $duplicatesDetailed[0]->rework_incidents ?? 0;
        }

        $percentage = $totalCrEm > 0 ? round(($reworkCount / $totalCrEm) * 100, 2) : 0;
        
        // Level
        $level = 1;
        $desc = "> 20%";
        if ($percentage <= 5) { $level = 5; $desc = "≤ 5%"; }
        elseif ($percentage <= 10) { $level = 4; $desc = "5% < - 10%"; }
        elseif ($percentage <= 15) { $level = 3; $desc = "10% < - 15%"; }
        elseif ($percentage <= 20) { $level = 2; $desc = "15% < - 20%"; }
        
        return [
            'total' => $totalCrEm,
            'rework' => $reworkCount,
            'percentage' => $percentage,
            'level' => $level,
            'description' => $desc
        ];
    }
    
    // I6.10.1 - Reactive Work
    private function calculateReactiveWork()
    {
        // Reactive Ratio = (Non Tactical Created) / (Tactical Closed + Non Tactical Created)
        // Tactical: PM, PdM, EJ, OH -> Closed
        // Non Tactical: CR, EM -> Created (All Issued)
        
        $tacticalClosed = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->whereIn('WORKTYPE', ['PM', 'PdM', 'EJ', 'OH'])
            ->whereIn('STATUS', ['COMP', 'CLOSE'])
            ->count();
            
        $nonTacticalCreated = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->whereIn('WORKTYPE', ['CR', 'EM'])
            ->count(); // All created
            
        $denominator = $tacticalClosed + $nonTacticalCreated;
        $percentage = $denominator > 0 ? round(($nonTacticalCreated / $denominator) * 100, 2) : 0;
        
        // Level
        $level = 1; 
        $desc = "> 20%";
        if ($percentage <= 5) { $level = 5; $desc = "≤ 5%"; }
        elseif ($percentage <= 10) { $level = 4; $desc = "5% < - 10%"; }
        elseif ($percentage <= 15) { $level = 3; $desc = "10% < - 15%"; }
        elseif ($percentage <= 20) { $level = 2; $desc = "15% < - 20%"; }
        
        return [
            'tactical_closed' => $tacticalClosed,
            'non_tactical' => $nonTacticalCreated,
            'percentage' => $percentage,
            'level' => $level,
            'description' => $desc
        ];
    }
    
    // I6.10.2 - WR/SR Open/Queued
    private function calculateWrSrOpen()
    {
        $srStatuses = ['NEW', 'QUEUED'];
        
        $totalSr = DB::connection('oracle')->table('SR')
            ->where('SITEID', 'KD')
            ->count();
            
        // Overdue Normal: >= 30 days.
        $normalOverdue = DB::connection('oracle')->table('SR')
            ->where('SITEID', 'KD')
            ->whereIn('STATUS', $srStatuses)
            ->where('INTERNALPRIORITY', '>', 1) // Assuming > 1 is Normal
            ->where('REPORTDATE', '<=', Carbon::now()->subDays(30))
            ->count();
            
        $urgentOverdue = DB::connection('oracle')->table('SR')
            ->where('SITEID', 'KD')
            ->whereIn('STATUS', $srStatuses)
            ->where('INTERNALPRIORITY', 1) // Assuming 1 is Urgent
            ->where('REPORTDATE', '<=', Carbon::now()->subDays(7))
            ->count();
            
        $totalOverdue = $normalOverdue + $urgentOverdue;
        $percentage = $totalSr > 0 ? round(($totalOverdue / $totalSr) * 100, 2) : 0;
        
        // Level
        $level = 1;
        $desc = "> 5%";
        if ($percentage == 0) { $level = 5; $desc = "0%"; }
        elseif ($percentage <= 1) { $level = 4; $desc = "≤ 1%"; }
        elseif ($percentage <= 2) { $level = 3; $desc = "≤ 2%"; }
        elseif ($percentage <= 5) { $level = 2; $desc = "≤ 5%"; }
        
        return [
            'total' => $totalSr,
            'overdue' => $totalOverdue,
            'percentage' => $percentage,
            'level' => $level,
            'description' => $desc
        ];
    }
    
    // I6.10.3 - WO Ageing
    private function calculateWoAgeing()
    {
        // Active statuses
        $openStatuses = ['WAPPR', 'APPR', 'WSCH', 'WMATL', 'WPCOND', 'INPRG'];
        
        $totalOpen = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WORKTYPE', '!=', 'OH')
            ->whereIn('STATUS', $openStatuses)
            ->count();
            
        $oldOpen = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->where('WORKTYPE', '!=', 'OH')
            ->whereIn('STATUS', $openStatuses)
            ->where('REPORTDATE', '<=', Carbon::now()->subDays(365))
            ->count();
            
        $percentage = $totalOpen > 0 ? round(($oldOpen / $totalOpen) * 100, 2) : 0;
        
        // Level
        $level = 1;
        $desc = "> 20%";
        if ($percentage <= 5) { $level = 5; $desc = "≤ 5%"; }
        elseif ($percentage <= 10) { $level = 4; $desc = "5% < - 10%"; }
        elseif ($percentage <= 15) { $level = 3; $desc = "10% < - 15%"; }
        elseif ($percentage <= 20) { $level = 2; $desc = "15% < - 20%"; }
        
        return [
            'total_open' => $totalOpen,
            'old_open' => $oldOpen,
            'percentage' => $percentage,
            'level' => $level,
            'description' => $desc
        ];
    }
    
    // I6.10.4 - Post Implementation Review
    private function calculatePostImplReview()
    {
        // Placeholder for Project Review
        $startDate = Carbon::now()->startOfYear();
        
        $totalProjects = DB::connection('oracle')->table('WORKORDER')
            ->where('SITEID', 'KD')
            ->whereIn('WORKTYPE', ['PJ', 'AI']) 
            ->whereIn('STATUS', ['COMP', 'CLOSE'])
            ->where('STATUSDATE', '>=', $startDate)
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