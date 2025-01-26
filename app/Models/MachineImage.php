<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineImage extends Model
{
    protected $fillable = ['machine_id', 'image_path'];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
} 