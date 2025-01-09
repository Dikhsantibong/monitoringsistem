<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\OtherDiscussionUpdated;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Models\PowerPlant;

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
                try {
                    event(new OtherDiscussionUpdated($discussion, 'create'));
                } catch (\Exception $e) {
                    Log::error("Failed to trigger OtherDiscussionUpdated event", [
                        'error' => $e->getMessage(),
                        'discussion_id' => $discussion->id
                    ]);
                }
                static::$isSyncing = false;
            }
        });

        static::updated(function ($discussion) {
            if (!static::$isSyncing) {
                static::$isSyncing = true;
                try {
                    event(new OtherDiscussionUpdated($discussion, 'update'));
                } catch (\Exception $e) {
                    Log::error("Failed to trigger OtherDiscussionUpdated event", [
                        'error' => $e->getMessage(),
                        'discussion_id' => $discussion->id
                    ]);
                }
                static::$isSyncing = false;
            }
        });

        static::deleted(function ($discussion) {
            if (!static::$isSyncing) {
                static::$isSyncing = true;
                try {
                    event(new OtherDiscussionUpdated($discussion, 'delete'));
                } catch (\Exception $e) {
                    Log::error("Failed to trigger OtherDiscussionUpdated event", [
                        'error' => $e->getMessage(),
                        'discussion_id' => $discussion->id
                    ]);
                }
                static::$isSyncing = false;
            }
        });
    }

    public static function getUnits()
    {
        return PowerPlant::select('name')
            ->distinct()
            ->pluck('name')
            ->toArray();
    }
} 