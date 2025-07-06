<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notulen extends Model
{
    protected $fillable = [
        'nomor_urut',
        'unit',
        'bidang',
        'sub_bidang',
        'bulan',
        'tahun',
        'pembahasan',
        'tindak_lanjut',
        'format_nomor',
        'pimpinan_rapat',
        'tempat',
        'agenda',
        'peserta',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'pimpinan_rapat_nama',
        'notulis_nama',
        'tanggal_tanda_tangan',
        'attendance_token',
        'attendance_token_expires_at',
        'documentation_images'
    ];

    protected $casts = [
        'tahun' => 'integer',
        'tanggal' => 'date',
        'tanggal_tanda_tangan' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'attendance_token_expires_at' => 'datetime',
        'documentation_images' => 'json'
    ];

    // Generate the formatted number
    public static function generateFormatNomor($nomor_urut, $unit, $bidang, $sub_bidang, $bulan, $tahun)
    {
        return sprintf(
            '%s/%s/%s/%s/%s/%s',
            str_pad($nomor_urut, 4, '0', STR_PAD_LEFT),
            $unit,
            $bidang,
            $sub_bidang,
            $bulan,
            $tahun
        );
    }
}
