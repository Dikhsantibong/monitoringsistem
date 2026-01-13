<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MaximoController extends Controller
{
    public function index()
    {
        try {
            /* =========================
             * SERVICE REQUEST (SR)
             * ========================= */
            $sr = DB::connection('oracle')
                ->table('SR')
                ->select(
                    'TICKETID',
                    'DESCRIPTION',
                    'STATUS',
                    'STATUSDATE',
                    'SITEID',
                    'LOCATION',
                    'ASSETNUM',
                    'REPORTEDBY',
                    'REPORTDATE'
                )
                ->where('SITEID', 'KD')
                ->orderBy('REPORTDATE', 'desc')
                ->limit(5)
                ->get();

            /* =========================
             * WORK ORDER (WO)
             * ========================= */
            $wo = DB::connection('oracle')
                ->table('WORKORDER')
                ->select(
                    'WONUM',
                    'PARENT',
                    'STATUS',
                    'STATUSDATE',
                    'WORKTYPE',
                    'DESCRIPTION',
                    'ASSETNUM',
                    'LOCATION',
                    'SITEID'
                )
                ->where('SITEID', 'KD')
                ->orderBy('STATUSDATE', 'desc')
                ->limit(5)
                ->get();

            return view('admin.maximo.index', [
                'srData' => $this->formatSR($sr),
                'woData' => $this->formatWO($wo),
                'error'  => null
            ]);

        } catch (\Throwable $e) {

            Log::error('MAXIMO ORACLE ERROR', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile()
            ]);

            return view('admin.maximo.index', [
                'srData' => collect([]),
                'woData' => collect([]),
                'error'  => $e->getMessage()
            ]);
        }
    }

    /* =========================
     * FORMAT SERVICE REQUEST
     * ========================= */
    private function formatSR($data)
    {
        return collect($data)->map(function ($sr) {
            return [
                'ticketid'    => $sr->TICKETID ?? '-',
                'description' => $sr->DESCRIPTION ?? '-',
                'status'      => $sr->STATUS ?? '-',
                'statusdate'  => !empty($sr->STATUSDATE)
                    ? Carbon::parse($sr->STATUSDATE)
                    : null,
                'siteid'      => $sr->SITEID ?? '-',
                'location'    => $sr->LOCATION ?? '-',
                'assetnum'    => $sr->ASSETNUM ?? '-',
                'reportedby'  => $sr->REPORTEDBY ?? '-',
                'reportdate'  => !empty($sr->REPORTDATE)
                    ? Carbon::parse($sr->REPORTDATE)
                    : null,
            ];
        });
    }

    /* =========================
     * FORMAT WORK ORDER
     * ========================= */
    private function formatWO($data)
    {
        return collect($data)->map(function ($wo) {
            return [
                'wonum'       => $wo->WONUM ?? '-',
                'parent'      => $wo->PARENT ?? '-',
                'status'      => $wo->STATUS ?? '-',
                'statusdate'  => !empty($wo->STATUSDATE)
                    ? Carbon::parse($wo->STATUSDATE)
                    : null,
                'worktype'    => $wo->WORKTYPE ?? '-',
                'description' => $wo->DESCRIPTION ?? '-',
                'assetnum'    => $wo->ASSETNUM ?? '-',
                'location'    => $wo->LOCATION ?? '-',
                'siteid'      => $wo->SITEID ?? '-',
            ];
        });
    }
}
