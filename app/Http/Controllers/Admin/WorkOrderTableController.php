<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WorkOrderTableController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 50);

        try {
            $query = DB::connection('oracle')
                ->table('WORKORDER')
                ->where('STATUS', 'APPR')
                ->where('SITEID', 'KD');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('WONUM', 'LIKE', "%{$search}%")
                        ->orWhere('DESCRIPTION', 'LIKE', "%{$search}%")
                        ->orWhere('ASSETNUM', 'LIKE', "%{$search}%")
                        ->orWhere('LOCATION', 'LIKE', "%{$search}%");
                });
            }

            $workOrders = $query->orderBy('STATUSDATE', 'desc')->paginate($perPage);

            // Transform keys to lowercase for easier blade access if needed, 
            // though Oracle usually returns uppercase keys in some drivers.
            // Laravel's Oracle driver often handles this, but let's be safe.
            
            return view('admin.maximo.full-table', [
                'workOrders' => $workOrders,
                'search' => $search,
            ]);

        } catch (\Exception $e) {
            Log::error('Full WorkOrder Table Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengambil data Work Order: ' . $e->getMessage());
        }
    }
}
