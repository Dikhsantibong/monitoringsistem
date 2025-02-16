<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Machine extends Model
{
    use HasFactory;

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
        
        static::creating(function ($machine) {
            // Generate ID manually if not set
            if (!$machine->id) {
                $lastId = DB::connection(session('unit'))
                           ->table('machines')
                           ->max('id');
                $machine->id = ($lastId ?? 0) + 1;
            }
        });

        static::created(function ($machine) {
            try {
                // Refresh model untuk memastikan relasi ter-load
                $machine = $machine->fresh(['powerPlant']);
                
                if (!$machine || !$machine->powerPlant) {
                    \Log::warning('Skipping sync - Power Plant not found for machine:', [
                        'machine_id' => $machine->id ?? null
                    ]);
                    return;
                }

                $currentSession = session('unit', 'mysql');
                $powerPlant = $machine->powerPlant;

                // Sinkronisasi hanya jika kondisi terpenuhi
                if ($currentSession === 'mysql' && $powerPlant->unit_source !== 'mysql') {
                    self::syncToUpKendari('create', $machine);
                } elseif ($currentSession !== 'mysql' && $currentSession === $powerPlant->unit_source) {
                    self::syncToUpKendari('create', $machine);
                }
            } catch (\Exception $e) {
                \Log::error('Error in Machine sync:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });

        static::updated(function ($machine) {
            $currentSession = session('unit', 'mysql');
            $powerPlant = $machine->powerPlant;

            if ($powerPlant) {
                if ($currentSession === 'mysql' && $powerPlant->unit_source !== 'mysql') {
                    self::syncToUpKendari('update', $machine);
                } elseif ($currentSession !== 'mysql' && $currentSession === $powerPlant->unit_source) {
                    self::syncToUpKendari('update', $machine);
                }
            }
        });

        static::deleting(function ($machine) {
            try {
                DB::beginTransaction();
                
                // 1. Hapus MachineStatusLog terlebih dahulu
                $statusLogs = $machine->statusLogs;
                foreach ($statusLogs as $log) {
                    // Ini akan mentrigger event deleted di MachineStatusLog
                    $log->delete();
                }
                
                // 2. Hapus relasi lainnya
                $machine->operations()->delete();
                $machine->issues()->delete();
                $machine->metrics()->delete();
                
                // 3. Lakukan sinkronisasi penghapusan ke database target
                $currentSession = session('unit', 'mysql');
                $powerPlant = $machine->powerPlant;

                if ($powerPlant) {
                    if ($currentSession === 'mysql' && $powerPlant->unit_source !== 'mysql') {
                        self::syncToUpKendari('delete', $machine);
                    } elseif ($currentSession !== 'mysql' && $currentSession === $powerPlant->unit_source) {
                        self::syncToUpKendari('delete', $machine);
                    }
                }

                DB::commit();
                
                Log::info('Machine and related records deleted successfully', [
                    'machine_id' => $machine->id,
                    'unit_source' => $machine->unit_source
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                Log::error("Error deleting machine and relations:", [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    protected static function syncToUpKendari($action, $machine)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $targetConnection = session('unit') === 'mysql' 
                ? $machine->powerPlant->unit_source 
                : 'mysql';
            
            $targetDB = DB::connection($targetConnection);
            
            if ($action === 'delete') {
                // Hapus relasi di database target terlebih dahulu
                $targetDB->transaction(function () use ($targetDB, $machine) {
                    $targetDB->table('machine_operations')
                            ->where('machine_id', $machine->id)
                            ->delete();
                            
                    $targetDB->table('machine_status_logs')
                            ->where('machine_id', $machine->id)
                            ->delete();
                            
                    $targetDB->table('machine_issues')
                            ->where('machine_id', $machine->id)
                            ->delete();
                            
                    $targetDB->table('machine_metrics')
                            ->where('machine_id', $machine->id)
                            ->delete();
                            
                    $targetDB->table('machines')
                            ->where('id', $machine->id)
                            ->delete();
                });
            } else {
                $data = [
                    'id' => $machine->id,
                    'power_plant_id' => $machine->power_plant_id,
                    'name' => $machine->name,
                    'status' => $machine->status ?? 'STOP',
                    'capacity' => $machine->capacity,
                    'type' => $machine->type,
                    'serial_number' => $machine->serial_number,
                    'components' => $machine->components,
                    'unit_source' => session('unit'),
                    'created_at' => $machine->created_at,
                    'updated_at' => $machine->updated_at
                ];

                Log::info("Attempting to {$action} Machine sync", ['data' => $data]);

                switch($action) {
                    case 'create':
                        // Ensure ID exists in target database
                        if (!$targetDB->table('machines')->where('id', $machine->id)->exists()) {
                            $targetDB->table('machines')->insert($data);
                        }
                        break;
                        
                    case 'update':
                        $targetDB->table('machines')
                                 ->where('id', $machine->id)
                                 ->update($data);
                        break;
                }

                Log::info("Machine {$action} sync successful", [
                    'id' => $machine->id,
                    'unit' => session('unit'),
                    'target' => $targetConnection
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Machine {$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        } finally {
            self::$isSyncing = false;
        }
    }
}