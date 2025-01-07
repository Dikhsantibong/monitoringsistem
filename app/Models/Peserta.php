<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $table = 'peserta';
    
    protected $fillable = [
        'jabatan'
    ];

    // Jika Anda ingin menambahkan relasi dengan tabel score_card
    public function scoreCards()
    {
        return $this->belongsToMany(ScoreCardDaily::class, 'score_card_peserta')
            ->withPivot(['kehadiran_awal', 'kehadiran_akhir', 'skor', 'keterangan'])
            ->withTimestamps();
    }
} 