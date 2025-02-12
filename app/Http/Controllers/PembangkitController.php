<?php

namespace App\Http\Controllers;

use App\Models\MachineStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PembangkitController extends Controller
{
    public function saveStatus(Request $request)
    {
        try {
            $logs = $request->input('logs');
            
            foreach ($logs as $log) {
                MachineStatusLog::create([
                    'uuid' => (string) Str::uuid(),
                    'machine_id' => $log['machine_id'],
                    'tanggal' => $log['tanggal'],
                    'status' => $log['status'],
                    'dmn' => $log['dmn'] ?? 0,
                    'dmp' => $log['dmp'] ?? 0,
                    'deskripsi' => $log['deskripsi'],
                    'action_plan' => $log['action_plan'],
                    'load_value' => $log['load_value'],
                    'progres' => $log['progres'],
                    'kronologi' => $log['kronologi'],
                    'target_selesai' => $log['target_selesai'],
                    'unit_source' => session('database', 'mysql')
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
} 