<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineMetric extends Model
{
    protected $fillable = ['machine_id', 'achievement_percentage'];

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