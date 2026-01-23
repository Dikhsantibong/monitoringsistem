<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KinerjaPemeliharaanController extends Controller
{
    public function index()
    {
        // Get data for Kinerja Dashboard (existing data)
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
        
        // Use Oracle connection
        $connection = DB::connection('oracle');
        
        // Get PM and CM counts (filtering by SITEID KD as per MaximoController)
        $pmCount = $connection->table('WORKORDER')
            ->where('WORKTYPE', 'PM')
            ->whereIn('STATUS', ['CLOSE', 'COMP'])
            ->where('SITEID', 'KD')
            ->where('REPORTDATE', '>=', $startDate)
            ->count();
        
        $cmCount = $connection->table('WORKORDER')
            ->where('WORKTYPE', 'CM')
            ->whereIn('STATUS', ['CLOSE', 'COMP'])
            ->where('SITEID', 'KD')
            ->where('REPORTDATE', '>=', $startDate)
            ->count();
        
        $totalWO = $pmCount + $cmCount;
        
        // Calculate percentages
        $pmPercentage = $totalWO > 0 ? round(($pmCount / $totalWO) * 100, 1) : 0;
        $cmPercentage = $totalWO > 0 ? round(($cmCount / $totalWO) * 100, 1) : 0;
        
        // Calculate PM/CM Ratio
        $pmCmRatio = $cmCount > 0 ? round($pmCount / $cmCount, 2) : 0;
        
        // Get data per unit (Using LOCATION as unit proxy)
        $unitData = $connection->table('WORKORDER')
            ->select('LOCATION as unit_layanan', 
                DB::raw("SUM(CASE WHEN WORKTYPE = 'PM' THEN 1 ELSE 0 END) as pm_count"),
                DB::raw("SUM(CASE WHEN WORKTYPE = 'CM' THEN 1 ELSE 0 END) as cm_count"),
                DB::raw("COUNT(*) as total_count"))
            ->whereIn('STATUS', ['CLOSE', 'COMP'])
            ->where('SITEID', 'KD')
            ->where('REPORTDATE', '>=', $startDate)
            ->whereNotNull('LOCATION')
            ->groupBy('LOCATION')
            ->orderByRaw('COUNT(*) DESC')
            ->take(10) // Top 10 locations
            ->get();
        
        $unitNames = $unitData->pluck('unit_layanan')->map(function($item) {
             return $item ?? 'Unknown';
        })->toArray();
        $pmPerUnit = $unitData->pluck('pm_count')->toArray();
        $cmPerUnit = $unitData->pluck('cm_count')->toArray();
        $totalPerUnit = $unitData->pluck('total_count')->toArray();
        
        // Find best performing unit
        $bestPerformingUnit = '';
        $maxRatio = 0;
        foreach ($unitData as $unit) {
            $unitPm = $unit->pm_count;
            $unitCm = $unit->cm_count;
            
            $ratio = $unitCm > 0 ? round($unitPm / $unitCm, 2) : 0;
            if ($ratio > $maxRatio) {
                $maxRatio = $ratio;
                $bestPerformingUnit = $unit->unit_layanan ?? 'N/A';
            }
        }
        
        // Get monthly trend
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $pmMonthly = $connection->table('WORKORDER')
                ->where('WORKTYPE', 'PM')
                ->whereIn('STATUS', ['CLOSE', 'COMP'])
                ->where('SITEID', 'KD')
                ->whereBetween('REPORTDATE', [$monthStart, $monthEnd])
                ->count();
            
            $cmMonthly = $connection->table('WORKORDER')
                ->where('WORKTYPE', 'CM')
                ->whereIn('STATUS', ['CLOSE', 'COMP'])
                ->where('SITEID', 'KD')
                ->whereBetween('REPORTDATE', [$monthStart, $monthEnd])
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
        $connection = DB::connection('oracle');
        
        $totalPmClosed = $connection->table('WORKORDER')
            ->where('WORKTYPE', 'PM')
            ->whereIn('STATUS', ['CLOSE', 'COMP'])
            ->where('SITEID', 'KD')
            ->count();
        
        // Assuming ACTFINISH and ACTLABHRS exist in WORKORDER
        // If not available, we might need to adjust criteria
        $pmCompliant = $connection->table('WORKORDER')
            ->where('WORKTYPE', 'PM')
            ->whereIn('STATUS', ['CLOSE', 'COMP'])
            ->where('SITEID', 'KD')
            ->whereNotNull('ACTFINISH')
            ->whereRaw('ACTFINISH BETWEEN SCHEDSTART AND SCHEDFINISH')
            ->count();
        
        $percentage = $totalPmClosed > 0 ? round(($pmCompliant / $totalPmClosed) * 100, 2) : 0;
        $level = $this->getPmComplianceLevel($percentage);
        
        return [
            'total' => $totalPmClosed,
            'compliant' => $pmCompliant,
            'percentage' => $percentage,
            'level' => $level
        ];
    }
    
    // I6.7 - WO Planned Backlog (dalam minggu)
    private function calculatePlannedBacklog()
    {
        $connection = DB::connection('oracle');
        
        // Planned Work + Ready Work manhours
        // Adapting status: APPR (Approved), WSCH (Waiting Schedule)
        $totalPlannedManhours = $connection->table('WORKORDER')
            ->whereIn('STATUS', ['APPR', 'WSCH', 'INPRG'])
            ->where('SITEID', 'KD')
            ->whereNotIn('WORKTYPE', ['OH'])
            ->sum('ESTLABHRS');
        
        // Crew Capacity per week (contoh: 10 crew x 40 jam/minggu)
        $crewCapacity = 400; // Sesuaikan dengan data aktual
        
        $weeks = $crewCapacity > 0 ? round($totalPlannedManhours / $crewCapacity, 2) : 0;
        $level = $this->getPlannedBacklogLevel($weeks);
        
        return [
            'total_manhours' => $totalPlannedManhours,
            'crew_capacity' => $crewCapacity,
            'weeks' => $weeks,
            'level' => $level
        ];
    }
    
    // I6.8 - Schedule Compliance (Non Tactical)
    private function calculateScheduleCompliance()
    {
        $connection = DB::connection('oracle');
        // Maximo Work Types mapping for non-tactical: CM, EM
        $nonTacticalTypes = ['CM', 'EM'];
        
        $totalNonTactical = $connection->table('WORKORDER')
            ->whereIn('WORKTYPE', $nonTacticalTypes)
            ->whereIn('STATUS', ['CLOSE', 'COMP'])
            ->where('SITEID', 'KD')
            ->count();
        
        $compliant = $connection->table('WORKORDER')
            ->whereIn('WORKTYPE', $nonTacticalTypes)
            ->whereIn('STATUS', ['CLOSE', 'COMP'])
            ->where('SITEID', 'KD')
            ->whereNotNull('ACTFINISH')
            ->whereRaw('ACTFINISH BETWEEN SCHEDSTART AND SCHEDFINISH')
            ->count();
        
        $percentage = $totalNonTactical > 0 ? round(($compliant / $totalNonTactical) * 100, 2) : 0;
        $level = $this->getScheduleComplianceLevel($percentage);
        
        return [
            'total' => $totalNonTactical,
            'compliant' => $compliant,
            'percentage' => $percentage,
            'level' => $level
        ];
    }
    
    // I6.9 - Rework
    private function calculateRework()
    {
        $connection = DB::connection('oracle');
        $oneMonthAgo = Carbon::now()->subMonth();
        
        $totalCrEm = $connection->table('WORKORDER')
            ->whereIn('WORKTYPE', ['CM', 'EM'])
            ->where('SITEID', 'KD')
            ->where('REPORTDATE', '>=', $oneMonthAgo)
            ->count();
        
        // Rework: CM/EM on same ASSETNUM within 30 days
        $reworkCount = $connection->table('WORKORDER as w1')
            ->join('WORKORDER as w2', function($join) {
                $join->on('w1.ASSETNUM', '=', 'w2.ASSETNUM')
                     ->where('w1.ASSETNUM', '!=', null)
                     ->whereRaw('w2.REPORTDATE > w1.REPORTDATE');
            })
            ->whereIn('w1.WORKTYPE', ['CM', 'EM'])
            ->where('w1.SITEID', 'KD')
            ->where('w2.SITEID', 'KD')
            ->where('w1.REPORTDATE', '>=', $oneMonthAgo)
            ->count();
            
        // Note: Counting strategy might need refinement based on exact Rework definition
        // Here assuming 1 match = 1 rework instance found
        
        $percentage = $totalCrEm > 0 ? round(($reworkCount / $totalCrEm) * 100, 2) : 0;
        $level = $this->getReworkLevel($percentage);
        
        return [
            'total' => $totalCrEm,
            'rework' => $reworkCount,
            'percentage' => $percentage,
            'level' => $level
        ];
    }
    
    // I6.10.1 - Reactive Work
    private function calculateReactiveWork()
    {
        $connection = DB::connection('oracle');
        
        $tacticalClosed = $connection->table('WORKORDER')
            ->whereIn('WORKTYPE', ['PM', 'PdM', 'OH'])
            ->whereIn('STATUS', ['CLOSE', 'COMP'])
            ->where('SITEID', 'KD')
            ->count();
        
        $nonTacticalCreated = $connection->table('WORKORDER')
            ->whereIn('WORKTYPE', ['CM', 'EM'])
            ->where('SITEID', 'KD')
            ->count();
        
        $totalWo = $tacticalClosed + $nonTacticalCreated;
        $percentage = $totalWo > 0 ? round(($nonTacticalCreated / $totalWo) * 100, 2) : 0;
        $level = $this->getReactiveWorkLevel($percentage);
        
        return [
            'tactical' => $tacticalClosed,
            'non_tactical' => $nonTacticalCreated,
            'total' => $totalWo,
            'percentage' => $percentage,
            'level' => $level
        ];
    }
    
    // I6.10.2 - WR/SR Open/Queued
    private function calculateWrSrOpen()
    {
        $connection = DB::connection('oracle');
        
        $totalWrSr = $connection->table('SR')
            ->where('SITEID', 'KD')
            ->count();
        
        $openOverdue = $connection->table('SR')
            ->whereIn('STATUS', ['NEW', 'QUEUED', 'PENDING', 'INPROG'])
            ->where('SITEID', 'KD')
            ->where('REPORTDATE', '<=', Carbon::now()->subDays(30))
            ->count();
            
        // Simplified calculation without priority field from Maximo
        
        $percentage = $totalWrSr > 0 ? round(($openOverdue / $totalWrSr) * 100, 2) : 0;
        $level = $this->getWrSrOpenLevel($percentage);
        
        return [
            'total' => $totalWrSr,
            'total_overdue' => $openOverdue,
            'normal_overdue' => $openOverdue, // Placeholder
            'urgent_overdue' => 0, // Placeholder
            'percentage' => $percentage,
            'level' => $level
        ];
    }
    
    // I6.10.3 - WO Ageing
    private function calculateWoAgeing()
    {
        $connection = DB::connection('oracle');
        
        // Open statuses in Maximo: WAPPR, APPR, WSCH, INPRG
        $openStatuses = ['WAPPR', 'APPR', 'WSCH', 'INPRG', 'WMATL'];
        
        $totalOpen = $connection->table('WORKORDER')
            ->whereIn('STATUS', $openStatuses)
            ->where('SITEID', 'KD')
            ->whereNotIn('WORKTYPE', ['OH'])
            ->count();
        
        $ageingOver365 = $connection->table('WORKORDER')
            ->whereIn('STATUS', $openStatuses)
            ->where('SITEID', 'KD')
            ->whereNotIn('WORKTYPE', ['OH'])
            ->where('REPORTDATE', '<=', Carbon::now()->subDays(365))
            ->count();
        
        $percentage = $totalOpen > 0 ? round(($ageingOver365 / $totalOpen) * 100, 2) : 0;
        $level = $this->getWoAgeingLevel($percentage);
        
        return [
            'total_open' => $totalOpen,
            'ageing_365' => $ageingOver365,
            'percentage' => $percentage,
            'level' => $level
        ];
    }
    
    // I6.10.4 - Post Implementation Review
    private function calculatePostImplReview()
    {
        // Keeping this local as 'programs' likely refers to a local feature not present in Maximo standard tables
        $totalPrograms = DB::table('programs')
            ->whereYear('completion_date', Carbon::now()->year)
            ->whereIn('type', ['AI', 'PROJECT'])
            ->where('status', 'COMPLETED')
            ->count();
        
        $reviewedPrograms = DB::table('programs')
            ->whereYear('completion_date', Carbon::now()->year)
            ->whereIn('type', ['AI', 'PROJECT'])
            ->where('status', 'COMPLETED')
            ->whereNotNull('post_implementation_review')
            ->count();
        
        $percentage = $totalPrograms > 0 ? round(($reviewedPrograms / $totalPrograms) * 100, 2) : 0;
        $level = $this->getPostImplReviewLevel($percentage);
        
        return [
            'total' => $totalPrograms,
            'reviewed' => $reviewedPrograms,
            'percentage' => $percentage,
            'level' => $level
        ];
    }
    
    // Level Determination Methods
    private function getPmComplianceLevel($percentage)
    {
        if ($percentage == 100) return 5;
        if ($percentage > 90) return 4;
        if ($percentage > 80) return 3;
        if ($percentage > 70) return 2;
        return 1;
    }
    
    private function getPlannedBacklogLevel($weeks)
    {
        if ($weeks < 4) return 5;
        if ($weeks < 6) return 4;
        if ($weeks < 8) return 3;
        if ($weeks >= 8) return 2;
        return 1;
    }
    
    private function getScheduleComplianceLevel($percentage)
    {
        if ($percentage > 80) return 5;
        if ($percentage > 70) return 4;
        if ($percentage > 50) return 3;
        if ($percentage > 30) return 2;
        return 1;
    }
    
    private function getReworkLevel($percentage)
    {
        if ($percentage <= 5) return 5;
        if ($percentage <= 10) return 4;
        if ($percentage <= 15) return 3;
        if ($percentage <= 20) return 2;
        return 1;
    }
    
    private function getReactiveWorkLevel($percentage)
    {
        if ($percentage <= 5) return 5;
        if ($percentage <= 10) return 4;
        if ($percentage <= 15) return 3;
        if ($percentage <= 20) return 2;
        return 1;
    }
    
    private function getWrSrOpenLevel($percentage)
    {
        if ($percentage == 0) return 5;
        if ($percentage <= 1) return 4;
        if ($percentage <= 2) return 3;
        if ($percentage <= 5) return 2;
        return 1;
    }
    
    private function getWoAgeingLevel($percentage)
    {
        if ($percentage <= 5) return 5;
        if ($percentage <= 10) return 4;
        if ($percentage <= 15) return 3;
        if ($percentage <= 20) return 2;
        return 1;
    }
    
    private function getPostImplReviewLevel($percentage)
    {
        if ($percentage == 100) return 4; // Level 5 requires additional criteria
        if ($percentage > 75) return 3;
        if ($percentage > 50) return 2;
        return 1;
    }
}