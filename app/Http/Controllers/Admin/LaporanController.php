<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\WorkOrder;
use App\Models\SRWO;
use App\Models\WoBacklog;
use Illuminate\Support\Facades\DB;
use App\Models\PowerPlant;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;

class LaporanController extends Controller
{
    public function srWo(Request $request)
    {
        try {
            // Cek WO yang expired dan pindahkan ke backlog
            $this->checkExpiredWO();

            // Ambil data power plants
            $powerPlants = PowerPlant::all();

            // Query untuk Service Requests dengan eager loading powerPlant
            $serviceRequests = ServiceRequest::with('powerPlant')
                ->select('id', 'description', 'status', 'created_at', 'downtime', 'tipe_sr', 'priority', 'unit_source', 'power_plant_id')
                ->when($request->filled(['tanggal_mulai', 'tanggal_akhir']), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [
                        $request->tanggal_mulai . ' 00:00:00',
                        $request->tanggal_akhir . ' 23:59:59'
                    ]); 
                })
                ->when($request->filled('searchSR'), function ($query) use ($request) {
                    return $query->where(function($q) use ($request) {
                        $search = $request->searchSR;
                        $q->where('id', 'LIKE', "%{$search}%")
                          ->orWhere('description', 'LIKE', "%{$search}%")
                          ->orWhere('status', 'LIKE', "%{$search}%")
                          ->orWhere('tipe_sr', 'LIKE', "%{$search}%")
                          ->orWhere('priority', 'LIKE', "%{$search}%");
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Query untuk Work Orders dengan eager loading powerPlant
            $workOrders = WorkOrder::with('powerPlant')
                ->select('id', 'description', 'status', 'created_at', 'priority', 'type',
                        'schedule_start', 'schedule_finish', 'power_plant_id')
                ->where('is_active', true)
                ->when($request->filled(['tanggal_mulai', 'tanggal_akhir']), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [
                        $request->tanggal_mulai . ' 00:00:00',
                        $request->tanggal_akhir . ' 23:59:59'
                    ]);
                })
                ->when($request->filled('searchWO'), function ($query) use ($request) {
                    return $query->where(function($q) use ($request) {
                        $search = $request->searchWO;
                        $q->where('id', 'LIKE', "%{$search}%")
                          ->orWhere('description', 'LIKE', "%{$search}%")
                          ->orWhere('status', 'LIKE', "%{$search}%")
                          ->orWhere('priority', 'LIKE', "%{$search}%");
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Query untuk WO Backlog dengan eager loading powerPlant
            $woBacklogs = WoBacklog::with('powerPlant')
                ->select('id', 'no_wo', 'deskripsi', 'tanggal_backlog', 'keterangan', 'status', 'created_at', 'unit_source', 'power_plant_id')
                ->when($request->filled(['tanggal_mulai', 'tanggal_akhir']), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [
                        $request->tanggal_mulai . ' 00:00:00',
                        $request->tanggal_akhir . ' 23:59:59'
                    ]);
                })
                ->when($request->filled('searchBacklog'), function ($query) use ($request) {
                    return $query->where(function($q) use ($request) {
                        $search = $request->searchBacklog;
                        $q->where('no_wo', 'LIKE', "%{$search}%")
                          ->orWhere('deskripsi', 'LIKE', "%{$search}%")
                          ->orWhere('status', 'LIKE', "%{$search}%")
                          ->orWhere('keterangan', 'LIKE', "%{$search}%");
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Cek apakah ada notifikasi backlog
            $backlogNotification = session('backlog_notification');

            return view('admin.laporan.sr_wo', compact(
                'serviceRequests',
                'workOrders',
                'woBacklogs',
                'backlogNotification',
                'powerPlants'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in srWo method: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    

    // Tambah method untuk handle SR
    public function storeSR(Request $request)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validate([
                'sr_id' => 'required|numeric',
                'description' => 'required',
                'status' => 'required',
                'tanggal' => 'required|date',
                'downtime' => 'required',
                'tipe_sr' => 'required',
                'priority' => 'required',
                'unit' => 'required'
            ]);

            // Ambil power plant dan unit source
            $powerPlant = PowerPlant::findOrFail($request->unit);
            
            // Tentukan koneksi database berdasarkan session atau input
            $connection = session('unit') ?? 'mysql';
            
            // Buat Service Request
            $serviceRequest = new ServiceRequest();
            $serviceRequest->setConnection($connection);
            $serviceRequest->id = $validatedData['sr_id'];
            $serviceRequest->description = $validatedData['description'];
            $serviceRequest->status = $validatedData['status'];
            $serviceRequest->downtime = $validatedData['downtime'];
            $serviceRequest->tipe_sr = $validatedData['tipe_sr'];
            $serviceRequest->priority = $validatedData['priority'];
            $serviceRequest->power_plant_id = $powerPlant->id;
            $serviceRequest->save();

            DB::commit();
            return redirect()->route('admin.laporan.sr_wo')->with('success', 'Service Request berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in storeSR method: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    

    // Tambah method untuk handle WO
    public function storeWO(Request $request)
    {
        try {
            // 1. Validasi input
            $validatedData = $request->validate([
                'wo_id' => 'required|numeric|unique:work_orders,id',
                'description' => 'required',
                'type' => 'required|in:CM,PM,PDM,PAM,OH,EJ,EM',
                'status' => 'required|in:Open,Closed,Comp,APPR,WAPPR,WMATL',
                'priority' => 'required|in:emergency,normal,outage,urgent',
                'schedule_start' => 'required|date',
                'schedule_finish' => 'required|date|after_or_equal:schedule_start',
                'unit' => 'required|exists:power_plants,id'
            ]);

            // 2. Ambil power plant
            $powerPlant = PowerPlant::findOrFail($request->unit);
            
            // 3. Set koneksi database
            $connection = session('unit') ?? 'mysql';
            
            // 4. Buat Work Order
            try {
                DB::beginTransaction();

                // Cek duplikat ID di kedua database
                $exists = DB::connection($connection)->table('work_orders')
                    ->where('id', $validatedData['wo_id'])
                    ->exists();

                if ($exists) {
                    throw new \Exception('ID WO sudah digunakan di database ' . $connection);
                }

                // Cek di database target
                $targetConnection = $connection === 'mysql' 
                    ? PowerPlant::getConnectionByUnitSource($powerPlant->unit_source)
                    : 'mysql';

                $existsInTarget = DB::connection($targetConnection)->table('work_orders')
                    ->where('id', $validatedData['wo_id'])
                    ->exists();

                if ($existsInTarget) {
                    throw new \Exception('ID WO sudah digunakan di database ' . $targetConnection);
                }
                
                $workOrder = new WorkOrder();
                $workOrder->setConnection($connection);
                $workOrder->id = $validatedData['wo_id'];
                $workOrder->description = $validatedData['description'];
                $workOrder->type = $validatedData['type'];
                $workOrder->status = $validatedData['status'];
                $workOrder->priority = $validatedData['priority'];
                $workOrder->schedule_start = $validatedData['schedule_start'];
                $workOrder->schedule_finish = $validatedData['schedule_finish'];
                $workOrder->power_plant_id = $powerPlant->id;
                $workOrder->is_active = true;
                $workOrder->is_backlogged = false;
                
                // Simpan WO
                $workOrder->save();
                
                DB::commit();
                
                Log::info('Work Order created successfully', [
                    'id' => $workOrder->id,
                    'power_plant' => $powerPlant->name,
                    'connection' => $connection,
                    'target_connection' => $targetConnection
                ]);

                return redirect()
                    ->route('admin.laporan.sr_wo')
                    ->with('success', 'Work Order berhasil ditambahkan');

            } catch (QueryException $e) {
                DB::rollBack();
                
                // Tangani duplikat ID
                if ($e->getCode() == 23000) { // Duplicate entry
                    return back()
                        ->withInput()
                        ->with('error', 'ID WO sudah digunakan. Silakan gunakan ID lain.');
                }
                
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error in storeWO method', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function srWoClosed()
    {
        // Ambil SR yang closed
        $closedSR = ServiceRequest::where('status', 'Closed')
            ->select('id', 'description', 'status', 'created_at as tanggal')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor' => 'SR-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                    'tanggal' => $item->tanggal,
                    'deskripsi' => $item->description,
                    'status' => $item->status,
                    'tipe' => 'SR'
                ];
            });

        // Ambil WO yang closed
        $closedWO = WorkOrder::where('status', 'Closed')
            ->select('id', 'description', 'status', 'created_at as tanggal')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor' => 'WO-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                    'tanggal' => $item->tanggal,
                    'deskripsi' => $item->description,
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
            // 1. Validasi input
            $request->validate([
                'status' => 'required|in:Open,Closed,Comp,APPR,WAPPR,WMATL'
            ]);

            // 2. Cari WO menggunakan model untuk trigger events
            $wo = WorkOrder::findOrFail($id);
            
            if ($wo->status === 'Closed') {
                throw new \Exception('WO yang sudah Closed tidak dapat diubah statusnya');
            }

            $oldStatus = $wo->status;
            
            // 3. Update status
            $wo->status = $request->status;
            $wo->save();

            // 4. Log perubahan
            Log::info('WO status updated successfully', [
                'wo_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $wo->status,
                'updated_at' => $wo->updated_at
            ]);

            return response()->json([
                'success' => true,
                'message' => "Status berhasil diubah dari {$oldStatus} ke {$request->status}",
                'data' => [
                    'id' => $id,
                    'newStatus' => $wo->status,
                    'updated_at' => $wo->updated_at,
                    'power_plant_id' => $wo->power_plant_id
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update WO status', [
                'wo_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 400);
        }
    }

    public function createSR()
    {
        // Ambil data power plants
        $powerPlants = PowerPlant::all();
        
        return view('admin.laporan.create_sr', compact('powerPlants'));
    }

    public function createWOBacklog()
    {
        return view('admin.laporan.create_wo_backlog'); // Ganti dengan nama view yang sesuai
    }

    public function createWO()
    {
        // Ambil data power plants
        $powerPlants = PowerPlant::all();
        
        return view('admin.laporan.create_wo', compact('powerPlants'));
    }

    public function storeWOBacklog(Request $request)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validate([
                'no_wo' => 'required',
                'deskripsi' => 'required',
                'tanggal_backlog' => 'required|date',
                'keterangan' => 'required',
                'status' => 'required',
                'unit' => 'required' // validasi unit
            ]);

            // Ambil power plant berdasarkan ID yang dipilih
            $powerPlant = PowerPlant::findOrFail($request->unit);
            
            // Tentukan koneksi database berdasarkan session atau input
            $connection = session('unit') ?? 'mysql';
            
            // Buat WO Backlog
            $woBacklog = new WoBacklog();
            $woBacklog->setConnection($connection);
            $woBacklog->no_wo = $validatedData['no_wo'];
            $woBacklog->deskripsi = $validatedData['deskripsi'];
            $woBacklog->tanggal_backlog = $validatedData['tanggal_backlog'];
            $woBacklog->keterangan = $validatedData['keterangan'];
            $woBacklog->status = $validatedData['status'];
            $woBacklog->power_plant_id = $powerPlant->id;
            $woBacklog->save();

            DB::commit();
            return redirect()->route('admin.laporan.sr_wo')->with('success', 'WO Backlog berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in storeWoBacklog method: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
        // Skip jika request update status
        if (request()->is('*/update-wo-status/*')) {
            return;
        }

        DB::beginTransaction();
        try {
            // Ambil WO yang expired, belum di-backlog, dan masih Open
            $expiredWOs = WorkOrder::where('schedule_finish', '<', now())
                ->where('status', 'Open')
                ->where('is_backlogged', false)
                ->lockForUpdate()
                ->get();

            foreach ($expiredWOs as $wo) {
                // Cek apakah sudah ada di backlog
                $existingBacklog = WoBacklog::where('no_wo', $wo->id)->first();
                
                if (!$existingBacklog) {
                    // Generate ID baru untuk WO Backlog
                    $lastBacklog = WoBacklog::orderBy('id', 'desc')->first();
                    $newId = $lastBacklog ? $lastBacklog->id + 1 : 1;

                    // Buat WO Backlog baru menggunakan query builder untuk memastikan semua field terisi dengan benar
                    DB::table('wo_backlog')->insert([
                        'id' => $newId,
                        'no_wo' => $wo->id,
                        'deskripsi' => $wo->description,
                        'tanggal_backlog' => now()->format('Y-m-d'), // Format sesuai dengan tipe DATE
                        'keterangan' => 'Auto-generated from overdue WO',
                        'status' => 'Open',
                        'unit_source' => $wo->getConnection()->getName(),
                        'power_plant_id' => $wo->power_plant_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Update flag di work order dan ubah status
                    $wo->update([
                        'is_backlogged' => true,
                        'status' => 'WAPPR',
                        'is_active' => false
                    ]);

                    // Set notifikasi
                    session()->flash('backlog_notification', 
                        "WO #{$wo->id} telah ditambahkan ke backlog karena melewati jadwal.");

                    \Log::info('WO moved to backlog', [
                        'wo_id' => $wo->id,
                        'backlog_id' => $newId,
                        'connection' => $wo->getConnection()->getName()
                    ]);
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error creating WO Backlog: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function destroySR($id)
    {
        try {
            $sr = ServiceRequest::findOrFail($id);
            $sr->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Service Request berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Service Request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyWO($id)
    {
        try {
            $wo = WorkOrder::findOrFail($id);
            $wo->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Work Order berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Work Order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyBacklog($id)
    {
        try {
            $backlog = WoBacklog::findOrFail($id);
            $backlog->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'WO Backlog berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus WO Backlog: ' . $e->getMessage()
            ], 500);
        }
    }

    public function manage()
    {
        try {
            // Ambil data SR, WO, dan Backlog
            $serviceRequests = ServiceRequest::orderBy('created_at', 'desc')->get();
            $workOrders = WorkOrder::orderBy('created_at', 'desc')->get();
            $woBacklogs = WoBacklog::orderBy('created_at', 'desc')->get();
            $backlogs = WoBacklog::orderBy('created_at', 'desc')->get();

            return view('admin.laporan.manage', compact(
                'serviceRequests',
                'workOrders',
                'woBacklogs',
                'backlogs'
            ));
        } catch (\Exception $e) {
            \Log::error('Error in manage method: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }
}
