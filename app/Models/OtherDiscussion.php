<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\OtherDiscussionUpdated;

class OtherDiscussion extends Model
{
    use HasFactory;

    protected $table = 'other_discussions';
    protected static $isSyncing = false;

    // Definisi konstanta untuk risk levels
    const RISK_LEVELS = [
        'R' => 'Rendah',
        'MR' => 'Menengah Rendah',
        'MT' => 'Menengah Tinggi',
        'T' => 'Tinggi'
    ];

    // Definisi konstanta untuk priority levels
    const PRIORITY_LEVELS = [
        'Low',
        'Medium',
        'High'
    ];

    // Definisi konstanta untuk status
    const STATUSES = [
        'Open',
        'Closed'
    ];

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

    public function getConnectionName()
    {
        return session('unit', 'mysql');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($discussion) {
            if (!static::$isSyncing) {
                static::$isSyncing = true;
                event(new OtherDiscussionUpdated($discussion, 'create'));
                static::$isSyncing = false;
            }
        });

        static::updated(function ($discussion) {
            if (!static::$isSyncing) {
                static::$isSyncing = true;
                event(new OtherDiscussionUpdated($discussion, 'update'));
                static::$isSyncing = false;
            }
        });

        static::deleted(function ($discussion) {
            if (!static::$isSyncing) {
                static::$isSyncing = true;
                event(new OtherDiscussionUpdated($discussion, 'delete'));
                static::$isSyncing = false;
            }
        });
    }
} 