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
            $currentSession = session('unit', 'mysql');
            $powerPlant = $event->machineStatus->getPowerPlant();

            Log::info('Processing sync event', [
                'current_session' => $currentSession,
                'machine_id' => $event->machineStatus->machine_id,
                'uuid' => $event->machineStatus->uuid,
                'action' => $event->action,
                'power_plant_unit' => $powerPlant ? $powerPlant->unit_source : null
            ]);

            // Jika dari UP Kendari, sync ke unit lokal
            if ($currentSession === 'mysql' && $powerPlant && $powerPlant->unit_source !== 'mysql') {
                $targetDB = DB::connection($powerPlant->unit_source);
                
                Log::info('Syncing from UP Kendari to local unit', [
                    'target_unit' => $powerPlant->unit_source,
                    'uuid' => $event->machineStatus->uuid
                ]);

                $data = [
                    'uuid' => $event->machineStatus->uuid,
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
                    'unit_source' => $powerPlant->unit_source,
                    'updated_at' => now()
                ];

                DB::beginTransaction();
                
                try {
                    switch($event->action) {
                        case 'create':
                            $data['created_at'] = now();
                            $targetDB->table('machine_status_logs')->insert($data);
                            break;
                            
                        case 'update':
                            $targetDB->table('machine_status_logs')
                                ->where('uuid', $event->machineStatus->uuid)
                                ->update($data);
                            break;
                            
                        case 'delete':
                            $targetDB->table('machine_status_logs')
                                ->where('uuid', $event->machineStatus->uuid)
                                ->delete();
                            break;
                    }
                    
                    DB::commit();
                    
                    Log::info("Sync to local unit successful", [
                        'action' => $event->action,
                        'uuid' => $event->machineStatus->uuid,
                        'target_unit' => $powerPlant->unit_source
                    ]);
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
                return;
            }

            // Jika dari unit lokal, sync ke UP Kendari (kode yang sudah ada)
            if ($currentSession !== 'mysql') {
                // Skip jika sudah di UP Kendari
                if ($currentSession === 'mysql') {
                    Log::info('Skipping sync - already in UP Kendari');
                    return;
                }

                // Validasi power plant
                if (!$powerPlant || !$powerPlant->unit_source) {
                    throw new \Exception('Invalid power plant data');
                }

                // Pastikan ini adalah unit yang benar untuk disinkronkan
                if ($powerPlant->unit_source !== $currentSession) {
                    Log::info('Skipping sync - not the correct unit');
                    return;
                }

                // Verifikasi data di database lokal
                $localDB = DB::connection($currentSession);
                $localRecord = $localDB->table('machine_status_logs')
                    ->where('uuid', $event->machineStatus->uuid)
                    ->first();

                if (!$localRecord && $event->action !== 'delete') {
                    throw new \Exception('Data not found in local database');
                }

                // Lanjutkan dengan sinkronisasi ke UP Kendari
                $upKendariDB = DB::connection('mysql');
                
                $data = [
                    'uuid' => $event->machineStatus->uuid,
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
                    'updated_at' => now()
                ];

                DB::beginTransaction();
                
                try {
                    switch($event->action) {
                        case 'create':
                            $data['created_at'] = now();
                            $upKendariDB->table('machine_status_logs')->insert($data);
                            break;
                            
                        case 'update':
                            $upKendariDB->table('machine_status_logs')
                                ->where('uuid', $event->machineStatus->uuid)
                                ->update($data);
                            break;
                            
                        case 'delete':
                            $upKendariDB->table('machine_status_logs')
                                ->where('uuid', $event->machineStatus->uuid)
                                ->delete();
                            break;
                    }
                    
                    DB::commit();
                    
                    Log::info("Sync to UP Kendari successful", [
                        'action' => $event->action,
                        'uuid' => $event->machineStatus->uuid,
                        'machine_id' => $event->machineStatus->machine_id
                    ]);
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

        } catch (\Exception $e) {
            Log::error("Sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'uuid' => $event->machineStatus->uuid ?? null,
                'machine_id' => $event->machineStatus->machine_id,
                'session' => $currentSession
            ]);
        }
    }
} 