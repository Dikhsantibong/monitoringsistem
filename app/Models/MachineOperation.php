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
    ];

    // Menambahkan relationship dengan Machine
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}

