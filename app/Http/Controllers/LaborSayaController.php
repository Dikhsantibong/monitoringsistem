<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        $workOrder = \App\Models\WorkOrder::findOrFail($id);
        $powerPlants = \App\Models\PowerPlant::all();
        return view('pemeliharaan.labor-edit', compact('workOrder', 'powerPlants'));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $workOrder = \App\Models\WorkOrder::findOrFail($id);
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
        // Validasi dan update field lain (form biasa)
        $request->validate([
            'kendala' => 'nullable|string',
            'tindak_lanjut' => 'nullable|string',
            'status' => 'required|in:Open,Closed,Comp,APPR,WAPPR,WMATL',
            'document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
        ]);
        $workOrder->kendala = $request->kendala;
        $workOrder->tindak_lanjut = $request->tindak_lanjut;
        $workOrder->status = $request->status;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $path = $file->storeAs('work-orders', $fileName, 'public');
            $workOrder->document_path = $path;
        }
        $workOrder->save();
        return redirect()->route('pemeliharaan.labor-saya')->with('success', 'Work Order berhasil diupdate');
    }
}
