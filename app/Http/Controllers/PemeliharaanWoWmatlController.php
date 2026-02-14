<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PemeliharaanWoWmatlController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        try {
            $query = DB::connection('oracle')
                ->table('WORKORDER')
                ->where('SITEID', 'KD')
                ->where('STATUS', 'WMATL');

            if ($search) {
                $like = "%" . strtoupper($search) . "%";
                $query->where(function($q) use ($like) {
                    $q->where('WONUM', 'LIKE', $like)
                      ->orWhere('DESCRIPTION', 'LIKE', $like);
                });
            }

            $workOrdersPaginator = $query->orderBy('STATUSDATE', 'desc')->paginate(10);
            
            // Format data for view consistency
            $workOrders = collect($workOrdersPaginator->items())->map(function($wo) {
                $wo = (object) array_change_key_case((array) $wo, CASE_LOWER);
                return (object) [
                    'id' => $wo->wonum,
                    'description' => $wo->description,
                    'status' => $wo->status,
                    'labor' => $wo->lead ?? '-', // Using lead if available, otherwise '-'
                    'schedule_start' => $wo->schedstart ? \Carbon\Carbon::parse($wo->schedstart)->format('d/m/Y') : '-',
                    'schedule_finish' => $wo->schedfinish ? \Carbon\Carbon::parse($wo->schedfinish)->format('d/m/Y') : '-',
                ];
            });

            return view('pemeliharaan.wo-wmatl-index', compact('workOrders', 'workOrdersPaginator', 'search'));
        } catch (\Exception $e) {
            Log::error('Error fetching WO WMATL from Oracle: ' . $e->getMessage());
            $workOrders = collect();
            $workOrdersPaginator = null;
            return view('pemeliharaan.wo-wmatl-index', compact('workOrders', 'workOrdersPaginator', 'search'))
                ->with('error', 'Gagal mengambil data dari Oracle: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $wo = DB::connection('oracle')
                ->table('WORKORDER')
                ->where('SITEID', 'KD')
                ->where('WONUM', $id)
                ->first();

            if (!$wo) {
                return redirect()->route('pemeliharaan.wo-wmatl.index')->with('error', 'Work Order tidak ditemukan di Oracle.');
            }

            $wo = (object) array_change_key_case((array) $wo, CASE_LOWER);
            
            // Format for view
            $workOrder = (object) [
                'id' => $wo->wonum,
                'wonum' => $wo->wonum,
                'description' => $wo->description,
                'status' => $wo->status,
                'type' => $wo->worktype,
                'priority' => $wo->wopriority,
                'labor' => $wo->lead ?? '-',
                'location' => $wo->location ?? '-',
                'schedule_start' => $wo->schedstart ? \Carbon\Carbon::parse($wo->schedstart)->format('d/m/Y') : '-',
                'schedule_finish' => $wo->schedfinish ? \Carbon\Carbon::parse($wo->schedfinish)->format('d/m/Y') : '-',
                'kendala' => null,
                'tindak_lanjut' => null,
                'document_path' => null,
            ];

            return view('pemeliharaan.wo-wmatl-edit', compact('workOrder'));
        } catch (\Exception $e) {
            Log::error('Error fetching WO WMATL detail from Oracle: ' . $e->getMessage());
            return redirect()->route('pemeliharaan.wo-wmatl.index')->with('error', 'Gagal mengambil detail dari Oracle.');
        }
    }

    public function update(Request $request, $id)
    {
        // Removed local DB updates as per Oracle data transition
        return redirect()->route('pemeliharaan.wo-wmatl.index')->with('info', 'Update dinonaktifkan untuk data Oracle.');
    }
}
