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
        'unit_source',
        'closed_at',
        'document_path',
        'document_description'
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

    // protected static function boot()
    // {
    //     parent::boot();
        
    //     static::created(function ($discussion) {
    //         if (!static::$isSyncing) {
    //             static::$isSyncing = true;
    //             try {
    //                 event(new OtherDiscussionUpdated($discussion, 'create'));
    //             } finally {
    //                 static::$isSyncing = false;
    //             }
    //         }
    //     });

    //     static::updated(function ($discussion) {
    //         // Cek jika status berubah menjadi 'Closed'
    //         if ($discussion->isDirty('status') && 
    //             $discussion->status === 'Closed' && 
    //             !static::$isUpdatingClosedAt) {
                
    //             static::$isUpdatingClosedAt = true;
    //             try {
    //                 DB::transaction(function() use ($discussion) {
    //                     $discussion->closed_at = Carbon::now();
    //                     $discussion->saveQuietly(); // Menggunakan saveQuietly() untuk menghindari trigger events
    //                 });
    //             } finally {
    //                 static::$isUpdatingClosedAt = false;
    //             }
    //         }

    //         // Trigger event update hanya jika bukan update closed_at
    //         if (!static::$isSyncing && !static::$isUpdatingClosedAt) {
    //             static::$isSyncing = true;
    //             try {
    //                 event(new OtherDiscussionUpdated($discussion, 'update'));
    //             } finally {
    //                 static::$isSyncing = false;
    //             }
    //         }
    //     });

    //     static::deleted(function ($discussion) {
    //         if (!static::$isSyncing) {
    //             static::$isSyncing = true;
    //             try {
    //                 event(new OtherDiscussionUpdated($discussion, 'delete'));
    //             } finally {
    //                 static::$isSyncing = false;
    //             }
    //         }
    //     });
    // }

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

    // Tambahkan relasi untuk dokumen
    public function documents()
    {
        return $this->hasMany(DiscussionDocument::class, 'other_discussion_id');
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
        try {
            // Definisi kode unit
            $unitCodes = [
                'mysql' => 'UPKD',         // UP KENDARI
                'mysql_poasia' => 'POAS',  // ULPLTD POASIA
                'mysql_wua_wua' => 'WUAS', // ULPLTD WUA-WUA
                'mysql_kolaka' => 'KOLA',  // ULPLTD KOLAKA
                'mysql_bau_bau' => 'BAUS'  // ULPLTD BAU-BAU
            ];

            // Cari power plant berdasarkan nama unit
            $powerPlant = PowerPlant::where('name', 'like', '%' . $unit . '%')->first();
            
            if (!$powerPlant) {
                throw new \Exception('Unit tidak ditemukan');
            }

            \Log::info('Found power plant:', [
                'unit' => $unit,
                'unit_source' => $powerPlant->unit_source
            ]);

            // Ambil kode unit berdasarkan unit_source
            if (!isset($unitCodes[$powerPlant->unit_source])) {
                throw new \Exception('Kode unit tidak ditemukan untuk unit source ini');
            }

            $unitCode = $unitCodes[$powerPlant->unit_source];

            // Ambil nomor urut terakhir untuk kode unit ini
            $lastDiscussion = self::where('no_pembahasan', 'like', $unitCode . '%')
                ->orderBy('no_pembahasan', 'desc')
                ->first();

            // Generate nomor berikutnya
            if ($lastDiscussion) {
                $lastNumber = (int) substr($lastDiscussion->no_pembahasan, -4);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            // Format nomor pembahasan (contoh: UPKD0001, POAS0001, dll)
            $noPembahasan = sprintf("%s%04d", $unitCode, $nextNumber);
            
            \Log::info('Generated no pembahasan:', [
                'no_pembahasan' => $noPembahasan,
                'unit_code' => $unitCode,
                'next_number' => $nextNumber
            ]);

            return $noPembahasan;
        } catch (\Exception $e) {
            \Log::error('Error generating no pembahasan:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 