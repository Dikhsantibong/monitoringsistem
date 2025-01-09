<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\ScoreCardDailyUpdated;

class ScoreCardDaily extends Model
{
    use HasFactory;

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
        'ketentuan_rapat',
        'keterangan',
        'unit_source'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'peserta' => 'array',
        'ketentuan_rapat' => 'array'
    ];

    // Relasi dengan model Peserta
    public function pesertaList()
    {
        return $this->belongsToMany(Peserta::class, 'score_card_peserta')
            ->withPivot(['kehadiran_awal', 'kehadiran_akhir', 'skor', 'keterangan'])
            ->withTimestamps();
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($scoreCard) {
            // Konversi data peserta ke format yang sesuai
            if (is_array($scoreCard->peserta)) {
                $pesertaData = collect($scoreCard->peserta)->map(function ($data, $id) {
                    return [
                        'peserta_id' => $id,
                        'kehadiran_awal' => $data['awal'] ?? 0,
                        'kehadiran_akhir' => $data['akhir'] ?? 0,
                        'skor' => $data['skor'] ?? 0,
                        'keterangan' => $data['keterangan'] ?? ''
                    ];
                })->toArray();
                
                $scoreCard->pesertaToSync = $pesertaData;
            }
        });

        static::created(function ($scoreCard) {
            // Sync data peserta ke tabel pivot
            if (isset($scoreCard->pesertaToSync)) {
                $scoreCard->pesertaList()->sync($scoreCard->pesertaToSync);
            }
            event(new ScoreCardDailyUpdated($scoreCard, 'create'));
        });

        static::updating(function ($scoreCard) {
            if (is_array($scoreCard->peserta)) {
                $pesertaData = collect($scoreCard->peserta)->map(function ($data, $id) {
                    return [
                        'peserta_id' => $id,
                        'kehadiran_awal' => $data['awal'] ?? 0,
                        'kehadiran_akhir' => $data['akhir'] ?? 0,
                        'skor' => $data['skor'] ?? 0,
                        'keterangan' => $data['keterangan'] ?? ''
                    ];
                })->toArray();
                
                $scoreCard->pesertaToSync = $pesertaData;
            }
        });

        static::updated(function ($scoreCard) {
            if (isset($scoreCard->pesertaToSync)) {
                $scoreCard->pesertaList()->sync($scoreCard->pesertaToSync);
            }
            event(new ScoreCardDailyUpdated($scoreCard, 'update'));
        });

        static::deleted(function ($scoreCard) {
            $scoreCard->pesertaList()->detach();
            event(new ScoreCardDailyUpdated($scoreCard, 'delete'));
        });
    }

    public function getConnectionName()
    {
        return session('unit', 'mysql');
    }
} 