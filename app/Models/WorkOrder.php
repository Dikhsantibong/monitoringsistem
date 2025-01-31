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
                'created_at' => $workOrder->created_at,
                'updated_at' => $workOrder->updated_at
            ];

            // Jika input dari UP Kendari, sync ke unit lokal
            if (session('unit') === 'mysql') {
                $targetConnection = PowerPlant::getConnectionByUnitSource($powerPlant->unit_source);
                $targetDB = DB::connection($targetConnection);
            } 
            // Jika input dari unit lokal, sync ke UP Kendari
            else {
                $targetDB = DB::connection('mysql');
            }

            Log::info("Attempting to {$action} WO sync", ['data' => $data]);

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
                'unit' => $powerPlant->unit_source
            ]);

        } catch (\Exception $e) {
            Log::error("WO Sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
        }
    }

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }

    public function checkAndMoveToBacklog()
    {
        if ($this->status !== 'Closed' && Carbon::parse($this->schedule_finish)->isPast()) {
            try {
                DB::beginTransaction();
                
                // Buat WO Backlog baru
                WoBacklog::create([
                    'no_wo' => $this->id,
                    'deskripsi' => $this->description,
                    'tanggal_backlog' => now(),
                    'keterangan' => 'Auto-generated from expired WO',
                    'status' => 'Open',
                    'power_plant_id' => $this->power_plant_id,
                    'unit_source' => $this->unit_source
                ]);

                // Hapus WO yang expired
                $this->delete();

                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error moving WO to backlog: ' . $e->getMessage());
                return false;
            }
        }
        return false;
    }
} 