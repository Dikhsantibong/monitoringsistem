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

class LaborSayaController extends Controller
{
    public function index()
    {
        $normalizedName = Str::of(Auth::user()->name)
            ->lower()
            ->replace(['-', ' '], '');

        $workOrders = WorkOrder::whereRaw(
            "LOWER(REPLACE(REPLACE(labor, '-', ''), ' ', '')) LIKE ?",
            ['%' . $normalizedName . '%']
        )->get();

        return view('pemeliharaan.labor-saya', compact('workOrders'));
    }

    public function edit($id)
    {
        $workOrder = WorkOrder::findOrFail($id);
        $powerPlants = PowerPlant::all();
        $masterLabors = DB::table('master_labors')->orderBy('nama')->get();

        return view('pemeliharaan.labor-edit', compact('workOrder', 'powerPlants', 'masterLabors'));
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
}
