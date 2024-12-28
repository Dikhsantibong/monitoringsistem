<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineOperation extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak sesuai dengan konvensi
    protected $table = 'machine_operations';

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'machine_id',
        'dmn',
        'dmp',
        'load_value',
        'hop',
        'recorded_at',
        'status',
        'keterangan'
    ];

    // Nilai default untuk kolom tertentu
    protected $attributes = [
        'load_value' => null,
        'status' => null,
        'keterangan' => null,
        'dmn' => 0,
        'dmp' => 0
    ];

    // Menambahkan relationship dengan Machine
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'u478221055_up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
}

