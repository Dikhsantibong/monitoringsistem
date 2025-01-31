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
        'type_wo',
        'priority',
        'schedule_start',
        'schedule_finish',
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
                
                Log::info('Starting moveBackToWorkOrder process', [
                    'no_wo' => $this->no_wo,
                    'status' => $this->status
                ]);

                // Buat WO baru dengan data asli
                $workOrder = WorkOrder::create([
                    'id' => $this->no_wo,
                    'description' => $this->deskripsi,
                    'type' => $this->type_wo,         // gunakan data asli
                    'priority' => $this->priority,     // gunakan data asli
                    'schedule_start' => $this->schedule_start, // gunakan data asli
                    'schedule_finish' => $this->schedule_finish, // gunakan data asli
                    'status' => 'Closed',
                    'power_plant_id' => $this->power_plant_id,
                    'unit_source' => $this->unit_source,
                    'is_active' => false,
                    'is_backlogged' => false
                ]);

                Log::info('Created new WorkOrder with original data', [
                    'work_order_id' => $workOrder->id,
                    'type' => $this->type_wo,
                    'priority' => $this->priority,
                    'schedule_start' => $this->schedule_start,
                    'schedule_finish' => $this->schedule_finish
                ]);

                // Hapus dari backlog
                $this->delete();
                
                Log::info('Deleted from backlog', [
                    'no_wo' => $this->no_wo
                ]);

                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error moving backlog to WO', [
                    'no_wo' => $this->no_wo,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        }
        return false;
    }
}   