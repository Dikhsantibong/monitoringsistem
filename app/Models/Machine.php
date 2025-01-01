<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'power_plant_id',
        'name',
        'status',
        'capacity',
        'type',
        'serial_number',
        'components'
    ];

    public function issues()
    {
        return $this->hasMany(MachineIssue::class);
    }

    public function metrics()
    {
        return $this->hasMany(MachineMetric::class);
    }

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class, 'power_plant_id');
    }

    public function operations()
    {
        return $this->hasMany(MachineOperation::class);
    }

    public function machineOperations()
    {
        return $this->hasMany(MachineOperation::class, 'machine_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(MachineStatusLog::class);
    }
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'u478221055_up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
}
