<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'other_discussion_id',
        'file_path',
        'original_name',
        'file_type'
    ];

    public function discussion()
    {
        return $this->belongsTo(OtherDiscussion::class, 'other_discussion_id');
    }
}
