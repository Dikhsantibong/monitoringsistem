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
    public function srWo(Request $request)
    {
        try {
            // Cek WO yang expired dan pindahkan ke backlog
            $this->checkExpiredWO();

            // Query untuk Service Requests
            $serviceRequests = ServiceRequest::query()
                ->select('id', 'description', 'status', 'created_at', 'downtime', 'tipe_sr', 'priority')
                ->when($request->filled(['tanggal_mulai', 'tanggal_akhir']), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [
                        $request->tanggal_mulai . ' 00:00:00',
                        $request->tanggal_akhir . ' 23:59:59'
                    ]);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Query untuk Work Orders
            $workOrders = WorkOrder::query()
                ->select('id', 'description', 'status', 'created_at', 'priority', 'schedule_start', 'schedule_finish')
                ->when($request->filled(['tanggal_mulai', 'tanggal_akhir']), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [
                        $request->tanggal_mulai . ' 00:00:00',
                        $request->tanggal_akhir . ' 23:59:59'
                    ]);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Query untuk WO Backlog
            $woBacklogs = WoBacklog::query()
                ->select('id', 'no_wo', 'deskripsi', 'tanggal_backlog', 'keterangan', 'status', 'created_at')
                ->when($request->filled(['tanggal_mulai', 'tanggal_akhir']), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [
                        $request->tanggal_mulai . ' 00:00:00',
                        $request->tanggal_akhir . ' 23:59:59'
                    ]);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Cek apakah ada notifikasi backlog
            $backlogNotification = session('backlog_notification');

            return view('admin.laporan.sr_wo', compact(
                'serviceRequests',
                'workOrders',
                'woBacklogs',
                'backlogNotification'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in srWo method: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
        try {
            $validated = $request->validate([
                'wo_id' => 'required|numeric|unique:work_orders,id',
                'description' => 'required',
                'status' => 'required|in:Open,Closed,Comp,APPR,WAPPR,WMATL',
                'priority' => 'required|in:emergency,normal,outage,urgent',
                'schedule_start' => 'required|date',
                'schedule_finish' => 'required|date|after_or_equal:schedule_start',
            ]);

            // Buat Work Order baru
            WorkOrder::create([
                'id' => $request->wo_id,
                'description' => $request->description,
                'status' => $request->status,  // Gunakan status dari request
                'priority' => $request->priority,
                'schedule_start' => $request->schedule_start,
                'schedule_finish' => $request->schedule_finish,
            ]);

            return redirect()
                ->route('admin.laporan.sr_wo')
                ->with('success', 'Work Order berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
        try {
            DB::beginTransaction();
            
            // 1. Validasi input dengan nilai enum yang benar
            $request->validate([
                'status' => 'required|in:Open,Closed,Comp,APPR,WAPPR,WMATL'  // Sesuaikan dengan enum di database
            ]);

            // 2. Ambil dan update WO dengan query builder
            $wo = DB::table('work_orders')
                ->where('id', $id)
                ->first();

            if (!$wo) {
                throw new \Exception('Work Order tidak ditemukan');
            }

            $oldStatus = $wo->status;

            if ($oldStatus === 'Closed') {  // Ubah pengecekan ke 'Close'
                throw new \Exception('WO yang sudah Closed tidak dapat diubah statusnya');
            }

            // 3. Update menggunakan query builder
            DB::table('work_orders')
                ->where('id', $id)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);

            // 4. Ambil data terbaru untuk memastikan
            $updatedWo = DB::table('work_orders')
                ->where('id', $id)
                ->first();

            DB::commit();

            \Log::info('WO status updated', [
                'wo_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $updatedWo->status
            ]);

            return response()->json([
                'success' => true,
                'message' => "Status berhasil diubah dari {$oldStatus} ke {$request->status}",
                'data' => [
                    'id' => $id,
                    'newStatus' => $updatedWo->status
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update WO status', [
                'wo_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 400);
        }
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
        try {
            $validated = $request->validate([
                'deskripsi' => 'required|string',
                'keterangan' => 'nullable|string',
                'status' => 'required|in:Open,Closed' // Tambahkan validasi status
            ]);

            $backlog = WoBacklog::findOrFail($id);
            $backlog->update([
                'deskripsi' => $validated['deskripsi'],
                'keterangan' => $validated['keterangan'],
                'status' => $validated['status']
            ]);

            return redirect()
                ->route('admin.laporan.sr_wo')
                ->with('success', 'WO Backlog berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function checkExpiredWO()
    {
        // Hanya jalankan jika bukan request update status
        if (request()->is('*/update-wo-status/*')) {
            return;
        }

        $expiredWOs = WorkOrder::where('schedule_finish', '<', now())
            ->where('status', 'Open')
            ->get();

        foreach ($expiredWOs as $wo) {
            if ($wo->moveToBacklog()) {
                session()->flash('backlog_notification', 
                    "WO #{$wo->id} telah dipindahkan ke backlog karena melewati jadwal.");
            }
        }
    }
}

