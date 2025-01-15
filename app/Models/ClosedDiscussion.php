<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\OtherDiscussionUpdated;

class ClosedDiscussion extends Model
{
    use HasFactory;

    protected $table = 'closed_discussions';
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
        'target_deadline',
        'risk_level',
        'priority_level',
        'pic',
        'status',
        'closed_at',
        'original_id',
        'unit_source',
        'commitments',
        'created_at',
        'updated_at'
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

    public function commitments()
    {
        return $this->hasMany(Commitment::class, 'other_discussion_id');
    }

    // Tambahkan method untuk cek deadline
    public function isOverdue()
    {
        if ($this->target_deadline) {
            return Carbon::parse($this->target_deadline)->isPast();
        }
        return false;
    }

    public function hasOverdueCommitments()
    {
        return $this->commitments()
            ->where('deadline', '<', Carbon::now())
            ->exists();
    }
} 