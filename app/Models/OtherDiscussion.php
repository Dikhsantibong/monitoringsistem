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
    protected static $isUpdatingClosedAt = false;

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

    // Tambahkan konstanta untuk kode unit
    const UNIT_CODES = [
        'mysql' => [
            'UP KENDARI' => 'UPKD',
            'ULPLTD POASIA' => 'POAS',
            'ULPLTD WUA-WUA' => 'WUAS',
            'ULPLTD KOLAKA' => 'KOLA'
        ],
        'mysql_bau_bau' => [
            'ULPLTD BAU-BAU' => 'BAUS'
        ],
        'mysql_poasia' => [
            'ULPLTD POASIA' => 'POAS'
        ],
        'mysql_kola' => [
            'ULPLTD KOLAKA' => 'KOLA'
        ],
        'mysql_WUA' => [
            'ULPLTD WUA-WUA' => 'WUAS'
        ]
    ];

    protected $fillable = [
        'sr_number',
        'no_pembahasan',
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
                } finally {
                    static::$isSyncing = false;
                }
            }
        });

        static::updated(function ($discussion) {
            // Cek jika status berubah menjadi 'Closed'
            if ($discussion->isDirty('status') && 
                $discussion->status === 'Closed' && 
                !static::$isUpdatingClosedAt) {
                
                static::$isUpdatingClosedAt = true;
                try {
                    DB::transaction(function() use ($discussion) {
                        $discussion->closed_at = Carbon::now();
                        $discussion->saveQuietly(); // Menggunakan saveQuietly() untuk menghindari trigger events
                    });
                } finally {
                    static::$isUpdatingClosedAt = false;
                }
            }

            // Trigger event update hanya jika bukan update closed_at
            if (!static::$isSyncing && !static::$isUpdatingClosedAt) {
                static::$isSyncing = true;
                try {
                    event(new OtherDiscussionUpdated($discussion, 'update'));
                } finally {
                    static::$isSyncing = false;
                }
            }
        });

        static::deleted(function ($discussion) {
            if (!static::$isSyncing) {
                static::$isSyncing = true;
                try {
                    event(new OtherDiscussionUpdated($discussion, 'delete'));
                } finally {
                    static::$isSyncing = false;
                }
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

    /**
     * Save the model without triggering any events
     */
    public function saveQuietly(array $options = [])
    {
        return static::withoutEvents(function () use ($options) {
            return $this->save($options);
        });
    }

    // Method untuk generate nomor pembahasan
    public static function generateNoPembahasan($unit)
    {
        $year = date('Y');
        $month = date('m');
        
        // Ambil nomor urut terakhir untuk unit dan bulan ini
        $lastDiscussion = self::where('no_pembahasan', 'like', "$unit/$year/$month/%")
            ->orderBy('no_pembahasan', 'desc')
            ->first();
            
        if ($lastDiscussion) {
            $lastNumber = (int) substr($lastDiscussion->no_pembahasan, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        // Format: UNIT/TAHUN/BULAN/NOMOR URUT (4 digit)
        return sprintf("%s/%s/%s/%04d", $unit, $year, $month, $nextNumber);
    }
} 