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
        'category_id',
        'location',
        'status',
        'uptime'
    ];

    public function issues()
    {
        return $this->hasMany(MachineIssue::class);
    }

    public function metrics()
    {
        return $this->hasMany(MachineMetric::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
