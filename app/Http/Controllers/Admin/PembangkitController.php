<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PowerPlant;
use App\Models\Machine;
use App\Models\MachineOperation;
use App\Models\MachineStatusLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\UnitOperationHour;
use Illuminate\Support\Facades\File;
use App\Models\MachineImage;

class PembangkitController extends Controller
{
    public function ready()
    {
        $units = PowerPlant::orderByRaw("
            CASE 
                WHEN name LIKE 'PLTU%' THEN 1
                WHEN name LIKE 'PLTM%' THEN 2
                WHEN name LIKE 'PLTD%' THEN 3
                WHEN name LIKE 'PLTMG%' THEN 4
                ELSE 5
            END
        ")->get();
        $machines = Machine::with('issues', 'metrics')->get();
        $operations = MachineOperation::all();
        
        // Ambil status log hari ini
        $todayLogs = MachineStatusLog::whereDate('tanggal', Carbon::today())->get();
        $todayHops = UnitOperationHour::whereDate('tanggal', Carbon::today())->get();

        return view('admin.pembangkit.ready', compact('units', 'machines', 'operations', 'todayLogs', 'todayHops'));
    }

    public function saveStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Simpan data HOP
            foreach ($request->hops as $hopData) {
                UnitOperationHour::updateOrCreate(
                    [
                        'power_plant_id' => $hopData['power_plant_id'],
                        'tanggal' => $hopData['tanggal']
                    ],
                    [
                        'hop_value' => $hopData['hop_value'],
                        'unit_source' => session('unit')
                    ]
                );
            }

            // Simpan data status mesin
            foreach ($request->logs as $log) {
                // Pastikan data selalu tersimpan, bahkan jika status kosong
                MachineStatusLog::updateOrCreate(
                    [
                        'machine_id' => $log['machine_id'],
                        'tanggal' => $log['tanggal']
                    ],
                    [
                        'dmn' => $log['dmn'] ?? 0,
                        'dmp' => $log['dmp'] ?? 0,
                        'load_value' => $log['load_value'] ?? 0,
                        'status' => $log['status'] ?? '-',  // Berikan default value
                        'component' => $log['component'],
                        'equipment' => $log['equipment'] ?? null,
                        'deskripsi' => $log['deskripsi'] ?? null,
                        'kronologi' => $log['kronologi'] ?? null,
                        'action_plan' => $log['action_plan'] ?? null,
                        'progres' => $log['progres'] ?? null,
                        'tanggal_mulai' => $log['tanggal_mulai'] ?? null,
                        'target_selesai' => $log['target_selesai'] ?? null,
                        'unit_source' => session('unit')
                    ]
                );
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    public function getStatus(Request $request)
    {
        try {
            $tanggal = $request->tanggal ?? now()->toDateString();
            
            // Ambil semua log untuk tanggal tersebut
            $logs = MachineStatusLog::whereDate('tanggal', $tanggal)->get();
            
            $formattedLogs = $logs->map(function($log) {
                // Cari gambar langsung dari direktori
                $imagePath = null;
                $pattern = storage_path('app/public/machine-images/machine_' . $log->machine_id . '_*');
                $files = glob($pattern);
                if (!empty($files)) {
                    rsort($files); // Sort descending untuk mendapatkan file terbaru
                    $latestFile = $files[0];
                    $imagePath = 'machine-images/' . basename($latestFile);
                }
                
                // Bersihkan deskripsi dari tag gambar
                $cleanDescription = preg_replace('/\[image:.*?\]/', '', $log->deskripsi ?? '');
                
                return [
                    'machine_id' => $log->machine_id,
                    'tanggal' => $log->tanggal,
                    'status' => $log->status ?? '',
                    'dmn' => $log->dmn,
                    'dmp' => $log->dmp,
                    'load_value' => $log->load_value,
                    'component' => $log->component ?? '',
                    'equipment' => $log->equipment ?? '',
                    'deskripsi' => trim($cleanDescription),
                    'kronologi' => $log->kronologi,
                    'action_plan' => $log->action_plan,
                    'progres' => $log->progres,
                    'tanggal_mulai' => $log->tanggal_mulai ? $log->tanggal_mulai->format('Y-m-d') : null,
                    'target_selesai' => $log->target_selesai ? $log->target_selesai->format('Y-m-d') : null,
                    'image_url' => $imagePath
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'logs' => $formattedLogs,
                    'hops' => UnitOperationHour::whereDate('tanggal', $tanggal)->get()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error dalam getStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    public function getStatusHistory(Request $request)
    {
        try {
            $startDate = $request->start_date ?? Carbon::now()->subDays(30);
            $endDate = $request->end_date ?? Carbon::now();
            $machineId = $request->machine_id;

            // Query untuk mengambil history status
            $history = DB::table('machine_status_logs as msl')
                ->select([
                    'msl.tanggal',
                    'msl.status',
                    'msl.deskripsi',
                    'm.name as machine_name',
                    'pp.name as unit_name'
                ])
                ->join('machines as m', 'm.id', '=', 'msl.machine_id')
                ->join('power_plants as pp', 'pp.id', '=', 'm.power_plant_id')
                ->when($machineId, function($query) use ($machineId) {
                    return $query->where('msl.machine_id', $machineId);
                })
                ->whereBetween('msl.tanggal', [$startDate, $endDate])
                ->orderBy('msl.tanggal', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $history
            ]);
            
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil history: ' . $e->getMessage()
            ]);
        }
    }

    public function report(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        
        $logs = MachineStatusLog::with(['machine.powerPlant'])
            ->select([
                'machine_status_logs.*',
                'machines.name as machine_name',
                'power_plants.name as power_plant_name'
            ])
            ->join('machines', 'machines.id', '=', 'machine_status_logs.machine_id')
            ->join('power_plants', 'power_plants.id', '=', 'machines.power_plant_id')
            ->whereDate('tanggal', $date)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.pembangkit.report-table', compact('logs'))->render()
            ]);
        }

        return view('admin.pembangkit.report', compact('logs'));
    }

    public function downloadReport(Request $request)
    {
        $logs = MachineStatusLog::with(['machine.powerPlant'])
            ->select([
                'machine_status_logs.*',
                'machines.name as machine_name',
                'power_plants.name as power_plant_name'
            ])
            ->join('machines', 'machines.id', '=', 'machine_status_logs.machine_id')
            ->join('power_plants', 'power_plants.id', '=', 'machines.power_plant_id')
            ->whereDate('tanggal', $request->date ?? now())
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = PDF::loadView('admin.pembangkit.report-pdf', compact('logs'));
        
        return $pdf->download('laporan-kesiapan-pembangkit.pdf');
    }

    public function printReport(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        
        $logs = MachineStatusLog::with(['machine.powerPlant'])
            ->whereDate('tanggal', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pembangkit.report-print', compact('logs'));
    }

    public function resetStatus(Request $request)
    {
        try {
            $tanggal = $request->tanggal ?? now()->format('Y-m-d');
            
            // Ambil semua log status pada tanggal tersebut
            $currentLogs = MachineStatusLog::whereDate('tanggal', $tanggal)->get();
            
            // Ambil data mesin yang sedang gangguan dan masih dalam periode gangguan
            $activeIssues = MachineStatusLog::where('status', 'Gangguan')
                ->where(function($query) use ($tanggal) {
                    $query->whereNull('target_selesai')
                        ->orWhereDate('target_selesai', '>=', $tanggal);
                })
                ->whereDate('tanggal_mulai', '<=', $tanggal)
                ->get();
            
            // Kumpulkan machine_id yang sedang gangguan
            $machineIdsWithIssues = $activeIssues->pluck('machine_id')->toArray();
            
            // Cek apakah ada input baru dengan status lain untuk mesin yang gangguan
            $newInputsForIssues = MachineStatusLog::whereIn('machine_id', $machineIdsWithIssues)
                ->where('status', '!=', 'Gangguan')
                ->whereDate('tanggal', $tanggal)
                ->get()
                ->pluck('machine_id')
                ->toArray();
            
            // Machine IDs yang akan dipertahankan (tidak direset)
            $preservedMachineIds = array_diff($machineIdsWithIssues, $newInputsForIssues);
            
            DB::beginTransaction();
            
            foreach ($currentLogs as $log) {
                // Jika mesin tidak dalam daftar yang dipreservasi, reset datanya
                if (!in_array($log->machine_id, $preservedMachineIds)) {
                    // Simpan DMN dan DMP
                    $dmn = $log->dmn;
                    $dmp = $log->dmp;
                    
                    // Update log dengan nilai default kecuali DMN dan DMP
                    $log->update([
                        'status' => 'Operasi',
                        'component' => null,
                        'equipment' => '',
                        'deskripsi' => '',
                        'kronologi' => '',
                        'action_plan' => '',
                        'progres' => '',
                        'load_value' => '',
                        'tanggal_mulai' => null,
                        'target_selesai' => null,
                        'dmn' => $dmn,
                        'dmp' => $dmp
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil direset',
                'preserved_machines' => $preservedMachineIds
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Reset Status Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset data: ' . $e->getMessage()
            ]);
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            if (!$request->hasFile('image')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada file yang diunggah'
                ]);
            }

            $file = $request->file('image');
            $machineId = $request->input('machine_id');
            
            // Generate nama file unik dengan timestamp
            $filename = 'machine_' . $machineId . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Simpan file ke storage public
            $path = $file->storeAs('machine-images', $filename, 'public');

            // Hapus gambar lama dari storage dan database
            $oldImage = MachineImage::where('machine_id', $machineId)->first();
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage->image_path);
                $oldImage->delete();
            }

            // Simpan informasi gambar ke database
            MachineImage::create([
                'machine_id' => $machineId,
                'image_path' => $path
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil diunggah',
                'image_url' => $path
            ]);

        } catch (\Exception $e) {
            \Log::error('Error dalam uploadImage: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah gambar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteImage($machineId)
    {
        try {
            $image = MachineImage::where('machine_id', $machineId)->first();
            
            if ($image) {
                if (Storage::exists('public/' . $image->image_path)) {
                    Storage::delete('public/' . $image->image_path);
                }
                $image->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error dalam deleteImage: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus gambar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkImage($machineId)
    {
        try {
            $image = MachineImage::where('machine_id', $machineId)->first();
            
            if ($image) {
                return response()->json([
                    'success' => true,
                    'image_url' => $image->image_path
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No image found'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking image: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking image'
            ], 500);
        }
    }
}