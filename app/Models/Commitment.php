<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commitment extends Model
{
    protected $fillable = [
        'description',
        'deadline',
        'department_id',
        'section_id',
        'pic',
        'status',
        'other_discussion_id'
    ];

    protected $attributes = [
        'status' => 'open'
    ];

    protected $dates = [
        'deadline'
    ];

    public function discussion()
    {
        return $this->belongsTo(OtherDiscussion::class, 'other_discussion_id');
    }

    public function pic()
    {
        return $this->belongsTo(Pic::class);
    }
} 