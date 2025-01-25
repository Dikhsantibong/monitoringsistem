<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class WorkOrder extends Model
{
    use HasFactory;

    public static $isSyncing = false;
    public static $maxRetries = 3;
    public static $retryDelay = 1; // dalam detik

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

            Log::info("Starting sync - Current: {$currentConnection}, Target: {$targetConnection}");

            $attempt = 0;
            $maxAttempts = 3;
            $success = false;

            while (!$success && $attempt < $maxAttempts) {
                try {
                    // Set session variables terlebih dahulu
                    DB::connection($targetConnection)->statement('SET SESSION innodb_lock_wait_timeout = 5');
                    DB::connection($targetConnection)->statement('SET SESSION transaction_isolation = "READ-UNCOMMITTED"');

                    // Cek data tanpa lock
                    $exists = DB::connection($targetConnection)
                        ->table('work_orders')
                        ->where('id', $data['id'])
                        ->exists();

                    if ($action === 'create') {
                        if (!$exists) {
                            // Gunakan insert ignore untuk menghindari error duplikat
                            DB::connection($targetConnection)
                                ->statement("INSERT IGNORE INTO work_orders 
                                    (id, description, type, status, priority, schedule_start, 
                                    schedule_finish, power_plant_id, unit_source, is_active, 
                                    is_backlogged, created_at, updated_at) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                                    array_values($data));
                        }
                    } elseif ($action === 'update' && $exists) {
                        DB::connection($targetConnection)
                            ->table('work_orders')
                            ->where('id', $workOrder->id)
                            ->update($data);
                    } elseif ($action === 'delete' && $exists) {
                        DB::connection($targetConnection)
                            ->table('work_orders')
                            ->where('id', $workOrder->id)
                            ->delete();
                    }

                    $success = true;
                    Log::info("WO Sync successful on attempt #{$attempt}", [
                        'id' => $workOrder->id,
                        'unit' => $powerPlant->unit_source,
                        'action' => $action
                    ]);

                } catch (QueryException $e) {
                    if (str_contains($e->getMessage(), 'Lock wait timeout exceeded')) {
                        $attempt++;
                        if ($attempt < $maxAttempts) {
                            $waitTime = pow(2, $attempt); // exponential backoff
                            Log::warning("Lock timeout on attempt #{$attempt}, waiting {$waitTime}s before retry", [
                                'id' => $workOrder->id,
                                'error' => $e->getMessage()
                            ]);
                            sleep($waitTime);
                            continue;
                        }
                    }
                    throw $e;
                }
            }

            if (!$success) {
                throw new \Exception("Sync failed after {$maxAttempts} attempts");
            }

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
