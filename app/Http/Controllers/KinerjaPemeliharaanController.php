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

        try {
            // Ambil data dari Maximo dengan filter SITEID = 'KD' dan WONUM LIKE 'WO%'
            $workOrdersQuery = DB::connection('oracle')
                ->table('WORKORDER')
                ->select([
                    'WONUM',
                    'WORKTYPE',
                    'STATUS',
                    'LOCATION',
                    'SITEID',
                ])
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%') // Hanya ambil WO yang dimulai dengan "WO", bukan "WT"
                ->whereIn('STATUS', ['COMP', 'CLOSE']); // Hanya WO yang sudah closed/completed

            $workOrders = $workOrdersQuery->get();

            // Filter PM dan CM
            $pmClosed = $workOrders->where('worktype', 'PM');
            $cmClosed = $workOrders->where('worktype', 'CM');

        } catch (QueryException $e) {
            Log::error('ORACLE QUERY ERROR (Kinerja Pemeliharaan)', [
                'oracle_code' => $e->errorInfo[1] ?? null,
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
            ]);
            $workOrders = collect([]);
        } catch (\Throwable $e) {
            Log::error('ORACLE GENERAL ERROR (Kinerja Pemeliharaan)', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            $workOrders = collect([]);
        }

        // Group PM dan CM berdasarkan Unit Layanan (UL)
        $pmByUnit = [];
        $cmByUnit = [];
        $pmPerUnit = [];
        $cmPerUnit = [];

        foreach ($unitMap as $ulCode => $unitName) {
            $unitCodes = $ulMapping[$ulCode] ?? [];
            
            // Filter PM berdasarkan LOCATION prefix
            $pm = $pmClosed->filter(function ($wo) use ($unitCodes) {
                $location = strtoupper($wo->location ?? '');
                foreach ($unitCodes as $code) {
                    if (strpos($location, $code) === 0) {
                        return true;
                    }
                }
                return false;
            });

            // Filter CM berdasarkan LOCATION prefix
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
        }

        return view('kinerja.index', [
            'pmByUnit' => $pmByUnit,
            'cmByUnit' => $cmByUnit,
            'unitNames' => array_values($unitMap),
            'pmPerUnit' => $pmPerUnit,
            'cmPerUnit' => $cmPerUnit,
            'pmCount' => $pmClosed->count(),
            'cmCount' => $cmClosed->count(),
        ]);
    }
}
