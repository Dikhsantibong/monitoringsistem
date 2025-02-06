<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use HasFactory, SoftDeletes;

    public static $isSyncing = false;

    protected $fillable = [
        'power_plant_id',
        'name',
        'status',
        'capacity',
        'type',
        'serial_number',
        'components',
        'unit_source'
    ];

    public function issues()
    {
        return $this->hasMany(MachineIssue::class);
    }

    public function metrics()
    {
        return $this->hasMany(MachineMetric::class);
    }

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class, 'power_plant_id');
    }

    public function operations()
    {
        return $this->hasMany(MachineOperation::class);
    }

    public function machineOperations()
    {
        return $this->hasMany(MachineOperation::class, 'machine_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(MachineStatusLog::class);
    }

    public function getConnectionName()
    {
        return session('unit');
    }

    protected static function boot()
    {
        parent::boot();
        
        // Handle Created Event
        static::created(function ($machine) {
            self::syncToUpKendari('create', $machine);
        });

        // Handle Updated Event
        static::updated(function ($machine) {
            self::syncToUpKendari('update', $machine);
        });

        // Handle Deleted Event
        static::deleted(function ($machine) {
            self::syncToUpKendari('delete', $machine);
        });
    }

    protected static function syncToUpKendari($action, $machine)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $data = [
                'id' => $machine->id,
                'power_plant_id' => $machine->power_plant_id,
                'name' => $machine->name,
                'status' => $machine->status,
                'capacity' => $machine->capacity,
                'type' => $machine->type,
                'serial_number' => $machine->serial_number,
                'components' => $machine->components,
                'unit_source' => session('unit'),
                'created_at' => $machine->created_at,
                'updated_at' => $machine->updated_at
            ];

            Log::info("Attempting to {$action} Machine sync", ['data' => $data]);

            $upKendari = DB::connection('mysql')->table('machines');

            switch($action) {
                case 'create':
                    $upKendari->insert($data);
                    break;
                    
                case 'update':
                    $upKendari->where('id', $machine->id)
                             ->update($data);
                    break;
                    
                case 'delete':
                    $upKendari->where('id', $machine->id)
                             ->delete();
                    break;
            }

            Log::info("Machine {$action} sync successful", [
                'id' => $machine->id,
                'unit' => 'poasia'
            ]);

        } catch (\Exception $e) {
            Log::error("Machine {$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
        }
    }
}