<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OtherDiscussion extends Model
{
    use HasFactory;

    public static $isSyncing = false;

    protected $table = 'other_discussions';

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
        'unit_source'
    ];

    protected $casts = [
        'deadline' => 'date',
        'sr_number' => 'integer',
        'wo_number' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $dates = ['deadline'];

    // Konstanta untuk pilihan unit
    const UNITS = [
        'UP KENDARI',
        'ULPLTD POASIA',
        'ULPLTD KOLAKA',
        'ULPLTD WUA WUA',
        'ULPLTD BAU BAU'
    ];

    // Konstanta untuk tingkat resiko
    const RISK_LEVELS = [
        'R' => 'Rendah',
        'MR' => 'Menengah Rendah',
        'MT' => 'Menengah Tinggi',
        'T' => 'Tinggi'
    ];

    // Konstanta untuk tingkat prioritas
    const PRIORITY_LEVELS = [
        'Low',
        'Medium',
        'High'
    ];

    // Konstanta untuk status
    const STATUSES = [
        'Open',
        'Closed'
    ];

    public function getConnectionName()
    {
        return session('unit');
    }

    protected static function boot()
    {
        parent::boot();
        
        // Handle Created Event
        static::created(function ($discussion) {
            self::syncToUpKendari('create', $discussion);
        });

        // Handle Updated Event
        static::updated(function ($discussion) {
            self::syncToUpKendari('update', $discussion);
        });

        // Handle Deleted Event
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
                'unit_source' => session('unit'),
                'created_at' => $discussion->created_at,
                'updated_at' => $discussion->updated_at
            ];

            Log::info("Attempting to {$action} Other Discussion sync", ['data' => $data]);

            $upKendari = DB::connection('mysql')->table('other_discussions');

            switch($action) {
                case 'create':
                    $upKendari->insert($data);
                    break;
                    
                case 'update':
                    $upKendari->where('id', $discussion->id)
                             ->update($data);
                    break;
                    
                case 'delete':
                    $upKendari->where('id', $discussion->id)
                             ->delete();
                    break;
            }

            Log::info("Other Discussion {$action} sync successful", [
                'id' => $discussion->id,
                'unit' => 'poasia'
            ]);

        } catch (\Exception $e) {
            Log::error("Other Discussion {$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
        }
    }

    // Accessor untuk mendapatkan label tingkat resiko
    public function getRiskLevelLabelAttribute()
    {
        return self::RISK_LEVELS[$this->risk_level] ?? $this->risk_level;
    }

    // Scope untuk filter berdasarkan unit
    public function scopeByUnit($query, $unit)
    {
        return $query->where('unit', $unit);
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan tingkat resiko
    public function scopeByRiskLevel($query, $riskLevel)
    {
        return $query->where('risk_level', $riskLevel);
    }

    // Scope untuk filter berdasarkan tingkat prioritas
    public function scopeByPriorityLevel($query, $priorityLevel)
    {
        return $query->where('priority_level', $priorityLevel);
    }

    // Accessor untuk memastikan format tanggal selalu benar
    public function getDeadlineAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    // Optional: Jika ingin custom format saat menyimpan data
    public function setDeadlineAttribute($value)
    {
        $this->attributes['deadline'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }
} 