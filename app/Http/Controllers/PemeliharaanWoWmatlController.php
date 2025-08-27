<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\PowerPlant;
use App\Models\MaterialMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PemeliharaanWoWmatlController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $userName = Auth::user()->name;
        $normalizedName = Str::of($userName)->lower()->replace(['-', ' '], '');
        $query = WorkOrder::where('status', 'WMATL')
            ->whereRaw("LOWER(REPLACE(REPLACE(labor, '-', ''), ' ', '')) LIKE ?", ['%' . $normalizedName . '%']);
        if ($search) {
            $like = "%{$search}%";
            $query->where(function($q) use ($like) {
                $q->where('id', 'LIKE', $like)
                  ->orWhere('description', 'LIKE', $like)
                  ->orWhere('kendala', 'LIKE', $like)
                  ->orWhere('tindak_lanjut', 'LIKE', $like)
                  ->orWhere('priority', 'LIKE', $like)
                  ->orWhere('type', 'LIKE', $like);
            });
        }
        $workOrders = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('pemeliharaan.wo-wmatl-index', compact('workOrders', 'search'));
    }

    public function edit($id)
    {
        $workOrder = WorkOrder::findOrFail($id);
        $powerPlants = PowerPlant::all();
        $materials = MaterialMaster::orderBy('description')->limit(200)->get();
        return view('pemeliharaan.wo-wmatl-edit', compact('workOrder', 'powerPlants', 'materials'));
    }

    public function update(Request $request, $id)
    {
        $workOrder = WorkOrder::findOrFail($id);
        // Jika hanya upload dokumen (AJAX PDF), tidak perlu validasi field lain
        if ($request->hasFile('document') && $request->file('document')->isValid()) {
            $file = $request->file('document');
            $fileName = basename($workOrder->document_path);
            if ($workOrder->document_path && Storage::disk('public')->exists($workOrder->document_path)) {
                Storage::disk('public')->delete($workOrder->document_path);
            }
            $path = $file->storeAs('work-orders', $fileName, 'public');
            $workOrder->document_path = $path;
            $workOrder->save();
            return response()->json(['success' => true]);
        }
        DB::beginTransaction();
        try {
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
            if ($request->hasFile('document') && $request->file('document')->isValid()) {
                if ($workOrder->document_path && Storage::disk('public')->exists($workOrder->document_path)) {
                    Storage::disk('public')->delete($workOrder->document_path);
                }
                $file = $request->file('document');
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $path = $file->storeAs('work-orders', $fileName, 'public');
                $data['document_path'] = $path;
            }
            $workOrder->update($data);
            DB::commit();
            return redirect()->route('pemeliharaan.wo-wmatl.index')->with('success', 'Work Order berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating WO WMATL:', [
                'wo_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('pemeliharaan.wo-wmatl.index')->with('error', 'Gagal mengupdate Work Order: ' . $e->getMessage());
        }
    }
}
