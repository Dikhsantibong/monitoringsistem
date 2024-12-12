<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\WorkOrder;
use App\Models\SRWO;

class LaporanController extends Controller
{
    public function srWo()
    {
        $serviceRequests = ServiceRequest::all();
        $workOrders = WorkOrder::all();
        return view('admin.laporan.sr_wo', compact('serviceRequests', 'workOrders'));
    }

    // Tambah method untuk handle SR
    public function storeSR(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required',
            'status' => 'required'
        ]);

        ServiceRequest::create($validated);

        return redirect()->back()->with('success', 'Service Request berhasil ditambahkan');
    }

    // Tambah method untuk handle WO
    public function storeWO(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required',
            'status' => 'required'
        ]);

        WorkOrder::create($validated);

        return redirect()->back()->with('success', 'Work Order berhasil ditambahkan');
    }

    public function srWoClosed()
    {
        // Ambil SR yang closed
        $closedSR = ServiceRequest::where('status', 'Closed')
            ->select('id', 'description as deskripsi', 'status', 'created_at as tanggal')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor' => 'SR-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                    'tanggal' => $item->tanggal,
                    'deskripsi' => $item->deskripsi,
                    'status' => $item->status,
                    'tipe' => 'SR'
                ];
            });

        // Ambil WO yang closed
        $closedWO = WorkOrder::where('status', 'Closed')
            ->select('id', 'description as deskripsi', 'status', 'created_at as tanggal')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor' => 'WO-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                    'tanggal' => $item->tanggal,
                    'deskripsi' => $item->deskripsi,
                    'status' => $item->status,
                    'tipe' => 'WO'
                ];
            });

        // Gabungkan dan urutkan berdasarkan tanggal
        $closedReports = $closedSR->concat($closedWO)
            ->sortByDesc('tanggal')
            ->values();

        return view('admin.laporan.sr_wo_closed', compact('closedReports'));
    }
}
