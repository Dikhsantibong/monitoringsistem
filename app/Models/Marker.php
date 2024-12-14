<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marker extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak sesuai dengan konvensi
    protected $table = 'markers';

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'lat',
        'lng',
        'name',
        'capacity',
        'status',
        'date',
        'is_active',
        'DMN',
        'DMP',
        'HOP',
        'Beban',
    ];
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
}
