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
        'updated_at',
        'labor',
        'labors',
        'materials',
    ];

    protected $casts = [
        'labors' => 'array',
        'materials' => 'array',
        'schedule_start' => 'datetime',
        'schedule_finish' => 'datetime',
        'tanggal_backlog' => 'datetime',
    ];

    // Mutator untuk memastikan type_wo selalu dalam huruf kapital
    public function setTypeWoAttribute($value)
    {
        $this->attributes['type_wo'] = strtoupper($value);
    }

    // Accessor untuk memastikan type_wo selalu dalam huruf kapital
    public function getTypeWoAttribute($value)
    {
        return strtoupper($value);
    }

    public function getConnectionName()
    {
        return session('unit', 'mysql');
    }

    protected static function boot()
    {
        parent::boot();
        
        // Tambahkan event creating untuk cek sebelum data dibuat
        static::creating(function ($woBacklog) {
            // Cek duplikasi di database tujuan
            $currentConnection = session('unit', 'mysql');
            
            if ($currentConnection !== 'mysql') {
                // Jika dari unit, cek di database utama
                $exists = DB::connection('mysql')
                    ->table('wo_backlog')
                    ->where('no_wo', $woBacklog->no_wo)
                    ->where('unit_source', $currentConnection)
                    ->exists();

                if ($exists) {
                    Log::info('Preventing duplicate WO Backlog creation', [
                        'no_wo' => $woBacklog->no_wo,
                        'unit_source' => $currentConnection,
                        'connection' => 'mysql'
                    ]);
                    return false;
                }
            }
        });

        static::created(function ($woBacklog) {
            if (!self::$isSyncing && !session()->has('syncing_backlog')) {
                self::syncData('create', $woBacklog);
            }
        });

        static::updated(function ($woBacklog) {
            if (!self::$isSyncing && !session()->has('syncing_backlog')) {
                self::syncData('update', $woBacklog);
            }
        });

        static::deleted(function ($woBacklog) {
            if (!self::$isSyncing && !session()->has('syncing_backlog')) {
                self::syncData('delete', $woBacklog);
            }
        });
    }

    protected static function syncData($action, $woBacklog)
    {
        if (self::$isSyncing || session()->has('syncing_backlog')) {
            Log::info('Skipping duplicate sync', [
                'action' => $action,
                'no_wo' => $woBacklog->no_wo,
                'is_syncing' => self::$isSyncing,
                'session_syncing' => session()->has('syncing_backlog')
            ]);
            return;
        }

        try {
            self::$isSyncing = true;
            session(['syncing_backlog' => true]);
            
            $powerPlant = PowerPlant::find($woBacklog->power_plant_id);
            $currentConnection = session('unit', 'mysql');
            
            Log::info('Starting WO Backlog sync', [
                'action' => $action,
                'current_connection' => $currentConnection,
                'power_plant' => $powerPlant ? $powerPlant->toArray() : null
            ]);

            // Jika koneksi saat ini bukan mysql (berarti dari unit)
            if ($currentConnection !== 'mysql') {
                // Double check untuk memastikan tidak ada duplikasi
                $existingRecord = DB::connection('mysql')
                    ->table('wo_backlog')
                    ->where('no_wo', $woBacklog->no_wo)
                    ->where('unit_source', $currentConnection)
                    ->lockForUpdate()
                    ->first();

                if ($existingRecord && $action === 'create') {
                    Log::info('Skipping duplicate creation in main database', [
                        'no_wo' => $woBacklog->no_wo,
                        'unit_source' => $currentConnection
                    ]);
                    return;
                }

                $data = [
                    'no_wo' => $woBacklog->no_wo,
                    'deskripsi' => $woBacklog->deskripsi,
                    'kendala' => $woBacklog->kendala,
                    'tindak_lanjut' => $woBacklog->tindak_lanjut,
                    'document_path' => $woBacklog->document_path,
                    'type_wo' => $woBacklog->type_wo,
                    'priority' => $woBacklog->priority,
                    'labor' => $woBacklog->labor,
                    'labors' => $woBacklog->labors,
                    'materials' => $woBacklog->materials,
                    'schedule_start' => $woBacklog->schedule_start,
                    'schedule_finish' => $woBacklog->schedule_finish,
                    'tanggal_backlog' => $woBacklog->tanggal_backlog,
                    'status' => $woBacklog->status,
                    'keterangan' => $woBacklog->keterangan,
                    'power_plant_id' => $woBacklog->power_plant_id,
                    'unit_source' => $currentConnection,
                    'created_at' => $woBacklog->created_at,
                    'updated_at' => $woBacklog->updated_at
                ];

                DB::connection('mysql')->transaction(function () use ($action, $data, $woBacklog, $currentConnection) {
                    switch($action) {
                        case 'create':
                            DB::connection('mysql')
                                ->table('wo_backlog')
                                ->insert($data);
                            break;
                            
                        case 'update':
                            DB::connection('mysql')
                                ->table('wo_backlog')
                                ->where('no_wo', $woBacklog->no_wo)
                                ->where('unit_source', $currentConnection)
                                ->update($data);
                            break;
                            
                        case 'delete':
                            DB::connection('mysql')
                                ->table('wo_backlog')
                                ->where('no_wo', $woBacklog->no_wo)
                                ->where('unit_source', $currentConnection)
                                ->delete();
                            break;
                    }
                });
            }
            // Jika koneksi saat ini adalah mysql dan ada power plant
            elseif ($powerPlant && $powerPlant->unit_source !== 'mysql') {
                // Sync ke database unit
                $targetConnection = PowerPlant::getConnectionByUnitSource($powerPlant->unit_source);
                
                $data = [
                    'no_wo' => $woBacklog->no_wo,
                    'deskripsi' => $woBacklog->deskripsi,
                    'kendala' => $woBacklog->kendala,
                    'tindak_lanjut' => $woBacklog->tindak_lanjut,
                    'document_path' => $woBacklog->document_path,
                    'type_wo' => $woBacklog->type_wo,
                    'priority' => $woBacklog->priority,
                    'labor' => $woBacklog->labor,
                    'labors' => $woBacklog->labors,
                    'materials' => $woBacklog->materials,
                    'schedule_start' => $woBacklog->schedule_start,
                    'schedule_finish' => $woBacklog->schedule_finish,
                    'tanggal_backlog' => $woBacklog->tanggal_backlog,
                    'status' => $woBacklog->status,
                    'keterangan' => $woBacklog->keterangan,
                    'power_plant_id' => $woBacklog->power_plant_id,
                    'unit_source' => $powerPlant->unit_source,
                    'created_at' => $woBacklog->created_at,
                    'updated_at' => $woBacklog->updated_at
                ];

                Log::info("Syncing to unit database", [
                    'action' => $action,
                    'target_connection' => $targetConnection,
                    'data' => $data
                ]);

                switch($action) {
                    case 'create':
                        DB::connection($targetConnection)
                            ->table('wo_backlog')
                            ->insert($data);
                        break;
                        
                    case 'update':
                        DB::connection($targetConnection)
                            ->table('wo_backlog')
                            ->where('no_wo', $woBacklog->no_wo)
                            ->update($data);
                        break;
                        
                    case 'delete':
                        DB::connection($targetConnection)
                            ->table('wo_backlog')
                            ->where('no_wo', $woBacklog->no_wo)
                            ->delete();
                        break;
                }
            }

            Log::info("WO Backlog Sync completed successfully", [
                'no_wo' => $woBacklog->no_wo,
                'action' => $action,
                'current_connection' => $currentConnection
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
            session()->forget('syncing_backlog');
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
                    'status' => $this->status,
                    'unit_source' => $this->unit_source,
                    'current_connection' => session('unit', 'mysql')
                ]);

                // Persiapkan data untuk WorkOrder dengan format yang benar
                $workOrderData = [
                    'id' => $this->no_wo,
                    'description' => $this->deskripsi,
                    'kendala' => $this->kendala,
                    'tindak_lanjut' => $this->tindak_lanjut,
                    'document_path' => $this->document_path,
                    'type' => strtoupper($this->type_wo),
                    'priority' => $this->priority,
                    'labor' => $this->labor,
                    'labors' => is_array($this->labors) ? json_encode($this->labors) : $this->labors,
                    'materials' => is_array($this->materials) ? json_encode($this->materials) : $this->materials,
                    'schedule_start' => $this->schedule_start,
                    'schedule_finish' => $this->schedule_finish,
                    'status' => 'Closed',
                    'power_plant_id' => $this->power_plant_id,
                    'unit_source' => $this->unit_source,
                    'is_active' => false,
                    'is_backlogged' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // 1. Hapus data dari wo_backlog di kedua database terlebih dahulu
                $connections = [$this->unit_source];
                if ($this->unit_source !== 'mysql') {
                    $connections[] = 'mysql';
                }

                foreach ($connections as $connection) {
                    try {
                        // Disable foreign key checks
                        DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=0');
                        
                        // Hapus data
                        $deleted = DB::connection($connection)
                            ->table('wo_backlog')
                            ->where('no_wo', $this->no_wo)
                            ->delete();

                        // Enable foreign key checks kembali
                        DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=1');

                        Log::info('Deleted from wo_backlog', [
                            'connection' => $connection,
                            'wo_id' => $this->no_wo,
                            'deleted' => $deleted
                        ]);
                    } catch (\Exception $e) {
                        Log::warning('Failed to delete from wo_backlog', [
                            'connection' => $connection,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // 2. Cek dan update atau insert Work Order di database unit
                if ($this->unit_source !== 'mysql') {
                    try {
                        $exists = DB::connection($this->unit_source)
                            ->table('work_orders')
                            ->where('id', $this->no_wo)
                            ->exists();

                        if ($exists) {
                            DB::connection($this->unit_source)
                                ->table('work_orders')
                                ->where('id', $this->no_wo)
                                ->update($workOrderData);
                            Log::info('Updated existing WorkOrder in unit database', [
                                'connection' => $this->unit_source,
                                'wo_id' => $this->no_wo
                            ]);
                        } else {
                            DB::connection($this->unit_source)
                                ->table('work_orders')
                                ->insert($workOrderData);
                            Log::info('Created new WorkOrder in unit database', [
                                'connection' => $this->unit_source,
                                'wo_id' => $this->no_wo
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to create/update WorkOrder in unit database', [
                            'error' => $e->getMessage(),
                            'connection' => $this->unit_source
                        ]);
                        throw $e;
                    }
                }

                // 3. Cek dan update atau insert Work Order di database utama (mysql)
                try {
                    $exists = DB::connection('mysql')
                        ->table('work_orders')
                        ->where('id', $this->no_wo)
                        ->exists();

                    if ($exists) {
                        DB::connection('mysql')
                            ->table('work_orders')
                            ->where('id', $this->no_wo)
                            ->update($workOrderData);
                        Log::info('Updated existing WorkOrder in main database', [
                            'wo_id' => $this->no_wo
                        ]);
                    } else {
                        DB::connection('mysql')
                            ->table('work_orders')
                            ->insert($workOrderData);
                        Log::info('Created new WorkOrder in main database', [
                            'wo_id' => $this->no_wo
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to create/update WorkOrder in main database', [
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }

                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in moveBackToWorkOrder', [
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