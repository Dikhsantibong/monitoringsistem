<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\WorkOrder;
use App\Models\SRWO;
use App\Models\WoBacklog;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function srWo()
    {
        // Jalankan pengecekan WO yang expired
        $this->checkExpiredWO();

        $serviceRequests = ServiceRequest::all();
        $workOrders = WorkOrder::all();
        
        // Ambil data WO Backlog
        $woBacklogs = WoBacklog::all();     

        return view('admin.laporan.sr_wo', compact('serviceRequests', 'workOrders', 'woBacklogs'));
    }
    

    // Tambah method untuk handle SR
    public function storeSR(Request $request)
    {
        $validated = $request->validate([
            'sr_id' => 'required|numeric|unique:service_requests,id',
            'description' => 'required',
            'status' => 'required',
            'tanggal' => 'required|date',
            'downtime' => 'required',
            'tipe_sr' => 'required',
            'priority' => 'required'
        ]);

        ServiceRequest::create([
            'id' => $request->sr_id,
            'description' => $request->description,
            'status' => $request->status,
            'created_at' => $request->tanggal,
            'downtime' => $request->downtime,
            'tipe_sr' => $request->tipe_sr,
            'priority' => $request->priority
        ]);

        return redirect()->back()->with('success', 'Service Request berhasil ditambahkan');
    }

    // Tambah method untuk handle WO
    public function storeWO(Request $request)
    {
        $validated = $request->validate([
            'wo_id' => 'required|numeric|unique:work_orders,id',
            'description' => 'required',
            'status' => 'required|in:Open,Closed,Comp,APPR,WAPPR,WMATL',
            'priority' => 'required|in:emergency,normal,outage,urgent',
            'schedule_start' => 'required|date',
            'schedule_finish' => 'required|date',
        ]);

        WorkOrder::create([
            'id' => $request->wo_id,
            'description' => $request->description,
            'status' => $request->status,
            'priority' => $request->priority,
            'schedule_start' => $request->schedule_start,
            'schedule_finish' => $request->schedule_finish,
        ]);

        return redirect()->route('admin.laporan.sr_wo')->with('success', 'Work Order berhasil ditambahkan');
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
    public function downloadSrWoClosed()
    {
        $srReports = ServiceRequest::where('status', 'Closed')->get();
        $woReports = WorkOrder::where('status', 'Closed')->get();

        $pdf = PDF::loadView('admin.laporan.sr_wo_closed_pdf', compact('srReports', 'woReports'));
        
        return $pdf->download('laporan-sr-wo-closed.pdf');
    }

    public function printSrWoClosed()
    {
        $srReports = ServiceRequest::where('status', 'Closed')->get();
        $woReports = WorkOrder::where('status', 'Closed')->get();

        return view('admin.laporan.sr_wo_closed_print', compact('srReports', 'woReports'));
    }

    public function updateSRStatus(Request $request, $id)
    {
        $sr = ServiceRequest::findOrFail($id);
        $sr->status = $request->status;
        $sr->save();

        return response()->json(['success' => true]);
    }

    public function updateWOStatus(Request $request, $id)
    {
        $wo = WorkOrder::findOrFail($id);
        $wo->status = $request->status;
        $wo->save();

        return response()->json(['success' => true]);
    }

    public function createSR()
    {
        return view('admin.laporan.create_sr');
    }

    public function createWOBacklog()
    {
        return view('admin.laporan.create_wo_backlog'); // Ganti dengan nama view yang sesuai
    }

    public function createWO()
    {
        return view('admin.laporan.create_wo'); // Ganti dengan nama view yang sesuai
    }

    public function storeWOBacklog(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'no_wo' => 'required|string|max:255|unique:wo_backlog,no_wo',
                'deskripsi' => 'required|string',
                'tanggal_backlog' => 'required|date',
                'keterangan' => 'nullable|string'
            ]);

            // Buat record baru
            $woBacklog = WoBacklog::create([
                'no_wo' => $request->no_wo,
                'deskripsi' => $request->deskripsi,
                'tanggal_backlog' => $request->tanggal_backlog,
                'keterangan' => $request->keterangan
            ]);

            return redirect()
                ->route('admin.laporan.sr_wo')
                ->with('success', 'WO Backlog berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Tambahkan method untuk mengecek dan memindahkan WO yang expired
    private function checkExpiredWO()
    {
        $expiredWOs = WorkOrder::where('schedule_finish', '<', now())
            ->where('status', 'Open')
            ->get();

        foreach ($expiredWOs as $wo) {
            // Pindahkan ke WO Backlog
            WoBacklog::create([
                'no_wo' => $wo->id,
                'deskripsi' => $wo->description,
                'tanggal_backlog' => $wo->schedule_finish,
                'keterangan' => 'Otomatis Terkirim ke Backlog',
                'status' => 'Open'
            ]);

            // Update status WO menjadi COMP (sesuaikan dengan nilai ENUM yang valid di database Anda)
            DB::table('work_orders')
                ->where('id', $wo->id)
                ->update([
                    'status' => 'COMP', // Coba gunakan salah satu dari: COMP, APPR, WAPPR, WMATL
                    'updated_at' => now()
                ]);
        }
    }

    // Method untuk update status WO Backlog
    public function updateBacklogStatus(Request $request, $id)
    {
        $backlog = WoBacklog::findOrFail($id);
        $backlog->status = $request->status;
        $backlog->save();

        return response()->json(['success' => true]);
    }

    // Method untuk edit WO Backlog
    public function editWoBacklog($id)
    {
        $backlog = WoBacklog::findOrFail($id);
        return view('admin.laporan.edit_wo_backlog', compact('backlog'));
    }

    // Method untuk update WO Backlog
    public function updateWoBacklog(Request $request, $id)
    {
        $validated = $request->validate([
            'deskripsi' => 'required|string',
            'keterangan' => 'nullable|string'
        ]);

        $backlog = WoBacklog::findOrFail($id);
        $backlog->update($validated);

        return redirect()
            ->route('admin.laporan.sr_wo')
            ->with('success', 'WO Backlog berhasil diupdate');
    }
}

