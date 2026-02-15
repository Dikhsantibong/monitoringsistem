<?php

namespace App\Listeners;

use App\Events\UnitStatusUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncUnitStatusToUpKendari
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\UnitStatusUpdated  $event
     * @return void
     */
    public function handle(UnitStatusUpdated $event)
    {
        try {
            $currentSession = session('unit', 'mysql');
            
            Log::info('Processing UnitStatus sync event', [
                'current_session' => $currentSession,
                'wonum' => $event->unitStatus->wonum,
                'action' => $event->action
            ]);

            // Jika operasi dilakukan di unit (bukan mysql), sinkronkan ke UP Kendari (mysql)
            if ($currentSession !== 'mysql') {
                $upKendariDB = DB::connection('mysql');
                
                $data = [
                    'wonum' => $event->unitStatus->wonum,
                    'status_unit' => $event->unitStatus->status_unit,
                    'updated_at' => now(),
                ];

                DB::beginTransaction();
                
                try {
                    switch($event->action) {
                        case 'create':
                        case 'update':
                            $data['created_at'] = $event->unitStatus->created_at ?? now();
                            
                            // Cek apakah data sudah ada di UP Kendari
                            $exists = $upKendariDB->table('unit_statuses')
                                ->where('wonum', $event->unitStatus->wonum)
                                ->exists();

                            if ($exists) {
                                $upKendariDB->table('unit_statuses')
                                    ->where('wonum', $event->unitStatus->wonum)
                                    ->update($data);
                            } else {
                                $upKendariDB->table('unit_statuses')->insert($data);
                            }
                            break;
                            
                        case 'delete':
                            $upKendariDB->table('unit_statuses')
                                ->where('wonum', $event->unitStatus->wonum)
                                ->delete();
                            break;
                    }
                    
                    DB::commit();
                    Log::info("UnitStatus sync to UP Kendari successful", [
                        'action' => $event->action,
                        'wonum' => $event->unitStatus->wonum
                    ]);
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

        } catch (\Exception $e) {
            Log::error("UnitStatus Sync failed", [
                'message' => $e->getMessage(),
                'wonum' => $event->unitStatus->wonum ?? null,
                'session' => session('unit', 'mysql')
            ]);
        }
    }
}
