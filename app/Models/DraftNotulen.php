<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftNotulen extends Model
{
    protected $table = 'draft_notulen';

    protected $fillable = [
        'temp_notulen_id',
        'nomor_urut',
        'unit',
        'bidang',
        'sub_bidang',
        'bulan',
        'tahun',
        'agenda',
        'tempat',
        'peserta',
        'waktu_mulai',
        'waktu_selesai',
        'tanggal',
        'pembahasan',
        'tindak_lanjut',
        'pimpinan_rapat_nama',
        'notulis_nama',
        'tanggal_tanda_tangan'
    ];

    protected $casts = [
        'nomor_urut' => 'integer',
        'bulan' => 'integer',
        'tahun' => 'integer',
        'tanggal' => 'date',
        'tanggal_tanda_tangan' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The attributes that should be used for the model's unique identification.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Ensure temp_notulen_id is set
            if (empty($model->temp_notulen_id)) {
                throw new \Exception('temp_notulen_id is required');
            }
        });
    }
}
