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
        'id',
        'no_wo',
        'deskripsi',
        'tanggal_backlog',
        'status',
        'keterangan',
        'power_plant_id',
        'unit_source',
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
        
        static::created(function ($woBacklog) {
            self::syncData('create', $woBacklog);
        });

        static::updated(function ($woBacklog) {
            self::syncData('update', $woBacklog);
        });

        static::deleted(function ($woBacklog) {
            self::syncData('delete', $woBacklog);
        });
    }

    protected static function syncData($action, $woBacklog)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $powerPlant = PowerPlant::find($woBacklog->power_plant_id);
            if (!$powerPlant) {
                throw new \Exception('Power Plant not found');
            }

            $data = [
                'no_wo' => $woBacklog->no_wo,
                'deskripsi' => $woBacklog->deskripsi,
                'tanggal_backlog' => $woBacklog->tanggal_backlog,
                'keterangan' => $woBacklog->keterangan,
                'status' => $woBacklog->status,
                'power_plant_id' => $woBacklog->power_plant_id,
                'unit_source' => $powerPlant->unit_source,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $currentConnection = session('unit', 'mysql');
            $targetConnection = $currentConnection === 'mysql' 
                ? PowerPlant::getConnectionByUnitSource($powerPlant->unit_source)
                : 'mysql';

            Log::info("Current connection: {$currentConnection}, Target connection: {$targetConnection}");

            $targetDB = DB::connection($targetConnection);

            Log::info("Attempting to {$action} WO Backlog sync", [
                'data' => $data,
                'current_connection' => $currentConnection,
                'target_connection' => $targetConnection
            ]);

            switch($action) {
                case 'create':
                    $targetDB->table('wo_backlog')->insert($data);
                    break;
                    
                case 'update':
                    $targetDB->table('wo_backlog')
                            ->where('no_wo', $woBacklog->no_wo)
                            ->update($data);
                    break;
                    
                case 'delete':
                    $targetDB->table('wo_backlog')
                            ->where('no_wo', $woBacklog->no_wo)
                            ->delete();
                    break;
            }

            Log::info("WO Backlog Sync successful", [
                'no_wo' => $woBacklog->no_wo,
                'unit' => $powerPlant->unit_source,
                'action' => $action
            ]);

        } catch (\Exception $e) {
            Log::error("WO Backlog Sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'woBacklog' => $woBacklog->toArray()
            ]);
            throw $e;
        } finally {
            self::$isSyncing = false;
        }
    }
    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }

    public function moveBackToWorkOrder()
    {
        if ($this->status === 'Closed') {
            try {
                DB::beginTransaction();
                
                // Buat WO baru dengan status closed
                WorkOrder::create([
                    'id' => $this->no_wo,
                    'description' => $this->deskripsi,
                    'status' => 'Closed',
                    'power_plant_id' => $this->power_plant_id,
                    'unit_source' => $this->unit_source,
                    'schedule_start' => now(),
                    'schedule_finish' => now(),
                    'is_active' => false
                ]);

                // Hapus dari backlog
                $this->delete();

                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error moving backlog to WO: ' . $e->getMessage());
                return false;
            }
        }
        return false;
    }
}   