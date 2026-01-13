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
            /**
             * NOTE:
             * - Tidak perlu cek username/password/service_name di runtime
             * - Jika salah, Oracle akan throw exception otomatis
             */

            $workOrders = DB::connection('oracle')
                ->table('WORKORDER')
                ->select([
                    'WONUM',
                    'PARENT',
                    'STATUS',
                    'STATUSDATE',
                    'WORKTYPE',
                    'DESCRIPTION',
                    'ASSETNUM',
                    'LOCATION',
                    'SITEID',
                ])
                ->where('SITEID', 'KD')
                ->orderBy('STATUSDATE', 'desc')
                ->limit(5)
                ->get();

            $formattedData = $this->formatWorkOrders($workOrders);

            return view('admin.maximo.index', [
                'formattedData' => $formattedData,
                'error' => null,
            ]);

        } catch (\Throwable $e) {

            Log::error('ORACLE MAXIMO ERROR', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return view('admin.maximo.index', [
                'formattedData' => collect([]),
                'error' => 'Gagal mengambil data Work Order dari Maximo',
            ]);
        }
    }

    /**
     * Normalisasi data Oracle → Laravel
     */
    private function formatWorkOrders($workOrders)
    {
        return collect($workOrders)->map(function ($wo) {

            $statusDate = null;
            if (!empty($wo->STATUSDATE)) {
                try {
                    $statusDate = Carbon::parse($wo->STATUSDATE);
                } catch (\Throwable $e) {
                    $statusDate = null;
                }
            }

            return [
                'wonum'       => $wo->WONUM,
                'parent'      => $wo->PARENT,
                'status'      => $wo->STATUS,
                'statusdate'  => $statusDate,
                'worktype'    => $wo->WORKTYPE,
                'description' => $wo->DESCRIPTION,
                'assetnum'    => $wo->ASSETNUM,
                'location'    => $wo->LOCATION,
                'siteid'      => $wo->SITEID,
            ];
        });
    }
}
