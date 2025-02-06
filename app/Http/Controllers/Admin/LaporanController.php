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
use Illuminate\Support\Facades\Storage;


class LaporanController extends Controller
{
    public function srWo(Request $request)
    {
        $this->checkExpiredWO();

        try {
            // Debug: Log session unit
            \Log::info('Current session unit:', ['unit' => session('unit')]);

            // 1. Get power plants data berdasarkan kondisi
            $powerPlants = PowerPlant::when(session('unit') !== 'mysql', function($query) {
                                return $query->where('unit_source', session('unit'));
                            })
                            ->select('id', 'name', 'unit_source')
                            ->orderBy('name')
                            ->get();

            // Debug: Log power plants data
            \Log::info('Power Plants data:', [
                'count' => $powerPlants->count(),
                'data' => $powerPlants->toArray()
            ]);

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

            // 3. Optimasi query Work Orders dengan kolom baru
            $workOrders = WorkOrder::with(['powerPlant:id,name'])
                ->select([
                    'id', 
                    'description',
                    'kendala',           // Tambahkan kolom baru
                    'tindak_lanjut',     // Tambahkan kolom baru
                    'document_path',      // Tambahkan kolom baru
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
                ->latest()
                ->take(100)
                ->get();

            // 4. Optimasi query Backlogs
            $woBacklogs = WoBacklog::with(['powerPlant:id,name'])
                ->select([
                    'id', 
                    'no_wo', 
                    'deskripsi',
                    'kendala',           // Tambahkan kolom baru
                    'tindak_lanjut',     // Tambahkan kolom baru 
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

            // Debug log untuk memeriksa data
            Log::info('Work Orders Data:', [
                'count' => $woCount,
                'sample' => $workOrders->first()
            ]);

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
            DB::beginTransaction();

            // 1. Dapatkan power plant dan unit source
            $powerPlant = PowerPlant::findOrFail($request->unit);
            $unitSource = $powerPlant->unit_source ?? 'mysql';

            // 2. Fungsi untuk mendapatkan ID yang tersedia
            $getAvailableId = function($baseId) use ($unitSource) {
                $id = $baseId;
                $maxAttempts = 100; // Batasi jumlah percobaan
                $attempt = 0;

                while ($attempt < $maxAttempts) {
                    // Cek di semua database
                    $exists = false;
                    $connections = ['mysql', 'mysql_wua_wua', 'mysql_poasia', 'mysql_kolaka', 'mysql_bau_bau'];
                    
                    foreach ($connections as $connection) {
                        try {
                            $check = DB::connection($connection)
                                ->table('work_orders')
                                ->where('id', $id)
                                ->exists();
                            
                            if ($check) {
                                $exists = true;
                                break;
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }

                    if (!$exists) {
                        return $id;
                    }

                    $id++; // Coba ID berikutnya
                    $attempt++;
                }

                throw new \Exception("Tidak dapat menemukan ID yang tersedia setelah $maxAttempts percobaan");
            };

            // 3. Dapatkan ID yang tersedia
            $woId = $getAvailableId($request->wo_id);

            Log::info('Using WO ID:', [
                'original_id' => $request->wo_id,
                'final_id' => $woId,
                'unit_source' => $unitSource
            ]);

            // 4. Insert data dengan ID yang sudah diverifikasi
            $insertData = [
                'id' => $woId,
                'description' => $request->description,
                'type' => $request->type,
                'status' => 'Open',
                'priority' => $request->priority,
                'schedule_start' => $request->schedule_start,
                'schedule_finish' => $request->schedule_finish,
                'power_plant_id' => $request->unit,
                'unit_source' => $unitSource,
                'is_active' => true,
                'is_backlogged' => false,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // 5. Insert ke database unit
            DB::connection($unitSource)
                ->table('work_orders')
                ->insert($insertData);

            // 6. Sinkronisasi ke database utama jika berbeda
            if ($unitSource !== 'mysql') {
                try {
                    DB::connection('mysql')
                        ->table('work_orders')
                        ->insert($insertData);
                } catch (\Exception $e) {
                    Log::warning('Sync to main DB failed:', [
                        'error' => $e->getMessage(),
                        'wo_id' => $woId
                    ]);
                }
            }

            DB::commit();

            // 7. Return response dengan ID yang digunakan
            return response()->json([
                'success' => true,
                'message' => 'Work Order berhasil ditambahkan',
                'data' => [
                    'wo_id' => $woId,
                    'original_id' => $request->wo_id
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create WO:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan Work Order: ' . $e->getMessage()
            ], 500);
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

            // Cari WO di database saat ini
            $wo = WorkOrder::findOrFail($id);

            if ($wo->status === 'Closed') {
                throw new \Exception('WO yang sudah Closed tidak dapat diubah statusnya');
            }

            $oldStatus = $wo->status;
            $wo->status = $request->status;
            
            // Simpan perubahan di database saat ini
            $wo->save();

            // Sinkronisasi ke semua database unit
            $powerPlant = PowerPlant::find($wo->power_plant_id);
            if ($powerPlant) {
                $allConnections = [
                    'mysql',
                    'mysql_wua_wua',
                    'mysql_poasia',
                    'mysql_kolaka',
                    'mysql_bau_bau'
                ];

                // Filter koneksi saat ini
                $currentConnection = $powerPlant->unit_source ?? 'mysql';
                $targetConnections = array_filter($allConnections, function($conn) use ($currentConnection) {
                    return $conn !== $currentConnection;
                });

                foreach ($targetConnections as $connection) {
                    try {
                        DB::connection($connection)
                            ->table('work_orders')
                            ->where('id', $wo->id)
                            ->update([
                                'status' => $request->status,
                                'updated_at' => now()
                            ]);

                        Log::info("WO Status synced to {$connection}", [
                            'wo_id' => $wo->id,
                            'new_status' => $request->status
                        ]);
                    } catch (\Exception $e) {
                        Log::error("Failed to sync WO status to {$connection}", [
                            'wo_id' => $wo->id,
                            'error' => $e->getMessage()
                        ]);
                        continue; // Lanjut ke koneksi berikutnya jika gagal
                    }
                }
            }

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
            Log::info('Skipping expired WO check for status update request');
            return;
        }

        Log::info('Starting expired WO check');
        
        DB::beginTransaction();
        try {
            // Debug: Tampilkan waktu sekarang
            Log::debug('Current time: ' . now());

            // Cari WO yang expired, belum di-backlog, dan statusnya bukan Closed
            $expiredWOs = WorkOrder::whereNotIn('status', ['Closed'])  // Ubah kondisi ini
                ->where('is_backlogged', false)
                ->where('schedule_finish', '<', now())
                ->get();

            Log::info('Found expired WOs:', [
                'count' => $expiredWOs->count(),
                'statuses' => $expiredWOs->pluck('status')->toArray()
            ]);

            foreach ($expiredWOs as $wo) {
                Log::info('Processing expired WO:', [
                    'wo_id' => $wo->id,
                    'status' => $wo->status,
                    'schedule_finish' => $wo->schedule_finish,
                    'current_time' => now()
                ]);

                try {
                    // Buat WO Backlog dengan data lengkap
                    $backlog = WoBacklog::create([
                        'no_wo' => $wo->id,
                        'deskripsi' => $wo->description,
                        'type_wo' => $wo->type,
                        'priority' => $wo->priority,
                        'schedule_start' => $wo->schedule_start,
                        'schedule_finish' => $wo->schedule_finish,
                        'tanggal_backlog' => now(),
                        'keterangan' => "Otomatis masuk backlog karena melewati jadwal (Status: {$wo->status})",
                        'status' => 'Open',
                        'power_plant_id' => $wo->power_plant_id,
                        'unit_source' => $wo->unit_source
                    ]);

                    // Update status WO dan tandai sebagai backlogged
                    $wo->update([
                        'is_backlogged' => true,
                        'backlogged_at' => now()
                    ]);

                    Log::info('Successfully moved WO to backlog', [
                        'wo_id' => $wo->id,
                        'original_status' => $wo->status,
                        'backlog_id' => $backlog->id,
                        'type_wo' => $backlog->type_wo,
                        'priority' => $backlog->priority,
                        'schedule_start' => $backlog->schedule_start,
                        'schedule_finish' => $backlog->schedule_finish
                    ]);

                    // Hapus WO original
                    $wo->delete();

                } catch (\Exception $e) {
                    Log::error('Error processing individual WO:', [
                        'wo_id' => $wo->id,
                        'status' => $wo->status,
                        'error' => $e->getMessage()
                    ]);
                    continue; // Lanjut ke WO berikutnya jika ada error
                }
            }

            DB::commit();
            Log::info('Completed expired WO check');

            if ($expiredWOs->count() > 0) {
                $message = 'Ada ' . $expiredWOs->count() . ' WO yang telah dipindahkan ke backlog karena expired.';
                session()->flash('backlog_notification', $message);
                Log::info($message);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in checkExpiredWO:', [
                'error' => $e->getMessage(),
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

    public function moveToBacklog($id)
    {
        try {
            $workOrder = WorkOrder::findOrFail($id);
            
            Log::info('Moving WO to backlog manually', [
                'wo_id' => $workOrder->id,
                'type' => $workOrder->type,
                'priority' => $workOrder->priority,
                'schedule_start' => $workOrder->schedule_start,
                'schedule_finish' => $workOrder->schedule_finish
            ]);
            
            $workOrder->checkAndMoveToBacklog();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error in manual moveToBacklog', [
                'wo_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function __construct()
    {
        // Set channel logging khusus untuk WO
        Log::channel('wo_operations')->info('LaporanController initialized');
    }

    public function editWO($id)
    {
        $workOrder = WorkOrder::findOrFail($id);
        $powerPlants = PowerPlant::all();
        
        return view('admin.laporan.edit_wo', compact('workOrder', 'powerPlants'));
    }

    public function updateWO(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $workOrder = WorkOrder::findOrFail($id);
            
            // Validasi input
            $request->validate([
                'description' => 'required',
                'kendala' => 'nullable',
                'tindak_lanjut' => 'nullable',
                'type' => 'required|in:CM,PM,PDM,PAM,OH,EJ,EM',
                'priority' => 'required|in:emergency,normal,outage,urgent',
                'schedule_start' => 'required|date',
                'schedule_finish' => 'required|date|after_or_equal:schedule_start',
                'unit' => 'required|exists:power_plants,id',
                'document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120'
            ]);

            // Data yang akan diupdate
            $data = [
                'description' => $request->description,
                'kendala' => $request->kendala,
                'tindak_lanjut' => $request->tindak_lanjut,
                'type' => $request->type,
                'priority' => $request->priority,
                'schedule_start' => $request->schedule_start,
                'schedule_finish' => $request->schedule_finish,
                'power_plant_id' => $request->unit
            ];

            // Handle dokumen jika ada
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                
                // Hapus dokumen lama jika ada
                if ($workOrder->document_path && Storage::exists('public/' . $workOrder->document_path)) {
                    Storage::delete('public/' . $workOrder->document_path);
                }

                // Generate nama file yang aman
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                
                // Simpan file
                $path = $file->storeAs('work-orders', $fileName, 'public');
                
                // Log untuk debugging
                \Log::info('Document Upload:', [
                    'original_name' => $file->getClientOriginalName(),
                    'stored_path' => $path,
                    'full_url' => asset('storage/' . $path),
                    'exists' => Storage::exists('public/' . $path)
                ]);

                $data['document_path'] = $path;
            }

            // Update data WO
            $workOrder->update($data);

            // Sinkronisasi ke database utama jika berbeda
            if ($workOrder->unit_source !== 'mysql') {
                try {
                    DB::connection('mysql')
                        ->table('work_orders')
                        ->where('id', $workOrder->id)
                        ->update($data);
                } catch (\Exception $e) {
                    Log::warning('Sync to main DB failed:', [
                        'error' => $e->getMessage(),
                        'wo_id' => $workOrder->id
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Work Order berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating WO:', [
                'wo_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate Work Order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadDocument($id)
    {
        try {
            $workOrder = WorkOrder::findOrFail($id);
            
            if (!$workOrder->document_path) {
                return back()->with('error', 'Dokumen tidak ditemukan');
            }

            $path = storage_path('app/public/' . $workOrder->document_path);
            
            if (!file_exists($path)) {
                \Log::error('Document file not found:', ['path' => $path]);
                return back()->with('error', 'File tidak ditemukan di server');
            }

            // Dapatkan ekstensi file dari path
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            
            // Tentukan mime type berdasarkan ekstensi
            $mime = $this->getMimeType($extension);

            // Gunakan nama file asli dari document_description atau nama file di path
            $fileName = $workOrder->document_description ?? basename($path);

            \Log::info('Downloading document:', [
                'path' => $path,
                'mime' => $mime,
                'extension' => $extension,
                'filename' => $fileName
            ]);

            return response()->file($path, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error downloading document:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal mengunduh dokumen');
        }
    }

    private function getMimeType($extension)
    {
        $mimes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png'
        ];

        return $mimes[strtolower($extension)] ?? 'application/octet-stream';
    }
}