<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class KinerjaPemeliharaanController extends Controller
{
    public function index()
    {
        // Mapping UL (Unit Layanan) berdasarkan LOCATION prefix
        $ulMapping = [
            'ULPLTD_KOLAKA' => ['KLKA', 'LANI', 'SABI', 'MIKU'],
            'ULPLTD_BAU_BAU' => ['BBAU', 'RAHA', 'WANG', 'EREK', 'RONG', 'WINN'],
            'ULPLTD_POASIA' => ['POAS'],
            'ULPLTD_WUA_WUA' => ['WUAW', 'LANG'],
        ];
        
        $unitMap = [
            'ULPLTD_KOLAKA' => 'ULPLTD KOLAKA',
            'ULPLTD_BAU_BAU' => 'ULPLTD BAU-BAU',
            'ULPLTD_POASIA' => 'ULPLTD POASIA',
            'ULPLTD_WUA_WUA' => 'ULPLTD WUA-WUA',
        ];
        
        $pmClosed = collect();
        $cmClosed = collect();
        $workOrders = collect();
        
        try {
            // Ambil data dari Maximo
            $workOrdersQuery = DB::connection('oracle')
                ->table('WORKORDER')
                ->select([
                    'WONUM',
                    'WORKTYPE',
                    'STATUS',
                    'LOCATION',
                    'SITEID',
                    DB::raw("TO_CHAR(STATUSDATE, 'YYYY-MM-DD') as STATUSDATE")
                ])
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%')
                ->whereIn('STATUS', ['COMP', 'CLOSE']);
            
            $workOrders = $workOrdersQuery->get();
            
            // Filter PM dan CM
            $pmClosed = $workOrders->where('worktype', 'PM');
            $cmClosed = $workOrders->where('worktype', 'CM');
            
        } catch (QueryException $e) {
            Log::error('ORACLE QUERY ERROR (Kinerja Pemeliharaan)', [
                'oracle_code' => $e->errorInfo[1] ?? null,
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            Log::error('ORACLE GENERAL ERROR (Kinerja Pemeliharaan)', [
                'message' => $e->getMessage(),
            ]);
        }
        
        // Group PM dan CM berdasarkan Unit Layanan
        $pmByUnit = [];
        $cmByUnit = [];
        $pmPerUnit = [];
        $cmPerUnit = [];
        $totalPerUnit = [];
        
        foreach ($unitMap as $ulCode => $unitName) {
            $unitCodes = $ulMapping[$ulCode] ?? [];
            
            $pm = $pmClosed->filter(function ($wo) use ($unitCodes) {
                $location = strtoupper($wo->location ?? '');
                foreach ($unitCodes as $code) {
                    if (strpos($location, $code) === 0) {
                        return true;
                    }
                }
                return false;
            });
            
            $cm = $cmClosed->filter(function ($wo) use ($unitCodes) {
                $location = strtoupper($wo->location ?? '');
                foreach ($unitCodes as $code) {
                    if (strpos($location, $code) === 0) {
                        return true;
                    }
                }
                return false;
            });
            
            $pmByUnit[$unitName] = $pm;
            $cmByUnit[$unitName] = $cm;
            $pmPerUnit[] = $pm->count();
            $cmPerUnit[] = $cm->count();
            $totalPerUnit[] = $pm->count() + $cm->count();
        }
        
        // Data untuk trend (6 bulan terakhir)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->locale('id')->isoFormat('MMM YYYY');
            
            $monthlyPM = $pmClosed->filter(function($wo) use ($monthKey) {
                return strpos($wo->statusdate ?? '', $monthKey) === 0;
            })->count();
            
            $monthlyCM = $cmClosed->filter(function($wo) use ($monthKey) {
                return strpos($wo->statusdate ?? '', $monthKey) === 0;
            })->count();
            
            $monthlyTrend[] = [
                'label' => $monthLabel,
                'pm' => $monthlyPM,
                'cm' => $monthlyCM,
            ];
        }
        
        $pmCount = $pmClosed->count();
        $cmCount = $cmClosed->count();
        $totalWO = $pmCount + $cmCount;
        
        // Statistik tambahan
        $pmPercentage = $totalWO > 0 ? round(($pmCount / $totalWO) * 100, 1) : 0;
        $cmPercentage = $totalWO > 0 ? round(($cmCount / $totalWO) * 100, 1) : 0;
        $pmCmRatio = $cmCount > 0 ? round($pmCount / $cmCount, 2) : 0;
        
        // Unit dengan performa terbaik
        $bestPerformingUnit = '';
        $maxRatio = 0;
        foreach ($unitMap as $ulCode => $unitName) {
            $idx = array_search($unitName, array_values($unitMap));
            $pmUnit = $pmPerUnit[$idx] ?? 0;
            $cmUnit = $cmPerUnit[$idx] ?? 0;
            if ($cmUnit > 0) {
                $ratio = $pmUnit / $cmUnit;
                if ($ratio > $maxRatio) {
                    $maxRatio = $ratio;
                    $bestPerformingUnit = $unitName;
                }
            }
        }
        
        return view('kinerja.index', [
            'pmByUnit' => $pmByUnit,
            'cmByUnit' => $cmByUnit,
            'unitNames' => array_values($unitMap),
            'pmPerUnit' => $pmPerUnit,
            'cmPerUnit' => $cmPerUnit,
            'totalPerUnit' => $totalPerUnit,
            'pmCount' => $pmCount,
            'cmCount' => $cmCount,
            'totalWO' => $totalWO,
            'pmPercentage' => $pmPercentage,
            'cmPercentage' => $cmPercentage,
            'pmCmRatio' => $pmCmRatio,
            'monthlyTrend' => $monthlyTrend,
            'bestPerformingUnit' => $bestPerformingUnit,
            'maxRatio' => $maxRatio,
        ]);
    }
}