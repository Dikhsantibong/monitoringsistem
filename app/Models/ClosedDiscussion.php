<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClosedDiscussion extends Model
{
    use HasFactory;

    protected $table = 'closed_discussions';
    
    protected $fillable = [
        'discussion_id',
        'closed_at',
        'closed_by'
    ];

    protected $dates = [
        'closed_at'
    ];

    public function discussion()
    {
        return $this->belongsTo(OtherDiscussion::class, 'discussion_id');
    }
} 