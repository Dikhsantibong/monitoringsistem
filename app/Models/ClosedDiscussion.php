<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClosedDiscussion extends Model
{
    protected $table = 'closed_discussions';
    
    protected $fillable = [
        'sr_number',
        'wo_number',
        'unit',
        'topic',
        'target',
        'risk_level',
        'priority_level',
        'previous_commitment',
        'next_commitment',
        'pic',
        'status',
        'deadline',
        'closed_at',
        'original_id'
    ];

    protected $casts = [
        'deadline' => 'date:Y-m-d',
        'closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function setDeadlineAttribute($value)
    {
        $this->attributes['deadline'] = $value instanceof Carbon 
            ? $value->format('Y-m-d') 
            : Carbon::parse($value)->format('Y-m-d');
    }

    public function getDeadlineAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function originalDiscussion()
    {
        return $this->belongsTo(OtherDiscussion::class, 'original_id');
    }
} 