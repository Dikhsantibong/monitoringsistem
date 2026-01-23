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
        
        // Get PM and CM counts
        $pmCount = DB::table('work_orders')
            ->where('maintenance_type', 'PM')
            ->where('status', 'CLOSED')
            ->where('created_at', '>=', $startDate)
            ->count();
        
        $cmCount = DB::table('work_orders')
            ->where('maintenance_type', 'CM')
            ->where('status', 'CLOSED')
            ->where('created_at', '>=', $startDate)
            ->count();
        
        $totalWO = $pmCount + $cmCount;
        
        // Calculate percentages
        $pmPercentage = $totalWO > 0 ? round(($pmCount / $totalWO) * 100, 1) : 0;
        $cmPercentage = $totalWO > 0 ? round(($cmCount / $totalWO) * 100, 1) : 0;
        
        // Calculate PM/CM Ratio
        $pmCmRatio = $cmCount > 0 ? round($pmCount / $cmCount, 2) : 0;
        
        // Get data per unit
        $unitData = DB::table('work_orders')
            ->select('unit_layanan', 
                DB::raw('SUM(CASE WHEN maintenance_type = "PM" THEN 1 ELSE 0 END) as pm_count'),
                DB::raw('SUM(CASE WHEN maintenance_type = "CM" THEN 1 ELSE 0 END) as cm_count'),
                DB::raw('COUNT(*) as total_count'))
            ->where('status', 'CLOSED')
            ->where('created_at', '>=', $startDate)
            ->groupBy('unit_layanan')
            ->get();
        
        $unitNames = $unitData->pluck('unit_layanan')->toArray();
        $pmPerUnit = $unitData->pluck('pm_count')->toArray();
        $cmPerUnit = $unitData->pluck('cm_count')->toArray();
        $totalPerUnit = $unitData->pluck('total_count')->toArray();
        
        // Find best performing unit
        $bestPerformingUnit = '';
        $maxRatio = 0;
        foreach ($unitData as $unit) {
            $ratio = $unit->cm_count > 0 ? round($unit->pm_count / $unit->cm_count, 2) : 0;
            if ($ratio > $maxRatio) {
                $maxRatio = $ratio;
                $bestPerformingUnit = $unit->unit_layanan;
            }
        }
        
        // Get monthly trend
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $pmMonthly = DB::table('work_orders')
                ->where('maintenance_type', 'PM')
                ->where('status', 'CLOSED')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
            
            $cmMonthly = DB::table('work_orders')
                ->where('maintenance_type', 'CM')
                ->where('status', 'CLOSED')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
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
        $totalPmClosed = DB::table('work_orders')
            ->where('maintenance_type', 'PM')
            ->where('status', 'CLOSED')
            ->count();
        
        $pmCompliant = DB::table('work_orders')
            ->where('maintenance_type', 'PM')
            ->where('status', 'CLOSED')
            ->whereNotNull('actual_finish')
            ->whereNotNull('completion_comment')
            ->whereNotNull('actual_manhour')
            ->whereRaw('actual_finish BETWEEN schedule_start AND schedule_finish')
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
        // Planned Work + Ready Work manhours
        $totalPlannedManhours = DB::table('work_orders')
            ->whereIn('status', ['PLANNED', 'READY'])
            ->whereNotIn('maintenance_type', ['OH'])
            ->sum('estimated_manhour');
        
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
        $nonTacticalTypes = ['CR', 'EM', 'EJ', 'NM', 'SF'];
        
        $totalNonTactical = DB::table('work_orders')
            ->whereIn('maintenance_type', $nonTacticalTypes)
            ->where('status', 'CLOSED')
            ->count();
        
        $compliant = DB::table('work_orders')
            ->whereIn('maintenance_type', $nonTacticalTypes)
            ->where('status', 'CLOSED')
            ->whereNotNull('actual_finish')
            ->whereNotNull('completion_comment')
            ->whereNotNull('actual_manhour')
            ->whereRaw('actual_finish BETWEEN schedule_start AND schedule_finish')
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
        $oneMonthAgo = Carbon::now()->subMonth();
        
        $totalCrEm = DB::table('work_orders')
            ->whereIn('maintenance_type', ['CR', 'EM'])
            ->where('created_at', '>=', $oneMonthAgo)
            ->count();
        
        // WO berulang: sama equipment & problem dalam 1 bulan
        $reworkCount = DB::table('work_orders as w1')
            ->join('work_orders as w2', function($join) {
                $join->on('w1.equipment_id', '=', 'w2.equipment_id')
                     ->on('w1.problem_code', '=', 'w2.problem_code')
                     ->whereRaw('w2.id > w1.id');
            })
            ->whereIn('w1.maintenance_type', ['CR', 'EM'])
            ->where('w1.created_at', '>=', $oneMonthAgo)
            ->distinct('w2.id')
            ->count();
        
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
        $tacticalClosed = DB::table('work_orders')
            ->whereIn('maintenance_type', ['PM', 'PdM', 'EJ', 'OH'])
            ->where('status', 'CLOSED')
            ->count();
        
        $nonTacticalCreated = DB::table('work_orders')
            ->whereIn('maintenance_type', ['CR', 'EM'])
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
        $totalWrSr = DB::table('work_requests')->count();
        
        $normalOverdue = DB::table('work_requests')
            ->whereIn('status', ['OPEN', 'QUEUED'])
            ->where('priority', 'NORMAL')
            ->where('created_at', '<=', Carbon::now()->subDays(30))
            ->count();
        
        $urgentOverdue = DB::table('work_requests')
            ->whereIn('status', ['OPEN', 'QUEUED'])
            ->where('priority', 'URGENT')
            ->where('created_at', '<=', Carbon::now()->subDays(7))
            ->count();
        
        $totalOverdue = $normalOverdue + $urgentOverdue;
        $percentage = $totalWrSr > 0 ? round(($totalOverdue / $totalWrSr) * 100, 2) : 0;
        $level = $this->getWrSrOpenLevel($percentage);
        
        return [
            'total' => $totalWrSr,
            'normal_overdue' => $normalOverdue,
            'urgent_overdue' => $urgentOverdue,
            'total_overdue' => $totalOverdue,
            'percentage' => $percentage,
            'level' => $level
        ];
    }
    
    // I6.10.3 - WO Ageing
    private function calculateWoAgeing()
    {
        $totalOpen = DB::table('work_orders')
            ->whereIn('status', ['OPEN', 'INPROG', 'PLANNED', 'READY'])
            ->whereNotIn('maintenance_type', ['OH'])
            ->count();
        
        $ageingOver365 = DB::table('work_orders')
            ->whereIn('status', ['OPEN', 'INPROG', 'PLANNED', 'READY'])
            ->whereNotIn('maintenance_type', ['OH'])
            ->where('created_at', '<=', Carbon::now()->subDays(365))
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