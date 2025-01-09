<?php

namespace App\Listeners;

use App\Events\PesertaUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncPesertaToUpKendari
{
    public function handle(PesertaUpdated $event)
    {
        try {
            $upKendariDB = DB::connection('mysql');
            
            $data = [
                'jabatan' => $event->peserta->jabatan,
                'unit_source' => $event->sourceUnit,
                'created_at' => now(),
                'updated_at' => now()
            ];

            switch($event->action) {
                case 'create':
                    $upKendariDB->table('peserta')->insert($data);
                    break;
                    
                case 'update':
                    $upKendariDB->table('peserta')
                        ->where('id', $event->peserta->id)
                        ->update($data);
                    break;
                    
                case 'delete':
                    $upKendariDB->table('peserta')
                        ->where('id', $event->peserta->id)
                        ->delete();
                    break;
            }

            Log::info("Peserta sync to UP Kendari successful", [
                'action' => $event->action,
                'source_unit' => $event->sourceUnit,
                'peserta_id' => $event->peserta->id
            ]);

        } catch (\Exception $e) {
            Log::error("Peserta sync to UP Kendari failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 