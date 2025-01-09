<?php

namespace App\Listeners;

use App\Events\ScoreCardDailyUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncScoreCardDailyToUpKendari
{
    public function handle(ScoreCardDailyUpdated $event)
    {
        try {
            $upKendariDB = DB::connection('mysql');
            
            $data = [
                'tanggal' => $event->scoreCard->tanggal,
                'lokasi' => $event->scoreCard->lokasi,
                'peserta' => $event->scoreCard->peserta,
                'awal' => $event->scoreCard->awal,
                'akhir' => $event->scoreCard->akhir,
                'skor' => $event->scoreCard->skor,
                'waktu_mulai' => $event->scoreCard->waktu_mulai,
                'waktu_selesai' => $event->scoreCard->waktu_selesai,
                'kesiapan_panitia' => $event->scoreCard->kesiapan_panitia,
                'kesiapan_bahan' => $event->scoreCard->kesiapan_bahan,
                'kontribusi_pemikiran' => $event->scoreCard->kontribusi_pemikiran,
                'aktivitas_luar' => $event->scoreCard->aktivitas_luar,
                'gangguan_diskusi' => $event->scoreCard->gangguan_diskusi,
                'gangguan_keluar_masuk' => $event->scoreCard->gangguan_keluar_masuk,
                'gangguan_interupsi' => $event->scoreCard->gangguan_interupsi,
                'ketegasan_moderator' => $event->scoreCard->ketegasan_moderator,
                'kelengkapan_sr' => $event->scoreCard->kelengkapan_sr,
                'keterangan' => $event->scoreCard->keterangan,
                'unit_source' => $event->sourceUnit,
                'created_at' => now(),
                'updated_at' => now()
            ];

            switch($event->action) {
                case 'create':
                    $upKendariDB->table('score_card_daily')->insert($data);
                    break;
                    
                case 'update':
                    $upKendariDB->table('score_card_daily')
                        ->where('id', $event->scoreCard->id)
                        ->update($data);
                    break;
                    
                case 'delete':
                    $upKendariDB->table('score_card_daily')
                        ->where('id', $event->scoreCard->id)
                        ->delete();
                    break;
            }

            Log::info("Score Card Daily sync successful", [
                'action' => $event->action,
                'source_unit' => $event->sourceUnit,
                'id' => $event->scoreCard->id
            ]);

        } catch (\Exception $e) {
            Log::error("Score Card Daily sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 