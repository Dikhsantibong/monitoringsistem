<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WoBacklog extends Model
{
    use HasFactory;

    protected $table = 'wo_backlog';

    // Tambahkan kolom yang dapat diisi secara massal
    protected $fillable = [
        'no_wo',
        'deskripsi',
        'tanggal_backlog',
        'keterangan'
    ];
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'u478221055_up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
}
