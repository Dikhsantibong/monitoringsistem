<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WoBacklog extends Model
{
    use HasFactory;

    public static $isSyncing = false;

    protected $table = 'wo_backlog';

    protected $fillable = [
        'no_wo',
        'deskripsi',
        'tanggal_backlog',
        'keterangan',
        'status',
        'unit_source',
        'power_plant_id'
    ];

    public function getConnectionName()
    {
        return session('unit');
    }

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        // Handle Created Event
        static::created(function ($woBacklog) {
            self::syncToUpKendari('create', $woBacklog);
        });

        // Handle Updated Event
        static::updated(function ($woBacklog) {
            self::syncToUpKendari('update', $woBacklog);
        });

        // Handle Deleted Event
        static::deleted(function ($woBacklog) {
            self::syncToUpKendari('delete', $woBacklog);
        });
    }

    protected static function syncToUpKendari($action, $woBacklog)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $data = [
                'no_wo' => $woBacklog->no_wo,
                'deskripsi' => $woBacklog->deskripsi,
                'tanggal_backlog' => $woBacklog->tanggal_backlog,
                'keterangan' => $woBacklog->keterangan,
                'status' => $woBacklog->status,
                'unit_source' => session('unit'),
                'power_plant_id' => $woBacklog->power_plant_id,
                'created_at' => $woBacklog->created_at,
                'updated_at' => $woBacklog->updated_at
            ];

            Log::info("Attempting to {$action} WO Backlog sync", ['data' => $data]);

            $upKendari = DB::connection('mysql')->table('wo_backlog');

            switch($action) {
                case 'create':
                    $upKendari->insert($data);
                    break;
                    
                case 'update':
                    $upKendari->where('no_wo', $woBacklog->no_wo)
                             ->update($data);
                    break;
                    
                case 'delete':
                    $upKendari->where('no_wo', $woBacklog->no_wo)
                             ->delete();
                    break;
            }

            Log::info("WO Backlog {$action} sync successful", [
                'no_wo' => $woBacklog->no_wo,
                'unit' => 'poasia'
            ]);

        } catch (\Exception $e) {
            Log::error("WO Backlog {$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
        }
    }
}
