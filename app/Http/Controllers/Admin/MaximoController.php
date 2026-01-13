<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;

class MaximoController extends Controller
{
    public function index()
    {
        try {

            /* ==========================
             * WORK ORDER (TETAP)
             * ========================== */
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
                ->paginate(10);

            /* ==========================
             * SERVICE REQUEST (BARU)
             * ========================== */
            $serviceRequests = DB::connection('oracle')
                ->table('SR')
                ->select([
                    'TICKETID',
                    'DESCRIPTION',
                    'STATUS',
                    'STATUSDATE',
                    'SITEID',
                    'LOCATION',
                    'ASSETNUM',
                    'REPORTEDBY',
                    'REPORTDATE',
                ])
                ->where('SITEID', 'KD')
                ->orderBy('REPORTDATE', 'desc')
                ->paginate(10);

            return view('admin.maximo.index', [
                'workOrders'      => $this->formatWorkOrders($workOrders),
                'serviceRequests'=> $this->formatServiceRequests($serviceRequests),
                'workOrdersPaginate' => $workOrders,
                'serviceRequestsPaginate' => $serviceRequests,
                'error'           => null,
                'errorDetail'     => null,
            ]);

        } catch (QueryException $e) {

            Log::error('ORACLE QUERY ERROR', [
                'oracle_code' => $e->errorInfo[1] ?? null,
                'message'     => $e->getMessage(),
                'sql'         => $e->getSql(),
                'bindings'    => $e->getBindings(),
            ]);

                            $emptyPaginator = new LengthAwarePaginator([], 0, 10, 1, [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]);
                return view('admin.maximo.index', [
                    'workOrders'       => collect([]),
                    'serviceRequests' => collect([]),
                    'workOrdersPaginate' => $emptyPaginator,
                    'serviceRequestsPaginate' => $emptyPaginator,
                    'error' => 'Gagal mengambil data dari Maximo (Query Error)',
                    'errorDetail' => [
                        'oracle_code' => $e->errorInfo[1] ?? null,
                        'message' => $e->getMessage(),
                    ],
                ]);


        } catch (\Throwable $e) {

            Log::error('ORACLE GENERAL ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

                            $emptyPaginator = new LengthAwarePaginator([], 0, 10, 1, [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]);
                return view('admin.maximo.index', [
                    'workOrders'       => collect([]),
                    'serviceRequests' => collect([]),
                    'workOrdersPaginate' => $emptyPaginator,
                    'serviceRequestsPaginate' => $emptyPaginator,
                    'error' => 'Gagal mengambil data dari Maximo (General Error)',
                    'errorDetail' => [
                        'message' => $e->getMessage(),
                    ],
                ]);
        }
    }

    /* ==========================
     * FORMAT WORK ORDER
     * ========================== */
    private function formatWorkOrders($workOrders)
    {
        return collect($workOrders)->map(function ($wo) {
            return [
                'wonum'       => $wo->wonum ?? '-',
                'parent'      => $wo->parent ?? '-',
                'status'      => $wo->status ?? '-',
                'statusdate'  => $wo->statusdate
                    ? Carbon::parse($wo->statusdate)->format('d-m-Y H:i')
                    : '-',
                'worktype'    => $wo->worktype ?? '-',
                'description' => $wo->description ?? '-',
                'assetnum'    => $wo->assetnum ?? '-',
                'location'    => $wo->location ?? '-',
                'siteid'      => $wo->siteid ?? '-',
            ];
        });
    }

    /* ==========================
     * FORMAT SERVICE REQUEST
     * ========================== */
    private function formatServiceRequests($serviceRequests)
    {
        return collect($serviceRequests)->map(function ($sr) {
            return [
                'ticketid'    => $sr->ticketid ?? '-',
                'description' => $sr->description ?? '-',
                'status'      => $sr->status ?? '-',
                'statusdate'  => $sr->statusdate
                    ? Carbon::parse($sr->statusdate)->format('d-m-Y H:i')
                    : '-',
                'siteid'      => $sr->siteid ?? '-',
                'location'    => $sr->location ?? '-',
                'assetnum'    => $sr->assetnum ?? '-',
                'reportedby'  => $sr->reportedby ?? '-',
                'reportdate'  => $sr->reportdate
                    ? Carbon::parse($sr->reportdate)->format('d-m-Y H:i')
                    : '-',
            ];
        });
    }
}
