<?php

namespace App\Listeners;

use App\Events\MachineStatusUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncMachineStatusToUpKendari
{
    public function handle(MachineStatusUpdated $event)
    {
        try {
            // Koneksi ke database UP Kendari
            $upKendariDB = DB::connection('mysql');
            
            $data = [
                'machine_id' => $event->machineStatus->machine_id,
                'tanggal' => $event->machineStatus->tanggal,
                'status' => $event->machineStatus->status,
                'component' => $event->machineStatus->component,
                'equipment' => $event->machineStatus->equipment,
                'deskripsi' => $event->machineStatus->deskripsi,
                'kronologi' => $event->machineStatus->kronologi,
                'action_plan' => $event->machineStatus->action_plan,
                'progres' => $event->machineStatus->progres,
                'tanggal_mulai' => $event->machineStatus->tanggal_mulai,
                'target_selesai' => $event->machineStatus->target_selesai,
                'dmn' => $event->machineStatus->dmn,
                'dmp' => $event->machineStatus->dmp,
                'load_value' => $event->machineStatus->load_value,
                'unit_source' => $event->sourceUnit,
                'created_at' => now(),
                'updated_at' => now()
            ];

            switch($event->action) {
                case 'create':
                    $upKendariDB->table('machine_status_logs')->insert($data);
                    break;
                    
                case 'update':
                    $upKendariDB->table('machine_status_logs')
                        ->where('id', $event->machineStatus->id)
                        ->update($data);
                    break;
                    
                case 'delete':
                    $upKendariDB->table('machine_status_logs')
                        ->where('id', $event->machineStatus->id)
                        ->delete();
                    break;
            }

            Log::info("Sync to UP Kendari successful", [
                'action' => $event->action,
                'source_unit' => $event->sourceUnit,
                'machine_id' => $event->machineStatus->machine_id
            ]);

        } catch (\Exception $e) {
            Log::error("Sync to UP Kendari failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 