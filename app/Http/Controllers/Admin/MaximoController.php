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
            // Validasi config Oracle
            $oracle = config('database.connections.oracle');

            if (
                empty($oracle['username']) ||
                empty($oracle['password']) ||
                empty($oracle['service_name'])
            ) {
                throw new \Exception('Konfigurasi Oracle tidak lengkap');
            }

            // QUERY ORACLE MAXIMO (JANGAN pakai schema di table)
            $workOrders = DB::connection('oracle')
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
                ->where('SITEID', '=', 'KD')
                ->orderBy('STATUSDATE', 'desc')
                ->limit(5)
                ->get();

            return view('admin.maximo.index', [
                'formattedData' => $this->formatWorkOrders($workOrders),
                'error' => null
            ]);

        } catch (\Throwable $e) {
            Log::error('ORACLE MAXIMO ERROR', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
