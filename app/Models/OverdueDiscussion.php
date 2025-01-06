<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OverdueDiscussion extends Model
{
    public static $isSyncing = false;

    protected $table = 'overdue_discussions';
    
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
        'overdue_at',
        'original_id',
        'unit_source'
    ];

    protected $casts = [
        'deadline' => 'date:Y-m-d',
        'overdue_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'Open'
    ];

    public function getConnectionName()
    {
        return session('unit');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($discussion) {
            self::syncToUpKendari('create', $discussion);
        });

        static::updated(function ($discussion) {
            self::syncToUpKendari('update', $discussion);
        });

        static::deleted(function ($discussion) {
            self::syncToUpKendari('delete', $discussion);
        });
    }

    protected static function syncToUpKendari($action, $discussion)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $data = [
                'id' => $discussion->id,
                'sr_number' => $discussion->sr_number,
                'wo_number' => $discussion->wo_number,
                'unit' => $discussion->unit,
                'topic' => $discussion->topic,
                'target' => $discussion->target,
                'risk_level' => $discussion->risk_level,
                'priority_level' => $discussion->priority_level,
                'previous_commitment' => $discussion->previous_commitment,
                'next_commitment' => $discussion->next_commitment,
                'pic' => $discussion->pic,
                'status' => $discussion->status,
                'deadline' => $discussion->deadline,
                'overdue_at' => $discussion->overdue_at,
                'original_id' => $discussion->original_id,
                'unit_source' => session('unit'),
                'created_at' => $discussion->created_at,
                'updated_at' => $discussion->updated_at
            ];

            Log::info("Attempting to {$action} Overdue Discussion sync", ['data' => $data]);

            $upKendari = DB::connection('mysql')->table('overdue_discussions');

            switch($action) {
                case 'create':
                    $upKendari->insert($data);
                    break;
                case 'update':
                    $upKendari->where('id', $discussion->id)->update($data);
                    break;
                case 'delete':
                    $upKendari->where('id', $discussion->id)->delete();
                    break;
            }

            Log::info("Overdue Discussion {$action} sync successful", [
                'id' => $discussion->id,
                'unit' => 'poasia'
            ]);

        } catch (\Exception $e) {
            Log::error("Overdue Discussion {$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
        }
    }

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