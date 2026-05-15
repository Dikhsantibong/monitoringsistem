<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class WorkOrderTableController extends Controller
{
    /**
     * Halaman tabel data lengkap Work Order (STATUS = APPR)
     * Temporary controller untuk keperluan pencarian data WO
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 25);
        $sortBy = $request->input('sort_by', 'STATUSDATE');
        $sortDir = $request->input('sort_dir', 'desc');

        // Validasi sort direction
        $sortDir = in_array(strtolower($sortDir), ['asc', 'desc']) ? strtolower($sortDir) : 'desc';

        // Kolom yang bisa di-sort
        $sortableColumns = [
            'WONUM', 'PARENT', 'STATUSDATE', 'WORKTYPE', 'DESCRIPTION',
            'ASSETNUM', 'LOCATION', 'WOPRIORITY', 'REPORTDATE',
            'SCHEDSTART', 'SCHEDFINISH', 'ACTSTART', 'ACTFINISH',
            'TARGSTARTDATE', 'TARGCOMPDATE', 'ESTDUR', 'ESTLABHRS',
            'ACTLABHRS', 'PMNUM', 'JPNUM', 'REPORTEDBY', 'SUPERVISOR',
            'CREWID', 'FAILURECODE', 'PROBLEMCODE',
        ];
        if (!in_array(strtoupper($sortBy), $sortableColumns)) {
            $sortBy = 'STATUSDATE';
        }

        try {
            // Kolom yang akan diambil — pilih kolom-kolom paling penting/relevan
            $columns = [
                'WONUM',
                'PARENT',
                'STATUS',
                'STATUSDATE',
                'WORKTYPE',
                'DESCRIPTION',
                'ASSETNUM',
                'LOCATION',
                'JPNUM',
                'FAILDATE',
                'CHANGEBY',
                'CHANGEDATE',
                'ESTDUR',
                'ESTLABHRS',
                'ESTMATCOST',
                'ESTLABCOST',
                'ESTTOOLCOST',
                'PMNUM',
                'ACTLABHRS',
                'ACTMATCOST',
                'ACTLABCOST',
                'ACTTOOLCOST',
                'HASCHILDREN',
                'OUTLABCOST',
                'OUTMATCOST',
                'OUTTOOLCOST',
                'HISTORYFLAG',
                'CONTRACT',
                'WOPRIORITY',
                'TARGCOMPDATE',
                'TARGSTARTDATE',
                'WOEQ1',
                'WOEQ2',
                'WOEQ3',
                'WOEQ4',
                'WOEQ5',
                'WOEQ6',
                'REPORTEDBY',
                'REPORTDATE',
                'PROBLEMCODE',
                'DOWNTIME',
                'ACTSTART',
                'ACTFINISH',
                'SCHEDSTART',
                'SCHEDFINISH',
                'REMDUR',
                'CREWID',
                'SUPERVISOR',
                'FAILURECODE',
                'ESTSERVCOST',
                'ACTSERVCOST',
                'ORGID',
                'SITEID',
                'WOCLASS',
                'OWNER',
                'OWNERGROUP',
                'PERSONGROUP',
                'LEAD',
                'ORIGRECORDID',
                'ORIGRECORDCLASS',
                'ANGGARAN',
                'WONUMPLN',
                'REMARKDESCC',
                'REMARKDESCP',
                'REMARKDESCPLN',
                'REMARKDESCR',
                'GLACCOUNT',
            ];

            $query = DB::connection('oracle')
                ->table('WORKORDER')
                ->select($columns)
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%');

            // Search across multiple relevant columns
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('WONUM', 'LIKE', "%{$search}%")
                        ->orWhere('PARENT', 'LIKE', "%{$search}%")
                        ->orWhere('DESCRIPTION', 'LIKE', "%{$search}%")
                        ->orWhere('ASSETNUM', 'LIKE', "%{$search}%")
                        ->orWhere('LOCATION', 'LIKE', "%{$search}%")
                        ->orWhere('WORKTYPE', 'LIKE', "%{$search}%")
                        ->orWhere('JPNUM', 'LIKE', "%{$search}%")
                        ->orWhere('PMNUM', 'LIKE', "%{$search}%")
                        ->orWhere('REPORTEDBY', 'LIKE', "%{$search}%")
                        ->orWhere('SUPERVISOR', 'LIKE', "%{$search}%")
                        ->orWhere('LEAD', 'LIKE', "%{$search}%")
                        ->orWhere('FAILURECODE', 'LIKE', "%{$search}%")
                        ->orWhere('PROBLEMCODE', 'LIKE', "%{$search}%")
                        ->orWhere('ANGGARAN', 'LIKE', "%{$search}%")
                        ->orWhere('WONUMPLN', 'LIKE', "%{$search}%")
                        ->orWhere('ORIGRECORDID', 'LIKE', "%{$search}%")
                        ->orWhere('PERSONGROUP', 'LIKE', "%{$search}%")
                        ->orWhere('CREWID', 'LIKE', "%{$search}%");
                });
            }

            // Hitung total sebelum pagination
            $totalRecords = (clone $query)->count();

            $query->orderBy($sortBy, $sortDir);

            $workOrders = $query->paginate($perPage);

            return view('admin.workorder-table.index', [
                'workOrders' => $workOrders,
                'search' => $search,
                'perPage' => $perPage,
                'sortBy' => $sortBy,
                'sortDir' => $sortDir,
                'totalRecords' => $totalRecords,
                'error' => null,
            ]);

        } catch (QueryException $e) {
            Log::error('ORACLE QUERY ERROR (WO TABLE)', [
                'oracle_code' => $e->errorInfo[1] ?? null,
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
            ]);

            return view('admin.workorder-table.index', [
                'workOrders' => null,
                'search' => $search,
                'perPage' => $perPage,
                'sortBy' => $sortBy,
                'sortDir' => $sortDir,
                'totalRecords' => 0,
                'error' => 'Gagal mengambil data dari Maximo (Query Error): ' . $e->getMessage(),
            ]);

        } catch (\Throwable $e) {
            Log::error('ORACLE GENERAL ERROR (WO TABLE)', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return view('admin.workorder-table.index', [
                'workOrders' => null,
                'search' => $search,
                'perPage' => $perPage,
                'sortBy' => $sortBy,
                'sortDir' => $sortDir,
                'totalRecords' => 0,
                'error' => 'Gagal mengambil data dari Maximo: ' . $e->getMessage(),
            ]);
        }
    }
}
