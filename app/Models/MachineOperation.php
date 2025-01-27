<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MachineOperation extends Model
{
    use HasFactory;

    public static $isSyncing = false;

    protected $table = 'machine_operations';

    protected $fillable = [
        'machine_id',
        'dmn',
        'dmp',
        'load_value',
        'hop',
        'recorded_at',
        'keterangan',
        'unit_source'
    ];

    protected $attributes = [
        'load_value' => null,
        'keterangan' => null,
        'dmn' => 0,
        'dmp' => 0
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function getConnectionName()
    {
        return session('unit');
    }

    protected static function boot()
    {
        parent::boot();
        
        // Handle Created Event
        static::created(function ($machineOperation) {
            self::syncToUpKendari('create', $machineOperation);
        });

        // Handle Updated Event
        static::updated(function ($machineOperation) {
            self::syncToUpKendari('update', $machineOperation);
        });

        // Handle Deleted Event
        static::deleted(function ($machineOperation) {
            self::syncToUpKendari('delete', $machineOperation);
        });
    }

    protected static function syncToUpKendari($action, $machineOperation)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $data = [
                'id' => $machineOperation->id,
                'machine_id' => $machineOperation->machine_id,
                'dmn' => $machineOperation->dmn,
                'dmp' => $machineOperation->dmp,
                'load_value' => $machineOperation->load_value,
                'hop' => $machineOperation->hop,
                'recorded_at' => $machineOperation->recorded_at,
                'keterangan' => $machineOperation->keterangan,
                'unit_source' => session('unit'),
                'created_at' => $machineOperation->created_at,
                'updated_at' => $machineOperation->updated_at
            ];

            Log::info("Attempting to {$action} Machine Operation sync", ['data' => $data]);

            $upKendari = DB::connection('mysql')->table('machine_operations');

            switch($action) {
                case 'create':
                    $upKendari->insert($data);
                    break;
                    
                case 'update':
                    $upKendari->where('id', $machineOperation->id)
                             ->update($data);
                    break;
                    
                case 'delete':
                    $upKendari->where('id', $machineOperation->id)
                             ->delete();
                    break;
            }

            Log::info("Machine Operation {$action} sync successful", [
                'id' => $machineOperation->id,
                'unit' => 'poasia'
            ]);

        } catch (\Exception $e) {
            Log::error("Machine Operation {$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
        }
    }
}
