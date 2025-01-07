<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScoreCardDaily extends Model
{
    use HasFactory;

    public static $isSyncing = false;

    protected $table = 'score_card_daily';

    protected $fillable = [
        'tanggal',
        'lokasi',
        'peserta',
        'awal',
        'akhir',
        'skor',
        'waktu_mulai',
        'waktu_selesai',
        'kesiapan_panitia',
        'kesiapan_bahan',
        'kontribusi_pemikiran',
        'aktivitas_luar',
        'gangguan_diskusi',
        'gangguan_keluar_masuk',
        'gangguan_interupsi',
        'ketegasan_moderator',
        'kelengkapan_sr',
        'keterangan',
        'unit_source'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime'
    ];

    public function getConnectionName()
    {
        return session('unit', 'u478221055_up_kendari');
    }

    public function pesertaList()
    {
        return $this->belongsToMany(Peserta::class, 'score_card_peserta', 'score_card_daily_id', 'peserta_id')
            ->withPivot(['kehadiran_awal', 'kehadiran_akhir', 'skor', 'keterangan'])
            ->withTimestamps();
    }

    protected static function boot()
    {
        parent::boot();
        
        // Sebelum menyimpan, ambil data peserta dari tabel peserta
        static::creating(function ($scoreCard) {
            if (empty($scoreCard->peserta)) {
                $pesertaList = Peserta::pluck('jabatan')->implode(', ');
                $scoreCard->peserta = $pesertaList;
            }
        });

        // Handle Created Event
        static::created(function ($scoreCard) {
            self::syncToUpKendari('create', $scoreCard);
        });

        // Handle Updated Event
        static::updated(function ($scoreCard) {
            self::syncToUpKendari('update', $scoreCard);
        });

        // Handle Deleted Event
        static::deleted(function ($scoreCard) {
            self::syncToUpKendari('delete', $scoreCard);
        });
    }

    protected static function syncToUpKendari($action, $scoreCard)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $data = [
                'id' => $scoreCard->id,
                'tanggal' => $scoreCard->tanggal,
                'lokasi' => $scoreCard->lokasi,
                'peserta' => $scoreCard->peserta,
                'awal' => $scoreCard->awal,
                'akhir' => $scoreCard->akhir,
                'skor' => $scoreCard->skor,
                'waktu_mulai' => $scoreCard->waktu_mulai,
                'waktu_selesai' => $scoreCard->waktu_selesai,
                'kesiapan_panitia' => $scoreCard->kesiapan_panitia,
                'kesiapan_bahan' => $scoreCard->kesiapan_bahan,
                'kontribusi_pemikiran' => $scoreCard->kontribusi_pemikiran,
                'aktivitas_luar' => $scoreCard->aktivitas_luar,
                'gangguan_diskusi' => $scoreCard->gangguan_diskusi,
                'gangguan_keluar_masuk' => $scoreCard->gangguan_keluar_masuk,
                'gangguan_interupsi' => $scoreCard->gangguan_interupsi,
                'ketegasan_moderator' => $scoreCard->ketegasan_moderator,
                'kelengkapan_sr' => $scoreCard->kelengkapan_sr,
                'keterangan' => $scoreCard->keterangan,
                'unit_source' => session('unit'),
                'created_at' => $scoreCard->created_at,
                'updated_at' => $scoreCard->updated_at
            ];

            Log::info("Attempting to {$action} Score Card Daily sync", ['data' => $data]);

            $upKendari = DB::connection('mysql')->table('score_card_daily');

            switch($action) {
                case 'create':
                    $upKendari->insert($data);
                    break;
                    
                case 'update':
                    $upKendari->where('id', $scoreCard->id)
                             ->update($data);
                    break;
                    
                case 'delete':
                    $upKendari->where('id', $scoreCard->id)
                             ->delete();
                    break;
            }

            Log::info("Score Card Daily {$action} sync successful", [
                'id' => $scoreCard->id,
                'unit' => 'poasia'
            ]);

        } catch (\Exception $e) {
            Log::error("Score Card Daily {$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
        }
    }
} 