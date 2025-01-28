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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


class LaporanController extends Controller
{
    public function srWo(Request $request)
    {
        try {
            // 1. Cache power plants data
            $powerPlants = Cache::remember('power_plants', 3600, function () {
                return PowerPlant::select('id', 'name')->get();
            });

            // 2. Optimasi query Service Requests
            $serviceRequests = ServiceRequest::with(['powerPlant:id,name'])
                ->select([
                    'id', 
                    'description', 
                    'status', 
                    'created_at', 
                    'downtime',
                    'tipe_sr', 
                    'priority', 
                    'power_plant_id',
                    'unit_source'
                ])
                ->orderBy('created_at', 'desc')
                ->paginate(25);

            // 3. Optimasi query Work Orders
            $workOrders = WorkOrder::with(['powerPlant:id,name'])
                ->select([
                    'id', 
                    'description', 
                    'status', 
                    'created_at', 
                    'priority', 
                    'type',
                    'schedule_start', 
                    'schedule_finish', 
                    'power_plant_id',
                    'unit_source',
                    'is_active',
                    'is_backlogged'
                ])
                ->where('is_active', true)
                ->latest()
                ->take(100)
                ->get();

            // 4. Optimasi query Backlogs
            $woBacklogs = WoBacklog::with(['powerPlant:id,name'])
                ->select([
                    'id', 
                    'no_wo', 
                    'deskripsi', 
                    'tanggal_backlog', 
                    'keterangan', 
                    'status', 
                    'created_at', 
                    'power_plant_id',
                    'unit_source'
                ])
                ->latest()
                ->take(100)
                ->get();

            // 5. Update table counts
            $srCount = $serviceRequests->total();
            $woCount = $workOrders->count();
            $backlogCount = $woBacklogs->count();

            // 6. Return view dengan semua data yang diperlukan
            return view('admin.laporan.sr_wo', compact(
                'serviceRequests',
                'workOrders',
                'woBacklogs',
                'powerPlants',
                'srCount',
                'woCount',
                'backlogCount'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in srWo method: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    

    // Tambah method untuk handle SR
    public function storeSR(Request $request)
    {
        try {
            \Log::info('Received SR data:', $request->all());
            
            $sr = ServiceRequest::create([
                'id' => $request->sr_id,
                'description' => $request->description,
                'status' => $request->status,
                'created_at' => $request->tanggal,
                'downtime' => $request->downtime,
                'tipe_sr' => $request->tipe_sr,
                'priority' => $request->priority,
                'power_plant_id' => $request->unit
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SR berhasil ditambahkan',
                'data' => $sr
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating SR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan SR: ' . $e->getMessage()
            ], 422);
        }
    }
    

    // Tambah method untuk handle WO
    public function storeWO(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'wo_id' => 'required|numeric',
                'description' => 'required',
                'type' => 'required',
                'status' => 'required|in:Open,Closed,Comp,APPR,WAPPR,WMATL',
                'priority' => 'required',
                'schedule_start' => 'required|date',
                'schedule_finish' => 'required|date',
                'unit' => 'required'
            ]);
    
            // Ambil power plant untuk mendapatkan unit_source
            $powerPlant = PowerPlant::findOrFail($request->unit);
            
            // Buat Work Order menggunakan model
            $workOrder = new WorkOrder();
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

            return redirect()
                ->route('admin.laporan.sr_wo')
                ->with('success', 'Work Order berhasil ditambahkan');

        } catch (\Exception $e) {
            Log::error('Error in storeWO method: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
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
        try {
            $sr = ServiceRequest::findOrFail($id);
            
            // Cek jika SR sudah closed
            if ($sr->status === 'Closed') {
                throw new \Exception('SR yang sudah Closed tidak dapat diubah statusnya');
            }

            $oldStatus = $sr->status;
            $sr->status = $request->status;
            $sr->save();

            return response()->json([
                'success' => true,
                'message' => "Status berhasil diubah dari {$oldStatus} ke {$request->status}",
                'data' => [
                    'id' => $id,
                    'newStatus' => $sr->status,
                    'formattedId' => 'SR-' . str_pad($id, 4, '0', STR_PAD_LEFT)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 400);
        }
    }

    public function updateWOStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:Open,Closed,Comp,APPR,WAPPR,WMATL'
            ]);

            // Gunakan model untuk update (bukan DB facade)
            $wo = WorkOrder::findOrFail($id);

            if ($wo->status === 'Closed') {
                throw new \Exception('WO yang sudah Closed tidak dapat diubah statusnya');
            }

            $oldStatus = $wo->status;
            $wo->status = $request->status;
            // Update akan mentrigger event updated di model
            $wo->save();

            return response()->json([
                'success' => true,
                'message' => "Status berhasil diubah dari {$oldStatus} ke {$request->status}",
                'data' => [
                    'id' => $id,
                    'newStatus' => $request->status,
                    'formattedId' => 'WO-' . str_pad($id, 4, '0', STR_PAD_LEFT)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in updateWOStatus: ' . $e->getMessage(), [
                'wo_id' => $id,
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

    public function verifyPasswordAndDelete(Request $request)
    {
        try {
            // Verifikasi password
            if (!Hash::check($request->password, Auth::user()->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password yang Anda masukkan salah'
                ], 401);
            }

            // Proses penghapusan berdasarkan tipe
            switch ($request->type) {
                case 'sr':
                    $item = ServiceRequest::findOrFail($request->id);
                    break;
                case 'wo':
                    $item = WorkOrder::findOrFail($request->id);
                    break;
                case 'backlog':
                    $item = WoBacklog::findOrFail($request->id);
                    break;
                default:
                    throw new \Exception('Tipe data tidak valid');
            }

            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}