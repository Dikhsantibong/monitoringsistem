<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Barryvdh\DomPDF\Facade\Pdf;

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

            $serviceRequestsQuery->orderBy('REPORTDATE', 'desc');

            $serviceRequests = $serviceRequestsQuery->paginate(10, ['*'], 'sr_page', $serviceRequestPage);

            return view('admin.maximo.index', [
                'workOrders'      => $this->formatWorkOrders($workOrders->items()),
                'workOrdersPaginator' => $workOrders,
                'serviceRequests' => $this->formatServiceRequests($serviceRequests->items()),
                'serviceRequestsPaginator' => $serviceRequests,
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
        });
    }

    /* ==========================
     * GENERATE JOBCARD PDF
     * ========================== */
    public function generateJobcard($wonum)
    {
        try {
            // Ambil data Work Order dari Maximo berdasarkan WONUM
            $workOrder = DB::connection('oracle')
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
                ->where('SITEID', 'KD')
                ->where('WONUM', $wonum)
                ->first();

            if (!$workOrder) {
                return redirect()->route('admin.maximo.index')
                    ->with('error', 'Work Order tidak ditemukan.');
            }

            // Format data untuk PDF
            $jobcardData = [
                'wonum' => $workOrder->wonum ?? '-',
                'parent' => $workOrder->parent ?? '-',
                'status' => $workOrder->status ?? '-',
                'statusdate' => isset($workOrder->statusdate) && $workOrder->statusdate
                    ? Carbon::parse($workOrder->statusdate)->format('d-m-Y H:i')
                    : '-',
                'worktype' => $workOrder->worktype ?? '-',
                'description' => $workOrder->description ?? '-',
                'reportdate' => isset($workOrder->reportdate) && $workOrder->reportdate
                    ? Carbon::parse($workOrder->reportdate)->format('d-m-Y H:i')
                    : '-',
                'assetnum' => $workOrder->assetnum ?? '-',
                'wopriority' => $workOrder->wopriority ?? '-',
                'location' => $workOrder->location ?? '-',
                'siteid' => $workOrder->siteid ?? '-',
                'downtime' => $workOrder->downtime ?? '-',
                'schedstart' => isset($workOrder->schedstart) && $workOrder->schedstart
                    ? Carbon::parse($workOrder->schedstart)->format('d-m-Y H:i')
                    : '-',
                'schedfinish' => isset($workOrder->schedfinish) && $workOrder->schedfinish
                    ? Carbon::parse($workOrder->schedfinish)->format('d-m-Y H:i')
                    : '-',
                'generated_at' => Carbon::now()->format('d-m-Y H:i:s'),
            ];

            // Generate PDF
            $pdf = Pdf::loadView('admin.maximo.jobcard-pdf', ['jobcard' => $jobcardData]);
            
            // Set nama file
            $filename = 'Jobcard_' . $wonum . '_' . Carbon::now()->format('YmdHis') . '.pdf';
            
            // Download PDF
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error generating Jobcard PDF: ' . $e->getMessage(), [
                'wonum' => $wonum,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.maximo.index')
                ->with('error', 'Gagal membuat Jobcard PDF: ' . $e->getMessage());
        }
    }
}