<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkOrder extends Model
{
    use HasFactory;

    public static $isSyncing = false;

    protected $fillable = [
        'id',
        'description',
        'type',
        'status',
        'priority',
        'schedule_start',
        'schedule_finish',
        'power_plant_id',
        'unit_source',
        'is_active',
        'is_backlogged'
    ];

    public $incrementing = false;

    public function isExpired()
    {
        return Carbon::parse($this->schedule_finish)->isPast() && $this->status == 'Open';
    }

    public function moveToBacklog()
    {
        if ($this->isExpired()) {
            WoBacklog::create([
                'no_wo' => $this->id,
                'deskripsi' => $this->description,
                'tanggal_backlog' => $this->schedule_finish,
                'keterangan' => 'Otomatis masuk backlog karena melewati jadwal',
                'status' => 'Open'
            ]);

            return true;
        }
        return false;
    }

    public function getConnectionName()
    {
        return session('unit', 'mysql');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($workOrder) {
            self::syncData('create', $workOrder);
        });

        static::updated(function ($workOrder) {
            self::syncData('update', $workOrder);
        });

        static::deleted(function ($workOrder) {
            self::syncData('delete', $workOrder);
        });
    }

    protected static function syncData($action, $workOrder)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $powerPlant = PowerPlant::find($workOrder->power_plant_id);
            if (!$powerPlant) {
                throw new \Exception('Power Plant not found');
            }

            $data = [
                'id' => $workOrder->id,
                'description' => $workOrder->description,
                'type' => $workOrder->type,
                'status' => $workOrder->status,
                'priority' => $workOrder->priority,
                'schedule_start' => $workOrder->schedule_start,
                'schedule_finish' => $workOrder->schedule_finish,
                'power_plant_id' => $workOrder->power_plant_id,
                'unit_source' => $powerPlant->unit_source,
                'is_active' => $workOrder->is_active,
                'is_backlogged' => $workOrder->is_backlogged,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $currentConnection = session('unit', 'mysql');
            $targetConnection = $currentConnection === 'mysql' 
                ? PowerPlant::getConnectionByUnitSource($powerPlant->unit_source)
                : 'mysql';

            Log::info("Current connection: {$currentConnection}, Target connection: {$targetConnection}");

            $targetDB = DB::connection($targetConnection);

            Log::info("Attempting to {$action} WO sync", [
                'data' => $data,
                'current_connection' => $currentConnection,
                'target_connection' => $targetConnection
            ]);

            switch($action) {
                case 'create':
                    $targetDB->table('work_orders')->insert($data);
                    break;
                    
                case 'update':
                    $targetDB->table('work_orders')
                            ->where('id', $workOrder->id)
                            ->update($data);
                    break;
                    
                case 'delete':
                    $targetDB->table('work_orders')
                            ->where('id', $workOrder->id)
                            ->delete();
                    break;
            }

            Log::info("WO Sync successful", [
                'id' => $workOrder->id,
                'unit' => $powerPlant->unit_source,
                'action' => $action
            ]);

        } catch (\Exception $e) {
            Log::error("WO Sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'workOrder' => $workOrder->toArray()
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

   
} 