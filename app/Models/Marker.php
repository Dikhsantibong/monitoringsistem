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
        'mesin',
        'capacity',
        'status',
        'DMN',
        'DMP',
        'Beban',
        'HOP'
    ];

    // Jika Anda menggunakan accessor untuk mendapatkan latitude dan longitude
    public function getLatitudeAttribute()
    {
        return $this->lat; // Pastikan ini sesuai dengan nama kolom di database
    }

    public function getLongitudeAttribute()
    {
        return $this->lng; // Pastikan ini sesuai dengan nama kolom di database
    }

    public function getConnectionName()
    {
        $connection = session('unit', 'mysql');
        \Log::info('Marker using connection:', ['connection' => $connection]);
        return $connection;
    }
}
