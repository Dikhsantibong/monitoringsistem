<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitOperationHour extends Model
{
    protected $table = 'unit_operation_hours';
    
    protected $fillable = [
        'power_plant_id',
        'tanggal',
        'hop_value',
        'keterangan',
        'unit_source'
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }
}
