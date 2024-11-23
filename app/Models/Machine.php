<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'status'
    ];

    public function issues()
    {
        return $this->hasMany(MachineIssue::class);
    }

    public function metrics()
    {
        return $this->hasMany(MachineMetric::class);
    }
}
