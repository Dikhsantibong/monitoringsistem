<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class MaximoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $workOrderPage = $request->input('wo_page', 1);
            $serviceRequestPage = $request->input('sr_page', 1);
            $search = $request->input('search');
            
            // Filter untuk Work Order
            $woStatusFilter = $request->input('wo_status');
            $woWorkTypeFilter = $request->input('wo_worktype');
            $woDateFrom = $request->input('wo_date_from');
            $woDateTo = $request->input('wo_date_to');
            
            // Filter untuk Service Request
            $srStatusFilter = $request->input('sr_status');
            $srDateFrom = $request->input('sr_date_from');
            $srDateTo = $request->input('sr_date_to');

            /* ==========================
             * WORK ORDER (TETAP)
             * ========================== */
            $workOrdersQuery = DB::connection('oracle')
                ->table('WORKORDER')
                ->select([
                    'WONUM',
                    'PARENT',
                    'STATUS',
                    'STATUSDATE',
                    'WORKTYPE',
                    'WOPRIORITY',
                    'DESCRIPTION',
                    'ASSETNUM',
                    'LOCATION',
                    'SITEID',
                    'DOWNTIME',
                    'SCHEDSTART',
                    'SCHEDFINISH',
                    'REPORTDATE',
                ])
                ->where('SITEID', 'KD');

            if ($search) {
                $workOrdersQuery->where(function ($q) use ($search) {
                    $q->where('WONUM', 'LIKE', "%{$search}%")
                        ->orWhere('PARENT', 'LIKE', "%{$search}%")
                        ->orWhere('STATUS', 'LIKE', "%{$search}%")
                        ->orWhere('WORKTYPE', 'LIKE', "%{$search}%")
                        ->orWhere('DESCRIPTION', 'LIKE', "%{$search}%")
                        ->orWhere('ASSETNUM', 'LIKE', "%{$search}%")
                        ->orWhere('LOCATION', 'LIKE', "%{$search}%");
                });
            }
            
            // Filter Status
            if ($woStatusFilter) {
                $workOrdersQuery->where('STATUS', $woStatusFilter);
            }
            
            // Filter Work Type
            if ($woWorkTypeFilter) {
                $workOrdersQuery->where('WORKTYPE', $woWorkTypeFilter);
            }
            
            // Filter Tanggal (Report Date)
            if ($woDateFrom) {
                $workOrdersQuery->whereDate('REPORTDATE', '>=', Carbon::parse($woDateFrom)->format('Y-m-d'));
            }
            if ($woDateTo) {
                $workOrdersQuery->whereDate('REPORTDATE', '<=', Carbon::parse($woDateTo)->format('Y-m-d'));
            }

            $workOrdersQuery->orderBy('STATUSDATE', 'desc');

            $workOrders = $workOrdersQuery->paginate(10, ['*'], 'wo_page', $workOrderPage);

            /* ==========================
             * SERVICE REQUEST (BARU)
             * ========================== */
            $serviceRequestsQuery = DB::connection('oracle')
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
                ->where('SITEID', 'KD');

            if ($search) {
                $serviceRequestsQuery->where(function ($q) use ($search) {
                    $q->where('TICKETID', 'LIKE', "%{$search}%")
                        ->orWhere('DESCRIPTION', 'LIKE', "%{$search}%")
                        ->orWhere('STATUS', 'LIKE', "%{$search}%")
                        ->orWhere('SITEID', 'LIKE', "%{$search}%")
                        ->orWhere('LOCATION', 'LIKE', "%{$search}%")
                        ->orWhere('ASSETNUM', 'LIKE', "%{$search}%")
                        ->orWhere('REPORTEDBY', 'LIKE', "%{$search}%");
                });
            }
            
            // Filter Status
            if ($srStatusFilter) {
                $serviceRequestsQuery->where('STATUS', $srStatusFilter);
            }
            
            // Filter Tanggal (Report Date)
            if ($srDateFrom) {
                $serviceRequestsQuery->whereDate('REPORTDATE', '>=', Carbon::parse($srDateFrom)->format('Y-m-d'));
            }
            if ($srDateTo) {
                $serviceRequestsQuery->whereDate('REPORTDATE', '<=', Carbon::parse($srDateTo)->format('Y-m-d'));
            }

            $serviceRequestsQuery->orderBy('REPORTDATE', 'desc');

            $serviceRequests = $serviceRequestsQuery->paginate(10, ['*'], 'sr_page', $serviceRequestPage);

            // Ambil unique values untuk dropdown filter
            $woStatuses = DB::connection('oracle')
                ->table('WORKORDER')
                ->where('SITEID', 'KD')
                ->distinct()
                ->pluck('STATUS')
                ->filter()
                ->sort()
                ->values();
                
            $woWorkTypes = DB::connection('oracle')
                ->table('WORKORDER')
                ->where('SITEID', 'KD')
                ->distinct()
                ->pluck('WORKTYPE')
                ->filter()
                ->sort()
                ->values();
                
            $srStatuses = DB::connection('oracle')
                ->table('SR')
                ->where('SITEID', 'KD')
                ->distinct()
                ->pluck('STATUS')
                ->filter()
                ->sort()
                ->values();

            return view('admin.maximo.index', [
                'workOrders'      => $this->formatWorkOrders($workOrders->items()),
                'workOrdersPaginator' => $workOrders,
                'serviceRequests' => $this->formatServiceRequests($serviceRequests->items()),
                'serviceRequestsPaginator' => $serviceRequests,
                'woStatuses' => $woStatuses,
                'woWorkTypes' => $woWorkTypes,
                'srStatuses' => $srStatuses,
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

            return view('admin.maximo.index', [
                'workOrders'       => collect([]),
                'workOrdersPaginator' => null,
                'serviceRequests' => collect([]),
                'serviceRequestsPaginator' => null,
                'woStatuses' => collect([]),
                'woWorkTypes' => collect([]),
                'srStatuses' => collect([]),
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

            return view('admin.maximo.index', [
                'workOrders'       => collect([]),
                'workOrdersPaginator' => null,
                'serviceRequests' => collect([]),
                'serviceRequestsPaginator' => null,
                'woStatuses' => collect([]),
                'woWorkTypes' => collect([]),
                'srStatuses' => collect([]),
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
            try {
                return [
                    'wonum'       => $wo->wonum ?? '-',
                    'parent'      => $wo->parent ?? '-',
                    'status'      => $wo->status ?? '-',
                    'statusdate'  => isset($wo->statusdate) && $wo->statusdate
                        ? Carbon::parse($wo->statusdate)->format('d-m-Y H:i')
                        : '-',
                    'worktype'    => $wo->worktype ?? '-',
                    'description' => $wo->description ?? '-',
                    'reportdate'  => isset($wo->reportdate) && $wo->reportdate
                        ? Carbon::parse($wo->reportdate)->format('d-m-Y H:i')
                        : '-',
                    'assetnum'    => $wo->assetnum ?? '-',
                    'wopriority'  => $wo->wopriority ?? '-',
                    'location'    => $wo->location ?? '-',
                    'siteid'      => $wo->siteid ?? '-',
                    'downtime'    => $wo->downtime ?? '-',
                    'schedstart'  => isset($wo->schedstart) && $wo->schedstart
                        ? Carbon::parse($wo->schedstart)->format('d-m-Y H:i')
                        : '-',
                    'schedfinish' => isset($wo->schedfinish) && $wo->schedfinish
                        ? Carbon::parse($wo->schedfinish)->format('d-m-Y H:i')
                        : '-',
                ];
            } catch (\Exception $e) {
                Log::warning('Error formatting work order', [
                    'error' => $e->getMessage(),
                    'wo_data' => $wo ?? null
                ]);
                return [
                    'wonum'       => '-',
                    'parent'      => '-',
                    'status'      => '-',
                    'statusdate'  => '-',
                    'worktype'    => '-',
                    'description' => '-',
                    'reportdate'  => '-',
                    'assetnum'    => '-',
                    'wopriority'  => '-',
                    'location'    => '-',
                    'siteid'      => '-',
                    'downtime'    => '-',
                    'schedstart'  => '-',
                    'schedfinish' => '-',
                ];
            }
        });
    }

    /* ==========================
     * FORMAT SERVICE REQUEST
     * ========================== */
    private function formatServiceRequests($serviceRequests)
    {
        return collect($serviceRequests)->map(function ($sr) {
            try {
                return [
                    'ticketid'    => $sr->ticketid ?? '-',
                    'description' => $sr->description ?? '-',
                    'status'      => $sr->status ?? '-',
                    'statusdate'  => isset($sr->statusdate) && $sr->statusdate
                        ? Carbon::parse($sr->statusdate)->format('d-m-Y H:i')
                        : '-',
                    'siteid'      => $sr->siteid ?? '-',
                    'location'    => $sr->location ?? '-',
                    'assetnum'    => $sr->assetnum ?? '-',
                    'reportedby'  => $sr->reportedby ?? '-',
                    'reportdate'  => isset($sr->reportdate) && $sr->reportdate
                        ? Carbon::parse($sr->reportdate)->format('d-m-Y H:i')
                        : '-',
                ];
            } catch (\Exception $e) {
                Log::warning('Error formatting service request', [
                    'error' => $e->getMessage(),
                    'sr_data' => $sr ?? null
                ]);
                return [
                    'ticketid'    => '-',
                    'description' => '-',
                    'status'      => '-',
                    'statusdate'  => '-',
                    'siteid'      => '-',
                    'location'    => '-',
                    'assetnum'    => '-',
                    'reportedby'  => '-',
                    'reportdate'  => '-',
                ];
            }
        });
    }
}