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
        'kendala',
        'tindak_lanjut',
        'document_path',
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
            
            // Data lengkap untuk sinkronisasi
            $data = [
                'no_wo' => $woBacklog->no_wo,
                'deskripsi' => $woBacklog->deskripsi,
                'type_wo' => $woBacklog->type_wo,           // tambahkan
                'priority' => $woBacklog->priority,          // tambahkan
                'schedule_start' => $woBacklog->schedule_start,   // tambahkan
                'schedule_finish' => $woBacklog->schedule_finish, // tambahkan
                'tanggal_backlog' => $woBacklog->tanggal_backlog,
                'keterangan' => $woBacklog->keterangan,
                'status' => $woBacklog->status,
                'power_plant_id' => $woBacklog->power_plant_id,
                'unit_source' => $powerPlant->unit_source,
                'created_at' => $woBacklog->created_at,
                'updated_at' => $woBacklog->updated_at
            ];

            $targetDB = DB::connection($targetConnection);

            Log::info("Attempting to {$action} WO Backlog sync", [
                'data' => $data,
                'current_connection' => session('unit', 'mysql'),
                'target_connection' => $targetDB->getName()
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
                'action' => $action,
                'type_wo' => $data['type_wo'],
                'priority' => $data['priority'],
                'schedule_start' => $data['schedule_start'],
                'schedule_finish' => $data['schedule_finish']
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

                $workOrder = WorkOrder::create([
                    'id' => $this->no_wo,
                    'description' => $this->deskripsi,
                    'kendala' => $this->kendala,
                    'tindak_lanjut' => $this->tindak_lanjut,
                    'document_path' => $this->document_path,
                    'type' => $this->type_wo,
                    'priority' => $this->priority,
                    'schedule_start' => $this->schedule_start,
                    'schedule_finish' => $this->schedule_finish,
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