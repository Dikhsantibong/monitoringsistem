<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MachineStatusLog extends Model
{
    use HasFactory;

    protected $table = 'machine_status_logs';

    protected $fillable = [
        'machine_id',
        'tanggal',
        'status',
        'keterangan',
        'load_value',
        'dmn',
        'dmp'
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];

    // Relasi ke model Machine
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    // Relasi ke PowerPlant melalui Machine
    public function powerPlant()
    {
        return $this->hasOneThrough(
            PowerPlant::class,
            Machine::class,
            'id', // Foreign key di tabel machines
            'id', // Foreign key di tabel power_plants
            'machine_id', // Local key di tabel machine_status_logs
            'power_plant_id' // Local key di tabel machines
        );
    }

    public function machineOperation()
    {
        return $this->hasOne(MachineOperation::class, 'machine_id', 'machine_id')
            ->whereDate('recorded_at', '=', DB::raw('DATE(machine_status_logs.tanggal)'));
    }
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
} 