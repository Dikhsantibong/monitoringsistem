<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\PesertaUpdated;

class Peserta extends Model
{
    protected $table = 'peserta';
    
    protected $fillable = [
        'jabatan'
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    // Jika Anda ingin menambahkan relasi dengan tabel score_card
    public function scoreCards()
    {
        return $this->belongsToMany(ScoreCardDaily::class, 'score_card_peserta')
            ->withPivot(['kehadiran_awal', 'kehadiran_akhir', 'skor', 'keterangan'])
            ->withTimestamps();
    }
    public function getConnectionName()
    {
        return session('unit', 'u478221055_up_kendari');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($peserta) {
            event(new PesertaUpdated($peserta, 'create'));
        });

        static::updated(function ($peserta) {
            event(new PesertaUpdated($peserta, 'update'));
        });

        static::deleted(function ($peserta) {
            event(new PesertaUpdated($peserta, 'delete'));
        });
    }
} 