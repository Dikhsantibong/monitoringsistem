<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\WorkOrder;

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
}
