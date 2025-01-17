<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\OtherDiscussionUpdated;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Models\PowerPlant;
use App\Models\Commitment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        'target_deadline',
        'deadline',
        'department_id',
        'section_id',
        'pic',
        'risk_level',
        'priority_level',
        'status',
        'previous_commitment',
        'next_commitment'
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

            // Cek jika status berubah menjadi 'Closed'
            if ($discussion->isDirty('status') && $discussion->status === 'Closed') {
                try {
                    // Update closed_at timestamp
                    $discussion->closed_at = Carbon::now();
                    $discussion->save();
                } catch (\Exception $e) {
                    Log::error('Error updating closed timestamp: ' . $e->getMessage());
                    throw $e;
                }
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

    // Tambahkan scope untuk memfilter diskusi
    public function scopeActive($query)
    {
        return $query->where('status', 'Open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'Closed');
    }

    public function scopeTargetOverdue($query)
    {
        return $query->where('status', 'Open')
                    ->where('target_deadline', '<', Carbon::now());
    }

    public function scopeCommitmentOverdue($query)
    {
        return $query->where('status', 'Open')
                    ->whereHas('commitments', function($q) {
                        $q->where('deadline', '<', Carbon::now());
                    });
    }
} 