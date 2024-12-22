<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SRWO extends Model
{
    protected $table = 'sr_wo';
    
    protected $fillable = [
        'nomor',
        'tanggal',
        'unit',
        'deskripsi',
        'status'
    ];
    
    protected $dates = [
        'tanggal'
    ];
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'u478221055_up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
} 