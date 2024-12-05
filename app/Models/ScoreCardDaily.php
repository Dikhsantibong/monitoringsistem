<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'kelengkapan_sr'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime'
    ];
} 