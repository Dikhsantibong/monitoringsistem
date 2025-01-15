<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commitment extends Model
{
    protected $fillable = [
        'description',
        'deadline',
        'pic',
        'status',
        'other_discussion_id'
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