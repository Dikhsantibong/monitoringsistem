<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder as LocalWorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

            // Ambil document_path jobcard dari DB utama (mysql) untuk WONUM yang tampil (jika ada)
            $wonums = collect($workOrders->items())
                ->pluck('wonum')
                ->filter()
                ->values()
                ->all();
            $jobcardPaths = [];
            if (!empty($wonums)) {
                $jobcardPaths = DB::connection('mysql')
                    ->table('work_orders')
                    ->whereIn('id', $wonums)
                    ->whereNotNull('document_path')
                    ->pluck('document_path', 'id')
                    ->toArray();
            }

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
                'workOrders'      => $this->formatWorkOrders($workOrders->items(), $jobcardPaths),
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

    public function generateJobcard(string $wonum)
    {
        // Hanya akun session mysql yang boleh generate
        if (session('unit', 'mysql') !== 'mysql') {
            abort(403, 'Anda tidak memiliki akses untuk generate Jobcard.');
        }

        try {
            $wonum = trim($wonum);
            if ($wonum === '') {
                return redirect()
                    ->route('admin.maximo.index')
                    ->with('error', 'WONUM tidak valid.');
            }

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
                return redirect()
                    ->route('admin.maximo.index')
                    ->with('error', 'Work Order tidak ditemukan di Maximo.');
            }

            $jobcard = [
                'wonum' => $workOrder->wonum ?? $wonum,
                'parent' => $workOrder->parent ?? '-',
                'status' => $workOrder->status ?? '-',
                'statusdate' => isset($workOrder->statusdate) && $workOrder->statusdate
                    ? Carbon::parse($workOrder->statusdate)->format('d-m-Y H:i')
                    : '-',
                'worktype' => $workOrder->worktype ?? '-',
                'priority' => $workOrder->wopriority ?? '-',
                'description' => $workOrder->description ?? '-',
                'assetnum' => $workOrder->assetnum ?? '-',
                'location' => $workOrder->location ?? '-',
                'schedstart' => isset($workOrder->schedstart) && $workOrder->schedstart
                    ? Carbon::parse($workOrder->schedstart)->format('d-m-Y H:i')
                    : '-',
                'schedfinish' => isset($workOrder->schedfinish) && $workOrder->schedfinish
                    ? Carbon::parse($workOrder->schedfinish)->format('d-m-Y H:i')
                    : '-',
                'reportdate' => isset($workOrder->reportdate) && $workOrder->reportdate
                    ? Carbon::parse($workOrder->reportdate)->format('d-m-Y H:i')
                    : '-',
                'siteid' => $workOrder->siteid ?? 'KD',
                'downtime' => $workOrder->downtime ?? '-',
                'generated_at' => Carbon::now()->format('d-m-Y H:i'),
            ];

            // Simpan PDF dengan nama stabil agar bisa diedit/overwrite via pdf.js
            $safeWonum = preg_replace('/[^A-Za-z0-9_\-]/', '_', $wonum);
            $fileName = "Jobcard_{$safeWonum}.pdf";
            $documentPath = "work-orders/{$fileName}";

            $pdf = Pdf::loadView('admin.maximo.jobcard-pdf', [
                'jobcard' => $jobcard,
            ])->setPaper('a4', 'portrait');

            Storage::disk('public')->put($documentPath, $pdf->output());

            // Catat/Update di DB utama (mysql) supaya bisa dibaca akun pemeliharaan
            LocalWorkOrder::on('mysql')->updateOrCreate(
                ['id' => $wonum],
                [
                    'description' => $workOrder->description ?? null,
                    'type' => $workOrder->worktype ?? null,
                    'status' => $workOrder->status ?? null,
                    'priority' => (string)($workOrder->wopriority ?? 'normal'),
                    'schedule_start' => isset($workOrder->schedstart) && $workOrder->schedstart ? Carbon::parse($workOrder->schedstart) : null,
                    'schedule_finish' => isset($workOrder->schedfinish) && $workOrder->schedfinish ? Carbon::parse($workOrder->schedfinish) : null,
                    'document_path' => $documentPath,
                    'unit_source' => 'mysql',
                    'labor' => null,
                    'labors' => [],
                    'materials' => [],
                ]
            );

            return redirect()
                ->back()
                ->with('success', "Jobcard {$wonum} berhasil digenerate.");

        } catch (\Throwable $e) {
            Log::error('Error generating Jobcard PDF', [
                'wonum' => $wonum,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Gagal membuat Jobcard PDF: ' . $e->getMessage());
        }
    }

    /* ==========================
     * FORMAT WORK ORDER
     * ========================== */
    private function formatWorkOrders($workOrders, array $jobcardPaths = [])
    {
        return collect($workOrders)->map(function ($wo) use ($jobcardPaths) {
            $wonum = $wo->wonum ?? '-';
            $jobcardPath = $wonum !== '-' ? ($jobcardPaths[$wonum] ?? null) : null;
            return [
                'wonum'       => $wonum,
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
                'jobcard_path' => $jobcardPath,
                'jobcard_url' => $jobcardPath ? url('storage/' . $jobcardPath) : null,
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
}