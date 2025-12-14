<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\PowerPlant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\WoBacklog;
use App\Models\MaterialMaster;

class LaborSayaController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));
        $normalizedName = Str::of(Auth::user()->name)
            ->lower()
            ->replace(['-', ' '], '');

        $workOrders = WorkOrder::whereRaw(
                "LOWER(REPLACE(REPLACE(labor, '-', ''), ' ', '')) LIKE ?",
                ['%' . $normalizedName . '%']
            )
            ->when($search !== '', function ($query) use ($search) {
                $like = "%{$search}%";
                $query->where(function ($q) use ($like) {
                    $q->where('id', 'LIKE', $like)
                      ->orWhere('description', 'LIKE', $like)
                      ->orWhere('kendala', 'LIKE', $like)
                      ->orWhere('tindak_lanjut', 'LIKE', $like)
                      ->orWhere('status', 'LIKE', $like)
                      ->orWhere('type', 'LIKE', $like)
                      ->orWhere('priority', 'LIKE', $like);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $laborBacklogs = WoBacklog::whereRaw(
                "LOWER(REPLACE(REPLACE(labor, '-', ''), ' ', '')) LIKE ?",
                ['%' . $normalizedName . '%']
            )
            ->when($search !== '', function ($query) use ($search) {
                $like = "%{$search}%";
                $query->where(function ($q) use ($like) {
                    $q->where('no_wo', 'LIKE', $like)
                      ->orWhere('deskripsi', 'LIKE', $like)
                      ->orWhere('kendala', 'LIKE', $like)
                      ->orWhere('tindak_lanjut', 'LIKE', $like)
                      ->orWhere('status', 'LIKE', $like)
                      ->orWhere('type_wo', 'LIKE', $like)
                      ->orWhere('priority', 'LIKE', $like);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $warehouseKeywords = [
            'UPDK' => '2020',
            'PLTD WUA WUA' => '3011',
            'PLTD BAU BAU' => '3012',
            'PLTD KOLAKA' => '3013',
            'PLTD POASIA' => '3014',
            'PLTU TANASA' => '3015',
            'PLTD RAHA' => '3016',
            'PLTD WANGI' => '3017',
            'PLTD LAMBUYA' => '3018',
            'PLTMG TANASA' => '3022',
            'PLTM MIKUASI' => '3023',
            'PLTD PASARWAJO' => '3035',
            'PLTD LADUMPI' => '3047',
            'PLTD LANIPA' => '4048',
            'PLTD EREKE' => '3049',
            'PLTD LANGARA' => '3050',
            'PLTM RONGI' => '3054',
            'PLTMG BAU BAU' => '3053',
        ];
        $warehouseCode = null;
        if ($search) {
            foreach ($warehouseKeywords as $keyword => $code) {
                if (stripos($search, $keyword) !== false || stripos($keyword, $search) !== false) {
                    $warehouseCode = $code;
                    break;
                }
            }
        }

        return view('pemeliharaan.labor-saya', [
            'workOrders' => $workOrders,
            'laborBacklogs' => $laborBacklogs,
            'q' => $search,
        ]);
    }

    public function edit($id)
    {
        $workOrder = WorkOrder::findOrFail($id);
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