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
            
            // Skip sinkronisasi jika unit_source adalah mysql (database utama)
            if (!$powerPlant || $powerPlant->unit_source === 'mysql') {
                Log::info('Skipping sync for main database (mysql)', [
                    'power_plant' => $powerPlant->name ?? 'unknown',
                    'unit_source' => $powerPlant->unit_source ?? 'unknown'
                ]);
                return;
            }

            // Lanjutkan proses sinkronisasi untuk unit lain
            $targetConnection = PowerPlant::getConnectionByUnitSource($powerPlant->unit_source);
            
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

            Log::info("Attempting to {$action} WO sync", ['data' => $data]);

            switch($action) {
                case 'create':
                    $targetDB = DB::connection($targetConnection);
                    $targetDB->table('work_orders')->insert($data);
                    break;
                    
                case 'update':
                    $targetDB = DB::connection($targetConnection);
                    $targetDB->table('work_orders')
                            ->where('id', $workOrder->id)
                            ->update($data);
                    break;
                    
                case 'delete':
                    $targetDB = DB::connection($targetConnection);
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
            Log::error('WO Sync failed', [
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

    public function checkAndMoveToBacklog()
    {
        if ($this->status !== 'Closed' && Carbon::parse($this->schedule_finish)->isPast()) {
            try {
                DB::beginTransaction();
                
                // Buat WO Backlog dengan menyimpan data asli
                WoBacklog::create([
                    'no_wo' => $this->id,
                    'deskripsi' => $this->description,
                    'type_wo' => $this->type,           // simpan data asli
                    'priority' => $this->priority,       // simpan data asli
                    'schedule_start' => $this->schedule_start,   // simpan data asli
                    'schedule_finish' => $this->schedule_finish, // simpan data asli
                    'tanggal_backlog' => now(),
                    'keterangan' => 'Auto-generated from expired WO',
                    'status' => 'Open',
                    'power_plant_id' => $this->power_plant_id,
                    'unit_source' => $this->unit_source
                ]);

                Log::info('Created backlog with original WO data', [
                    'wo_id' => $this->id,
                    'type' => $this->type,
                    'priority' => $this->priority,
                    'schedule_start' => $this->schedule_start,
                    'schedule_finish' => $this->schedule_finish
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