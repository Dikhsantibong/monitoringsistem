<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\OtherDiscussionUpdated;

class OtherDiscussion extends Model
{
    use HasFactory;

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
        'unit_source'
    ];

    protected $dates = [
        'deadline',
        'closed_at',
        'created_at',
        'updated_at'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($discussion) {
            event(new OtherDiscussionUpdated($discussion, 'create'));
        });

        static::updated(function ($discussion) {
            event(new OtherDiscussionUpdated($discussion, 'update'));
        });

        static::deleted(function ($discussion) {
            event(new OtherDiscussionUpdated($discussion, 'delete'));
        });
    }

    public function getConnectionName()
    {
        return session('unit', 'mysql');
    }

    // ... method lain yang sudah ada ...
} 