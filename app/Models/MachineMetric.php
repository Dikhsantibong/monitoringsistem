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
} 