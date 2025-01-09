<?php

namespace App\Listeners;

use App\Events\OtherDiscussionUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncOtherDiscussionToUpKendari
{
    public function handle(OtherDiscussionUpdated $event)
    {
        try {
            // Koneksi ke database UP Kendari
            $upKendariDB = DB::connection('mysql');
            
            $data = [
                'sr_number' => $event->discussion->sr_number,
                'wo_number' => $event->discussion->wo_number,
                'unit' => $event->discussion->unit,
                'topic' => $event->discussion->topic,
                'target' => $event->discussion->target,
                'risk_level' => $event->discussion->risk_level,
                'priority_level' => $event->discussion->priority_level,
                'previous_commitment' => $event->discussion->previous_commitment,
                'next_commitment' => $event->discussion->next_commitment,
                'pic' => $event->discussion->pic,
                'status' => $event->discussion->status,
                'deadline' => $event->discussion->deadline,
                'closed_at' => $event->discussion->closed_at,
                'unit_source' => $event->sourceUnit,
                'created_at' => now(),
                'updated_at' => now()
            ];

            switch($event->action) {
                case 'create':
                    $upKendariDB->table('other_discussions')->insert($data);
                    break;
                    
                case 'update':
                    $upKendariDB->table('other_discussions')
                        ->where('id', $event->discussion->id)
                        ->update($data);
                    break;
                    
                case 'delete':
                    $upKendariDB->table('other_discussions')
                        ->where('id', $event->discussion->id)
                        ->delete();
                    break;
            }

            Log::info("Other Discussion sync to UP Kendari successful", [
                'action' => $event->action,
                'source_unit' => $event->sourceUnit,
                'discussion_id' => $event->discussion->id
            ]);

        } catch (\Exception $e) {
            Log::error("Other Discussion sync to UP Kendari failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 