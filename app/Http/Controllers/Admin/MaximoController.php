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
            // Pastikan koneksi oracle ada
            $oracleConfig = config('database.connections.oracle');

            if (
                empty($oracleConfig['username']) ||
                empty($oracleConfig['password']) ||
                empty($oracleConfig['database'])
            ) {
                throw new \Exception(
                    'Konfigurasi Oracle belum lengkap. Pastikan username, password, dan service_name terisi di config/database.php'
                );
            }

            // Query ke MAXIMO
            $workOrders = DB::connection('oracle')
                ->table('MAXIMO.WORKORDER')
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

            $formattedData = $this->formatWorkOrders($workOrders);

            return view('admin.maximo.index', compact('formattedData'));

        } catch (\Throwable $e) {
            Log::error('MAXIMO ORACLE ERROR', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return view('admin.maximo.index', [
                'formattedData' => collect([]),
                'error' => $e->getMessage()
            ]);
        }
    }

    private function formatWorkOrders($workOrders)
    {
        return collect($workOrders)->map(function ($wo) {
            $statusDate = null;

            if (!empty($wo->STATUSDATE)) {
                try {
                    $statusDate = Carbon::parse($wo->STATUSDATE);
                } catch (\Exception $e) {
                    $statusDate = $wo->STATUSDATE;
                }
            }

            return [
                'wonum'       => $wo->WONUM ?? '-',
                'parent'      => $wo->PARENT ?? '-',
                'status'      => $wo->STATUS ?? '-',
                'statusdate'  => $statusDate,
                'worktype'    => $wo->WORKTYPE ?? '-',
                'description' => $wo->DESCRIPTION ?? '-',
                'assetnum'    => $wo->ASSETNUM ?? '-',
                'location'    => $wo->LOCATION ?? '-',
                'siteid'      => $wo->SITEID ?? '-',
            ];
        });
    }
}
