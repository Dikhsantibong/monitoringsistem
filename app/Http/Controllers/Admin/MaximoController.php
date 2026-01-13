<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class MaximoController extends Controller
{
    public function index()
    {
        try {
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

            return view('admin.maximo.index', [
                'formattedData' => $this->formatWorkOrders($workOrders),
                'error' => null,
                'errorDetail' => null,
            ]);

        } catch (QueryException $e) {

            // ERROR DARI ORACLE (PALING PENTING)
            $oracleMessage = $e->getMessage();
            $oracleCode    = $e->errorInfo[1] ?? null;
            $sql           = $e->getSql();
            $bindings      = $e->getBindings();

            Log::error('ORACLE QUERY ERROR', [
                'oracle_code' => $oracleCode,
                'message'     => $oracleMessage,
                'sql'         => $sql,
                'bindings'    => $bindings,
            ]);

            return view('admin.maximo.index', [
                'formattedData' => collect([]),
                'error' => 'Gagal mengambil data dari Maximo (Query Error)',
                'errorDetail' => [
                    'oracle_code' => $oracleCode,
                    'message' => $oracleMessage,
                    'sql' => $sql,
                    'bindings' => $bindings,
                ],
            ]);

        } catch (\Throwable $e) {

            Log::error('ORACLE GENERAL ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return view('admin.maximo.index', [
                'formattedData' => collect([]),
                'error' => 'Gagal mengambil data dari Maximo (General Error)',
                'errorDetail' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
            ]);
        }
    }

    private function formatWorkOrders($workOrders)
    {
        return collect($workOrders)->map(function ($wo) {
            return [
                'wonum'       => $wo->wonum ?? '-',
                'parent'      => $wo->parent ?? '-',
                'status'      => $wo->status ?? '-',
                'statusdate'  => !empty($wo->statusdate)
                    ? Carbon::parse($wo->statusdate)
                    : null,
                'worktype'    => $wo->worktype ?? '-',
                'description' => $wo->description ?? '-',
                'assetnum'    => $wo->assetnum ?? '-',
                'location'    => $wo->location ?? '-',
                'siteid'      => $wo->siteid ?? '-',
            ];
        });
    }

}
