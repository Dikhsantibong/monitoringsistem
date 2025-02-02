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
            $data = [
                'sr_number' => $event->discussion->sr_number,
                'no_pembahasan' => $event->discussion->no_pembahasan,
                'unit' => $event->discussion->unit,
                'topic' => $event->discussion->topic,
                'target' => $event->discussion->target,
                'target_deadline' => $event->discussion->target_deadline,
                'deadline' => $event->discussion->deadline,
                'department_id' => $event->discussion->department_id,
                'section_id' => $event->discussion->section_id,
                'pic' => $event->discussion->pic,
                'risk_level' => $event->discussion->risk_level,
                'priority_level' => $event->discussion->priority_level,
                'status' => $event->discussion->status,
                'unit_source' => $event->sourceUnit,
                'closed_at' => $event->discussion->closed_at,
                'document_path' => $event->discussion->document_path,
                'document_description' => $event->discussion->document_description,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Hapus field yang bernilai null atau undefined
            $data = array_filter($data, function($value) {
                return !is_null($value);
            });

            // Gunakan koneksi UP Kendari
            DB::connection('mysql')->beginTransaction();

            try {
                switch($event->action) {
                    case 'create':
                        // Tambahkan id dari discussion asli
                        $data['id'] = $event->discussion->id;
                        
                        DB::connection('mysql')
                            ->table('other_discussions')
                            ->insert($data);
                        break;
                        
                    case 'update':
                        DB::connection('mysql')
                            ->table('other_discussions')
                            ->where('id', $event->discussion->id)
                            ->where('unit_source', $event->sourceUnit)
                            ->update($data);
                        break;
                        
                    case 'delete':
                        DB::connection('mysql')
                            ->table('other_discussions')
                            ->where('id', $event->discussion->id)
                            ->where('unit_source', $event->sourceUnit)
                            ->delete();
                        break;
                }

                DB::connection('mysql')->commit();

                Log::info("Other Discussion sync successful", [
                    'action' => $event->action,
                    'source_unit' => $event->sourceUnit,
                    'id' => $event->discussion->id
                ]);

            } catch (\Exception $e) {
                DB::connection('mysql')->rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error("Other Discussion sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'action' => $event->action,
                'source_unit' => $event->sourceUnit,
                'id' => $event->discussion->id ?? null
            ]);
        }
    }
} 