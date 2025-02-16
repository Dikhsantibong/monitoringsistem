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
        
        static::creating(function ($machineOperation) {
            // Generate ID manually if not set
            if (!$machineOperation->id) {
                $lastId = DB::connection(session('unit'))
                           ->table('machine_operations')
                           ->max('id');
                $machineOperation->id = ($lastId ?? 0) + 1;
            }

            // Set default values
            $machineOperation->dmn = $machineOperation->dmn ?? 0;
            $machineOperation->dmp = $machineOperation->dmp ?? 0;
            $machineOperation->load_value = $machineOperation->load_value ?? 0;
            $machineOperation->recorded_at = $machineOperation->recorded_at ?? now();
        });

        static::created(function ($machineOperation) {
            try {
                // Refresh model untuk memastikan relasi ter-load
                $machineOperation = $machineOperation->fresh(['machine.powerPlant']);
                
                if (!$machineOperation->machine || !$machineOperation->machine->powerPlant) {
                    \Log::warning('Skipping sync - Invalid relations for operation:', [
                        'operation_id' => $machineOperation->id
                    ]);
                    return;
                }

                $currentSession = session('unit', 'mysql');
                $powerPlant = $machineOperation->machine->powerPlant;

                // Sinkronisasi hanya jika kondisi terpenuhi
                if ($currentSession === 'mysql' && $powerPlant->unit_source !== 'mysql') {
                    self::syncToUpKendari('create', $machineOperation);
                } elseif ($currentSession !== 'mysql' && $currentSession === $powerPlant->unit_source) {
                    self::syncToUpKendari('create', $machineOperation);
                }
            } catch (\Exception $e) {
                \Log::error('Error in MachineOperation sync:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });

        static::updated(function ($machineOperation) {
            $currentSession = session('unit', 'mysql');
            $powerPlant = $machineOperation->machine->powerPlant;

            if ($powerPlant) {
                if ($currentSession === 'mysql' && $powerPlant->unit_source !== 'mysql') {
                    self::syncToUpKendari('update', $machineOperation);
                } elseif ($currentSession !== 'mysql' && $currentSession === $powerPlant->unit_source) {
                    self::syncToUpKendari('update', $machineOperation);
                }
            }
        });

        static::deleted(function ($machineOperation) {
            $currentSession = session('unit', 'mysql');
            $powerPlant = $machineOperation->machine->powerPlant;

            if ($powerPlant) {
                if ($currentSession === 'mysql' && $powerPlant->unit_source !== 'mysql') {
                    self::syncToUpKendari('delete', $machineOperation);
                } elseif ($currentSession !== 'mysql' && $currentSession === $powerPlant->unit_source) {
                    self::syncToUpKendari('delete', $machineOperation);
                }
            }
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

            $targetConnection = session('unit') === 'mysql' 
                ? $machineOperation->machine->powerPlant->unit_source 
                : 'mysql';

            $targetDB = DB::connection($targetConnection);

            switch($action) {
                case 'create':
                    // Ensure ID exists in target database
                    if (!$targetDB->table('machine_operations')->where('id', $machineOperation->id)->exists()) {
                        $targetDB->table('machine_operations')->insert($data);
                    }
                    break;
                    
                case 'update':
                    $targetDB->table('machine_operations')
                             ->where('id', $machineOperation->id)
                             ->update($data);
                    break;
                    
                case 'delete':
                    $targetDB->table('machine_operations')
                             ->where('id', $machineOperation->id)
                             ->delete();
                    break;
            }

            Log::info("Machine Operation {$action} sync successful", [
                'id' => $machineOperation->id,
                'unit' => session('unit'),
                'target' => $targetConnection
            ]);

        } catch (\Exception $e) {
            Log::error("Machine Operation {$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw exception to handle it in the controller
        } finally {
            self::$isSyncing = false;
        }
    }
}
