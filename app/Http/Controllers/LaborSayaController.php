<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PowerPlant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\MaterialMaster;
use Carbon\Carbon;

class LaborSayaController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));
        $normalizedName = Str::of(Auth::user()->name)
            ->lower()
            ->replace(['-', ' '], '');

        // Get Work Orders dari Maximo (Oracle)
        $workOrders = collect();
        try {
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
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%'); // Hanya ambil WO yang dimulai dengan "WO"

            // Search filter
            if ($search !== '') {
                $workOrdersQuery->where(function ($q) use ($search) {
                    $like = "%{$search}%";
                    $q->where('WONUM', 'LIKE', $like)
                      ->orWhere('DESCRIPTION', 'LIKE', $like)
                      ->orWhere('STATUS', 'LIKE', $like)
                      ->orWhere('WORKTYPE', 'LIKE', $like)
                      ->orWhere('WOPRIORITY', 'LIKE', $like)
                      ->orWhere('LOCATION', 'LIKE', $like)
                      ->orWhere('ASSETNUM', 'LIKE', $like);
                });
            }

            $workOrdersQuery->orderBy('STATUSDATE', 'desc');

            $workOrdersRaw = $workOrdersQuery->get();

            // Format data untuk view
            $workOrders = $workOrdersRaw->map(function ($wo) {
                return [
                    'id' => $wo->wonum ?? '-',
                    'wonum' => $wo->wonum ?? '-',
                    'parent' => $wo->parent ?? '-',
                    'description' => $wo->description ?? '-',
                    'status' => $wo->status ?? '-',
                    'statusdate' => isset($wo->statusdate) && $wo->statusdate
                        ? Carbon::parse($wo->statusdate)->format('Y-m-d H:i:s')
                        : null,
                    'worktype' => $wo->worktype ?? '-',
                    'type' => $wo->worktype ?? '-',
                    'wopriority' => $wo->wopriority ?? '-',
                    'priority' => $wo->wopriority ?? '-',
                    'assetnum' => $wo->assetnum ?? '-',
                    'location' => $wo->location ?? '-',
                    'siteid' => $wo->siteid ?? '-',
                    'downtime' => $wo->downtime ?? '-',
                    'schedstart' => isset($wo->schedstart) && $wo->schedstart
                        ? Carbon::parse($wo->schedstart)->format('Y-m-d H:i:s')
                        : null,
                    'schedule_start' => isset($wo->schedstart) && $wo->schedstart
                        ? Carbon::parse($wo->schedstart)->format('Y-m-d H:i:s')
                        : null,
                    'schedfinish' => isset($wo->schedfinish) && $wo->schedfinish
                        ? Carbon::parse($wo->schedfinish)->format('Y-m-d H:i:s')
                        : null,
                    'schedule_finish' => isset($wo->schedfinish) && $wo->schedfinish
                        ? Carbon::parse($wo->schedfinish)->format('Y-m-d H:i:s')
                        : null,
                    'reportdate' => isset($wo->reportdate) && $wo->reportdate
                        ? Carbon::parse($wo->reportdate)->format('Y-m-d H:i:s')
                        : null,
                    'created_at' => isset($wo->reportdate) && $wo->reportdate
                        ? Carbon::parse($wo->reportdate)->format('Y-m-d H:i:s')
                        : Carbon::now()->format('Y-m-d H:i:s'),
                    'kendala' => null, // Tidak ada di Maximo
                    'tindak_lanjut' => null, // Tidak ada di Maximo
                    'labor' => null, // Tidak ada di Maximo
                    'labors' => [], // Tidak ada di Maximo
                    'document_path' => null, // Tidak ada di Maximo
                    'power_plant_name' => $wo->location ?? ($wo->siteid ?? 'KD'),
                ];
            });

        } catch (\Exception $e) {
            Log::error('Error getting Work Orders from Maximo in LaborSayaController: ' . $e->getMessage());
            $workOrders = collect([]);
        }

        // Backlog tidak ada di Maximo, set empty collection
        $laborBacklogs = collect([]);

        return view('pemeliharaan.labor-saya', [
            'workOrders' => $workOrders,
            'laborBacklogs' => $laborBacklogs,
            'q' => $search,
        ]);
    }

    public function edit($id)
    {
        // Ambil data dari Maximo berdasarkan WONUM
        try {
            $workOrderRaw = DB::connection('oracle')
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
                ->where('WONUM', $id)
                ->first();

            if (!$workOrderRaw) {
                return redirect()->route('pemeliharaan.labor-saya')
                    ->with('error', 'Work Order tidak ditemukan di Maximo.');
            }

            // Format data untuk view
            $workOrder = (object) [
                'id' => $workOrderRaw->wonum ?? '-',
                'wonum' => $workOrderRaw->wonum ?? '-',
                'parent' => $workOrderRaw->parent ?? '-',
                'description' => $workOrderRaw->description ?? '-',
                'status' => $workOrderRaw->status ?? '-',
                'worktype' => $workOrderRaw->worktype ?? '-',
                'type' => $workOrderRaw->worktype ?? '-',
                'wopriority' => $workOrderRaw->wopriority ?? '-',
                'priority' => $workOrderRaw->wopriority ?? '-',
                'assetnum' => $workOrderRaw->assetnum ?? '-',
                'location' => $workOrderRaw->location ?? '-',
                'siteid' => $workOrderRaw->siteid ?? '-',
                'downtime' => $workOrderRaw->downtime ?? '-',
                'schedstart' => isset($workOrderRaw->schedstart) && $workOrderRaw->schedstart
                    ? Carbon::parse($workOrderRaw->schedstart)->format('Y-m-d H:i:s')
                    : null,
                'schedule_start' => isset($workOrderRaw->schedstart) && $workOrderRaw->schedstart
                    ? Carbon::parse($workOrderRaw->schedstart)->format('Y-m-d H:i:s')
                    : null,
                'schedfinish' => isset($workOrderRaw->schedfinish) && $workOrderRaw->schedfinish
                    ? Carbon::parse($workOrderRaw->schedfinish)->format('Y-m-d H:i:s')
                    : null,
                'schedule_finish' => isset($workOrderRaw->schedfinish) && $workOrderRaw->schedfinish
                    ? Carbon::parse($workOrderRaw->schedfinish)->format('Y-m-d H:i:s')
                    : null,
                'reportdate' => isset($workOrderRaw->reportdate) && $workOrderRaw->reportdate
                    ? Carbon::parse($workOrderRaw->reportdate)->format('Y-m-d H:i:s')
                    : null,
                'kendala' => null,
                'tindak_lanjut' => null,
                'labor' => null,
                'labors' => [],
                'document_path' => null,
                'power_plant_id' => null,
                'power_plant_name' => $workOrderRaw->location ?? ($workOrderRaw->siteid ?? 'KD'),
            ];

        } catch (\Exception $e) {
            Log::error('Error getting Work Order from Maximo in LaborSayaController::edit: ' . $e->getMessage());
            return redirect()->route('pemeliharaan.labor-saya')
                ->with('error', 'Gagal mengambil data Work Order dari Maximo.');
        }

        $powerPlants = PowerPlant::all();
        $userName = Auth::user()->name;
        $masterLabors = DB::table('master_labors')->where('unit', $userName)->orderBy('nama')->get();
        $materials = MaterialMaster::orderBy('description')->limit(200)->get();

        return view('pemeliharaan.labor-edit', compact('workOrder', 'powerPlants', 'masterLabors', 'materials'));
    }

    public function update(Request $request, $id)
    {
        $workOrder = WorkOrder::findOrFail($id);
        // Jika hanya upload dokumen (AJAX PDF), tidak perlu validasi field lain
        if ($request->hasFile('document') && $request->file('document')->isValid()) {
            $file = $request->file('document');
            $fileName = basename($workOrder->document_path);
            if ($workOrder->document_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($workOrder->document_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($workOrder->document_path);
            }
            $path = $file->storeAs('work-orders', $fileName, 'public');
            $workOrder->document_path = $path;
            $workOrder->save();
            return response()->json(['success' => true]);
        }

        DB::beginTransaction();

        try {
            $workOrder = WorkOrder::findOrFail($id);

            // Validasi untuk update biasa
            $request->validate([
                'description'    => 'nullable|string',
                'kendala'        => 'nullable|string',
                'tindak_lanjut'  => 'nullable|string',
                'type'           => 'nullable|string',
                'priority'       => 'nullable|string',
                'schedule_start' => 'nullable|date',
                'schedule_finish'=> 'nullable|date',
                'unit'           => 'nullable|integer|exists:power_plants,id',
                'labor'          => 'nullable|string',
                'status'         => 'required|in:Open,Closed,Comp,APPR,WAPPR,WMATL',
                'document'       => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
                'labors'         => 'nullable|array',
                'labors.*'       => 'string|max:100',
                'materials'      => 'nullable|array',
                'materials.*.code' => 'required_with:materials|string|max:100',
                'materials.*.qty'  => 'nullable|numeric|min:0',
                'materials.*.description' => 'required_with:materials|string|max:255',
                'materials.*.inventory_statistic_desc' => 'required_with:materials|string|max:255',
                'materials.*.inventory_statistic_code' => 'required_with:materials|string|max:255',
            ]);

            // Data yang akan diupdate
            $data = [
                'description'      => $request->description,
                'kendala'          => $request->kendala,
                'tindak_lanjut'    => $request->tindak_lanjut,
                'type'             => $request->type,
                'priority'         => $request->priority,
                'schedule_start'   => $request->schedule_start,
                'schedule_finish'  => $request->schedule_finish,
                'power_plant_id'   => $request->unit,
                'labor'            => $request->labor,
                'status'           => $request->status,
                'labors'           => $request->labors ?? [],
                'materials'        => $request->materials ?? [],
            ];

            // Cek jika ada file document baru
            if ($request->hasFile('document') && $request->file('document')->isValid()) {
                if ($workOrder->document_path && Storage::disk('public')->exists($workOrder->document_path)) {
                    Storage::disk('public')->delete($workOrder->document_path);
                }
                $file = $request->file('document');
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $path = $file->storeAs('work-orders', $fileName, 'public');
                $data['document_path'] = $path;
            }

            // Update di database utama (local/unit)
            $workOrder->update($data);

            // Sinkronisasi ke database utama jika unit_source !== 'mysql'
            if ($workOrder->unit_source !== 'mysql') {
                try {
                    // Pastikan labors adalah JSON string
                    $syncData = $data;
                    if (isset($syncData['labors']) && is_array($syncData['labors'])) {
                        $syncData['labors'] = json_encode($syncData['labors']);
                    }
                    if (isset($syncData['materials']) && is_array($syncData['materials'])) {
                        $syncData['materials'] = json_encode($syncData['materials']);
                    }
                    DB::connection('mysql')
                        ->table('work_orders')
                        ->where('id', $workOrder->id)
                        ->update($syncData);
                } catch (\Exception $e) {
                    Log::warning('Sync to main DB failed:', [
                        'error' => $e->getMessage(),
                        'wo_id' => $workOrder->id
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('pemeliharaan.labor-saya')
                ->with('success', 'Work Order berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating WO:', [
                'wo_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('pemeliharaan.labor-saya')
                ->with('error', 'Gagal mengupdate Work Order: ' . $e->getMessage());
        }
    }

    public function editBacklog($id)
    {
        $backlog = WoBacklog::findOrFail($id);
        // Pastikan hanya labor yang sesuai yang bisa edit
        $normalizedName = Str::of(Auth::user()->name)->lower()->replace(['-', ' '], '');
        $backlogLabor = Str::of($backlog->labor)->lower()->replace(['-', ' '], '');
        if (strpos($backlogLabor, $normalizedName) === false) {
            abort(403, 'Anda tidak berhak mengedit backlog ini.');
        }
        $materials = MaterialMaster::orderBy('description')->get();
        $userName = Auth::user()->name;
        $masterLabors = DB::table('master_labors')->where('unit', $userName)->orderBy('nama')->get();
        // Ambil existing materials dari backlog (pastikan kolom materials di-cast ke array)
        $existingMaterials = [];
        if (!empty($backlog->materials)) {
            $existingMaterials = is_array($backlog->materials)
                ? $backlog->materials
                : (is_string($backlog->materials) ? json_decode($backlog->materials, true) : []);
        }
        return view('pemeliharaan.labor-edit-backlog', compact('backlog', 'masterLabors', 'materials', 'existingMaterials'));
    }

    public function updateBacklog(Request $request, $id)
    {
        $backlog = WoBacklog::findOrFail($id);
        $normalizedName = Str::of(Auth::user()->name)->lower()->replace(['-', ' '], '');
        $backlogLabor = Str::of($backlog->labor)->lower()->replace(['-', ' '], '');
        if (strpos($backlogLabor, $normalizedName) === false) {
            abort(403, 'Anda tidak berhak mengedit backlog ini.');
        }
        // Jika hanya upload dokumen (AJAX PDF), tidak perlu validasi field lain
        if ($request->hasFile('document') && $request->file('document')->isValid()) {
            if ($backlog->document_path && Storage::disk('public')->exists($backlog->document_path)) {
                Storage::disk('public')->delete($backlog->document_path);
            }
            $file = $request->file('document');
            $fileName = basename($backlog->document_path) ?: (time() . '_' . str_replace(' ', '_', $file->getClientOriginalName()));
            $path = $file->storeAs('wo-backlog', $fileName, 'public');
            $backlog->document_path = $path;
            $backlog->save();
            return response()->json(['success' => true]);
        }
        $request->validate([
            'deskripsi' => 'required|string',
            'kendala' => 'nullable|string',
            'tindak_lanjut' => 'nullable|string',
            'status' => 'required|in:Open,Closed,WMATL',
            'keterangan' => 'nullable|string',
            'labors' => 'nullable|array',
            'labor' => 'nullable|string',
            'labors.*' => 'string|max:100',
            'document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'materials' => 'nullable|array',
            'materials.*.code' => 'required_with:materials|string|max:100',
            'materials.*.qty' => 'nullable|numeric|min:0',
        ]);
        $data = [
            'deskripsi' => $request->deskripsi,
            'kendala' => $request->kendala,
            'tindak_lanjut' => $request->tindak_lanjut,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'labors' => $request->labors ?? [],
            'labor' => $backlog->labor,
            'materials' => $request->materials ?? [],
        ];
        if ($request->hasFile('document') && $request->file('document')->isValid()) {
            if ($backlog->document_path && Storage::disk('public')->exists($backlog->document_path)) {
                Storage::disk('public')->delete($backlog->document_path);
            }
            $file = $request->file('document');
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $path = $file->storeAs('wo-backlog', $fileName, 'public');
            $data['document_path'] = $path;
        }
        $backlog->update($data);
        return redirect()->route('pemeliharaan.labor-saya')->with('success', 'WO Backlog berhasil diupdate');
    }
}