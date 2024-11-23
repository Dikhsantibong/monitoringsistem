<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineIssue extends Model
{
    protected $fillable = [
        'machine_id',
        'category_id',
        'description',
        'status',
        'resolved_at',
        'resolution_notes'
    ];

    protected $casts = [
        'resolved_at' => 'datetime'
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function category()
    {
        return $this->belongsTo(MachineHealthCategory::class, 'category_id');
    }
} 