<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\ScoreCardDailyUpdated;

class ScoreCardDaily extends Model
{
    use HasFactory;

    protected $table = 'score_card_daily';
    protected static $isSyncing = false;

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
        return session('unit', 'mysql');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($scoreCard) {
            if (!static::$isSyncing) {
                static::$isSyncing = true;
                event(new ScoreCardDailyUpdated($scoreCard, 'create'));
                static::$isSyncing = false;
            }
        });

        static::updated(function ($scoreCard) {
            if (!static::$isSyncing) {
                static::$isSyncing = true;
                event(new ScoreCardDailyUpdated($scoreCard, 'update'));
                static::$isSyncing = false;
            }
        });

        static::deleted(function ($scoreCard) {
            if (!static::$isSyncing) {
                static::$isSyncing = true;
                event(new ScoreCardDailyUpdated($scoreCard, 'delete'));
                static::$isSyncing = false;
            }
        });
    }
} 