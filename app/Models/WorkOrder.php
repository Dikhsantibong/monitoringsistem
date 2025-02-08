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
        'kendala',
        'tindak_lanjut',
        'document_path',
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
        
        static::creating(function ($model) {
            Log::info('Creating WO - Pre-save check:', [
                'wo_id' => $model->id,
                'connection' => $model->getConnectionName(),
                'exists' => static::on($model->getConnectionName())
                    ->where('id', $model->id)
                    ->exists()
            ]);
        });

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
        // Tambahkan pengecekan sesi sinkronisasi
        if (self::$isSyncing || session()->has('syncing_wo')) {
            Log::info('Skipping duplicate sync', [
                'action' => $action,
                'wo_id' => $workOrder->id,
                'is_syncing' => self::$isSyncing,
                'session_syncing' => session()->has('syncing_wo')
            ]);
            return;
        }

        try {
            self::$isSyncing = true;
            session(['syncing_wo' => true]);
            
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
                'kendala' => $workOrder->kendala,
                'tindak_lanjut' => $workOrder->tindak_lanjut,
                'document_path' => $workOrder->document_path,
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

            Log::info("Attempting to {$action} WO sync", [
                'data' => $data,
                'current_connection' => session('unit', 'mysql'),
                'target_connection' => $targetConnection
            ]);

            switch($action) {
                case 'create':
                    DB::connection($targetConnection)
                        ->table('work_orders')
                        ->insert($data);
                    break;
                    
                case 'update':
                    DB::connection($targetConnection)
                        ->table('work_orders')
                        ->where('id', $workOrder->id)
                        ->update($data);
                    break;
                    
                case 'delete':
                    DB::connection($targetConnection)
                        ->table('work_orders')
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
            Log::error('WO Sync failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'workOrder' => $workOrder->toArray()
            ]);
            throw $e;
        } finally {
            self::$isSyncing = false;
            session()->forget('syncing_wo');
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
                
                WoBacklog::create([
                    'no_wo' => $this->id,
                    'deskripsi' => $this->description,
                    'kendala' => $this->kendala,
                    'tindak_lanjut' => $this->tindak_lanjut,
                    'document_path' => $this->document_path,
                    'type_wo' => $this->type,
                    'priority' => $this->priority,
                    'schedule_start' => $this->schedule_start,
                    'schedule_finish' => $this->schedule_finish,
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

    public function getDocumentUrlAttribute()
    {
        if ($this->document_path) {
            // Pastikan path lengkap
            return url('storage/' . $this->document_path);
        }
        return null;
    }
}   