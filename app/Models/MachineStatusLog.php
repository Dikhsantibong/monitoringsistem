<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Events\MachineStatusUpdated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * MachineStatusLog Model
 * 
 * Model ini menangani pencatatan status mesin dan sinkronisasi data
 * antara database UP Kendari (mysql) dan database unit lokal.
 *
 * Sinkronisasi 2 arah:
 * 1. UP Kendari -> Unit Lokal: Ketika session = 'mysql'
 * 2. Unit Lokal -> UP Kendari: Ketika session = [mysql_poasia/mysql_kolaka/dll]
 *
 * @property int $id
 * @property int $machine_id
 * @property string $status
 * @property float $dmn
 * @property float $dmp
 * @property string $unit_source
 * @property \Carbon\Carbon $tanggal
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatusLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatusLog create(array $attributes)
 */
class MachineStatusLog extends Model
{
    use HasFactory;

    public static $isSyncing = false;

    protected $table = 'machine_status_logs';

    protected $fillable = [
        'machine_id',
        'tanggal',
        'status',
        'dmn',
        'dmp',
        'load_value',
        'component',
        'equipment',
        'deskripsi',
        'kronologi',
        'action_plan',
        'progres',
        'tanggal_mulai',
        'target_selesai',
        'unit_source'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_mulai' => 'date',
        'target_selesai' => 'date'
    ];

    protected $dates = [
        'tanggal',
        'tanggal_mulai',
        'target_selesai',
        'created_at',
        'updated_at'
    ];

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function powerPlant()
    {
        return $this->hasOneThrough(
            PowerPlant::class,
            Machine::class,
            'id', 
            'id',
            'machine_id', 
            'power_plant_id' 
        );
    }

    public function machineOperation()
    {
        return $this->hasOne(MachineOperation::class, 'machine_id', 'machine_id')
            ->whereDate('recorded_at', '=', DB::raw('DATE(machine_status_logs.tanggal)'));
    }

    public function getConnectionName()
    {
        return session('unit', 'mysql');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });

        static::created(function ($machineStatus) {
            self::syncData('create', $machineStatus);
        });

        static::updated(function ($machineStatus) {
            self::syncData('update', $machineStatus);
        });

        static::deleted(function ($machineStatus) {
            self::syncData('delete', $machineStatus);
        });

        // Tambahkan method untuk menghapus log status mesin
        public static function deleteMachineLogs($machineId)
        {
            try {
                DB::beginTransaction();
                
                // Dapatkan mesin dan power plant
                $machine = Machine::find($machineId);
                if (!$machine || !$machine->powerPlant) {
                    throw new \Exception('Mesin atau unit pembangkit tidak ditemukan');
                }

                $powerPlant = $machine->powerPlant;
                
                // Hapus di database UP Kendari
                static::where('machine_id', $machineId)->delete();
                
                // Jika bukan di session mysql (UP Kendari), hapus juga di database unit lokal
                if (session('unit', 'mysql') !== 'mysql') {
                    $localConnection = PowerPlant::getConnectionByUnitSource($powerPlant->unit_source);
                    
                    if ($localConnection) {
                        DB::connection($localConnection)
                            ->table('machine_status_logs')
                            ->where('machine_id', $machineId)
                            ->delete();
                    }
                }
                
                DB::commit();
                return true;
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Gagal menghapus log status mesin: " . $e->getMessage());
                throw $e; // Re-throw exception agar dapat ditangkap oleh controller
            }
        }
    }

    // Helper methods yang sudah ada
    public static function getDummyMonthlyData()
    {
        return collect([
            ['month' => 'January', 'count' => 5, 'tanggal' => '2024-01-15'],
            ['month' => 'February', 'count' => 8, 'tanggal' => '2024-02-15'],
            ['month' => 'March', 'count' => 3, 'tanggal' => '2024-03-15'],
            ['month' => 'April', 'count' => 7, 'tanggal' => '2024-04-15'],
            ['month' => 'May', 'count' => 12, 'tanggal' => '2024-05-15'],
            ['month' => 'June', 'count' => 6, 'tanggal' => '2024-06-15'],
            ['month' => 'July', 'count' => 9, 'tanggal' => '2024-07-15'],
            ['month' => 'August', 'count' => 15, 'tanggal' => '2024-08-15'],
            ['month' => 'September', 'count' => 11, 'tanggal' => '2024-09-15'],
            ['month' => 'October', 'count' => 4, 'tanggal' => '2024-10-15'],
            ['month' => 'November', 'count' => 7, 'tanggal' => '2024-11-15'],
            ['month' => 'December', 'count' => 10, 'tanggal' => '2024-12-15']
        ]);
    }

    public static function getDummyActiveIssues()
    {
        return 15;
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('H:i:s d/m/Y') : 'N/A';
    }

    public function isActiveIssue()
    {
        if ($this->status !== 'Gangguan') {
            return false;
        }
        return !$this->target_selesai || Carbon::now()->lte($this->target_selesai);
    }

    public function hasNewerUpdate()
    {
        return static::where('machine_id', $this->machine_id)
            ->where('created_at', '>', $this->created_at)
            ->whereBetween('created_at', [$this->tanggal_mulai, $this->target_selesai])
            ->exists();
    }

    public static function getChartData($powerPlantId)
    {
        $lastWeek = Carbon::now()->subWeek();
        $today = Carbon::now();

        \Log::info('Getting chart data for power plant:', [
            'powerPlantId' => $powerPlantId,
            'dateRange' => [$lastWeek, $today]
        ]);

        $data = static::query()
            ->join('machines', 'machines.id', '=', 'machine_status_logs.machine_id')
            ->where('machines.power_plant_id', $powerPlantId)
            ->whereBetween('tanggal', [$lastWeek, $today])
            ->select(
                DB::raw('DATE(tanggal) as date'),
                DB::raw('AVG(load_value) as avg_load'),
                DB::raw('SUM(machines.capacity) as total_capacity')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        \Log::info('Chart data retrieved:', ['data' => $data]);

        return $data->map(function ($item) {
            return [
                'date' => Carbon::parse($item->date)->format('D'),
                'load' => round($item->avg_load, 2),
                'capacity' => round($item->total_capacity, 2)
            ];
        });
    }

    public static function getUnservedLoadData($powerPlantId, $startDate, $endDate)
    {
        \Log::info("Mengambil data beban tak tersalur untuk pembangkit ID: $powerPlantId", [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $data = static::query()
            ->join('machines', 'machines.id', '=', 'machine_status_logs.machine_id')
            ->where('machines.power_plant_id', $powerPlantId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereIn('status', ['Gangguan', 'Mothballed', 'Overhaul'])
            ->select(
                'machine_status_logs.id',
                'machine_status_logs.tanggal',
                'machine_status_logs.status',
                'machine_status_logs.dmn',
                'machine_status_logs.dmp',
                'machines.capacity',
                'machines.name as machine_name'
            )
            ->orderBy('tanggal')
            ->get()
            ->map(function ($log) {
                // Jika DMP = 0, gunakan DMN atau capacity sebagai fallback
                $unservedLoad = $log->dmp > 0 ? $log->dmp : ($log->dmn > 0 ? $log->dmn : $log->capacity);
                return [
                    'tanggal' => $log->tanggal,
                    'status' => $log->status,
                    'unserved_load' => floatval($unservedLoad),
                    'machine_name' => $log->machine_name
                ];
            });

        \Log::info("Data beban tak tersalur ditemukan:", [
            'count' => $data->count(),
            'data' => $data->toArray()
        ]);
        
        return $data;
    }

    // Fungsi untuk mendapatkan power plant dari machine status log
    public function getPowerPlant()
    {
        return $this->machine->powerPlant;
    }

    /**
     * Track sync status and attempts
     */
    protected static $syncAttempts = [];
    protected static $lastSyncTime = null;
    protected static $syncErrors = [];

    /**
     * Get sync statistics
     */
    public static function getSyncStats()
    {
        return [
            'attempts' => self::$syncAttempts,
            'last_sync' => self::$lastSyncTime,
            'errors' => self::$syncErrors
        ];
    }

    /**
     * Record sync attempt
     */
    protected static function recordSyncAttempt($action, $machineStatus, $success = true, $error = null)
    {
        $timestamp = now();
        self::$lastSyncTime = $timestamp;
        
        $attempt = [
            'timestamp' => $timestamp,
            'action' => $action,
            'machine_status_id' => $machineStatus->id,
            'success' => $success,
            'error' => $error
        ];

        self::$syncAttempts[] = $attempt;

        // Keep only last 100 attempts
        if (count(self::$syncAttempts) > 100) {
            array_shift(self::$syncAttempts);
        }

        if (!$success && $error) {
            self::$syncErrors[] = [
                'timestamp' => $timestamp,
                'error' => $error
            ];

            // Keep only last 50 errors
            if (count(self::$syncErrors) > 50) {
                array_shift(self::$syncErrors);
            }
        }
    }

    /**
     * Verify sync success
     */
    protected static function verifySyncSuccess($action, $machineStatus, $targetDB)
    {
        try {
            $record = $targetDB->table('machine_status_logs')
                              ->where('id', $machineStatus->id)
                              ->first();

            switch($action) {
                case 'create':
                case 'update':
                    return !empty($record);
                case 'delete':
                    return empty($record);
                default:
                    return false;
            }
        } catch (\Exception $e) {
            Log::warning("Sync verification failed", [
                'action' => $action,
                'id' => $machineStatus->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Handle failed sync
     */
    protected static function handleSyncFailure($action, $machineStatus, $error)
    {
        $errorMessage = $error->getMessage();
        self::recordSyncAttempt($action, $machineStatus, false, $errorMessage);

        // Kirim notifikasi jika diperlukan
        if (config('app.env') === 'production') {
            Log::error("Critical sync failure", [
                'action' => $action,
                'machine_status_id' => $machineStatus->id,
                'error' => $errorMessage
            ]);
            
            // Tambahkan logika notifikasi di sini jika diperlukan
        }
    }

    /**
     * Enhanced logging for sync process
     */
    protected static function logSyncProcess($stage, $data)
    {
        $sessionId = uniqid('sync_');
        $currentSession = session('unit', 'mysql');
        
        $logData = array_merge([
            'sync_id' => $sessionId,
            'timestamp' => now()->toDateTimeString(),
            'stage' => $stage,
            'current_session' => $currentSession,
        ], $data);

        Log::channel('sync')->info("Sync Process: {$stage}", $logData);
        
        return $sessionId;
    }

    /**
     * Debug sync process
     */
    protected static function debugSync($sessionId, $message, $data = [])
    {
        if (config('app.debug')) {
            $debugData = array_merge([
                'sync_id' => $sessionId,
                'timestamp' => now()->toDateTimeString(),
                'memory_usage' => memory_get_usage(true),
            ], $data);

            Log::channel('sync')->debug("Sync Debug: {$message}", $debugData);
        }
    }

    protected static function syncData($action, $machineStatus)
    {
        $sessionId = self::logSyncProcess('start', [
            'action' => $action,
            'machine_status_id' => $machineStatus->id
        ]);

        try {
            // Validasi data
            self::validateSyncData($machineStatus);
            self::debugSync($sessionId, 'Validation passed');
            
            // Dapatkan power plant
            $powerPlant = $machineStatus->getPowerPlant();
            self::debugSync($sessionId, 'Power plant retrieved', [
                'power_plant_id' => $powerPlant->id,
                'unit_source' => $powerPlant->unit_source
            ]);
            
            // Cek apakah perlu sync
            if (!self::shouldSync($powerPlant)) {
                self::logSyncProcess('skipped', [
                    'sync_id' => $sessionId,
                    'reason' => 'Sync not needed'
                ]);
                return;
            }
            
            self::$isSyncing = true;
            
            // Siapkan data
            $data = self::prepareSyncData($machineStatus, $powerPlant);
            self::debugSync($sessionId, 'Data prepared', [
                'prepared_data' => $data
            ]);
            
            // Tentukan target database
            $currentSession = session('unit', 'mysql');
            
            if ($currentSession === 'mysql') {
                $targetConnection = PowerPlant::getConnectionByUnitSource($powerPlant->unit_source);
                self::debugSync($sessionId, 'Syncing from UP Kendari to unit local', [
                    'target_connection' => $targetConnection
                ]);
            } else {
                $targetConnection = 'mysql';
                $data['unit_source'] = $currentSession;
                self::debugSync($sessionId, 'Syncing from unit local to UP Kendari', [
                    'source_unit' => $currentSession
                ]);
            }

            $targetDB = DB::connection($targetConnection);

            self::logSyncProcess('executing', [
                'sync_id' => $sessionId,
                'data' => $data,
                'current_session' => $currentSession,
                'target_connection' => $targetConnection,
                'power_plant_unit' => $powerPlant->unit_source
            ]);

            // Lakukan operasi database dengan transaction
            DB::beginTransaction();
            try {
                switch($action) {
                    case 'create':
                        $targetDB->table('machine_status_logs')->insert($data);
                        self::debugSync($sessionId, 'Insert operation completed');
                        break;
                        
                    case 'update':
                        // Log data sebelum update
                        $oldData = $targetDB->table('machine_status_logs')
                                          ->where('id', $machineStatus->id)
                                          ->first();
                        self::debugSync($sessionId, 'Previous data state', [
                            'old_data' => $oldData
                        ]);
                        
                        $targetDB->table('machine_status_logs')
                                ->where('id', $machineStatus->id)
                                ->update($data);
                        self::debugSync($sessionId, 'Update operation completed');
                        break;
                        
                    case 'delete':
                        $targetDB->table('machine_status_logs')
                                ->where('id', $machineStatus->id)
                                ->delete();
                        self::debugSync($sessionId, 'Delete operation completed');
                        break;
                }
                DB::commit();
                self::debugSync($sessionId, 'Transaction committed');
                
            } catch (\Exception $e) {
                DB::rollBack();
                self::debugSync($sessionId, 'Transaction rolled back', [
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }

            // Verifikasi sync berhasil
            if (self::verifySyncSuccess($action, $machineStatus, $targetDB)) {
                self::recordSyncAttempt($action, $machineStatus, true);
                
                self::logSyncProcess('success', [
                    'sync_id' => $sessionId,
                    'id' => $machineStatus->id,
                    'from_session' => $currentSession,
                    'to_connection' => $targetConnection,
                    'unit_source' => $data['unit_source']
                ]);
            } else {
                throw new \Exception("Sync verification failed");
            }

        } catch (\Exception $e) {
            self::handleSyncFailure($action, $machineStatus, $e);
            self::logSyncProcess('failed', [
                'sync_id' => $sessionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
            self::logSyncProcess('end', [
                'sync_id' => $sessionId,
                'duration' => now()->diffInMilliseconds(now())
            ]);
        }
    }

    /**
     * Validate sync data before processing
     */
    protected static function validateSyncData($machineStatus)
    {
        if (!$machineStatus->machine_id) {
            throw new \Exception('Machine ID is required for sync');
        }

        if (!$machineStatus->tanggal) {
            throw new \Exception('Date is required for sync');
        }

        $powerPlant = $machineStatus->getPowerPlant();
        if (!$powerPlant) {
            throw new \Exception('Power Plant not found');
        }

        if (!$powerPlant->unit_source) {
            throw new \Exception('Unit source is not defined for Power Plant');
        }

        return true;
    }

    /**
     * Check if sync is needed for the current operation
     */
    protected static function shouldSync($powerPlant)
    {
        // Jika sedang dalam proses sync, skip
        if (self::$isSyncing) {
            return false;
        }

        // Jika tidak ada power plant atau unit source, skip
        if (!$powerPlant || !$powerPlant->unit_source) {
            return false;
        }

        $currentSession = session('unit', 'mysql');

        // Sync diperlukan dalam dua kasus:
        // 1. Jika operasi dari UP Kendari (mysql) ke unit lokal
        // 2. Jika operasi dari unit lokal ke UP Kendari
        return ($currentSession === 'mysql' && $powerPlant->unit_source !== 'mysql') ||
               ($currentSession !== 'mysql' && $powerPlant->unit_source === $currentSession);
    }

    /**
     * Get target database connection for sync
     */
    protected static function getTargetConnection($powerPlant)
    {
        if (session('unit') === 'mysql') {
            // Dari UP Kendari ke unit lokal
            return PowerPlant::getConnectionByUnitSource($powerPlant->unit_source);
        } else {
            // Dari unit lokal ke UP Kendari
            return 'mysql';
        }
    }

    /**
     * Prepare data for sync
     */
    protected static function prepareSyncData($machineStatus, $powerPlant)
    {
        return [
            'id' => $machineStatus->id,
            'machine_id' => $machineStatus->machine_id,
            'tanggal' => $machineStatus->tanggal,
            'status' => $machineStatus->status,
            'dmn' => $machineStatus->dmn,
            'dmp' => $machineStatus->dmp,
            'load_value' => $machineStatus->load_value,
            'component' => $machineStatus->component,
            'equipment' => $machineStatus->equipment,
            'deskripsi' => $machineStatus->deskripsi,
            'kronologi' => $machineStatus->kronologi,
            'action_plan' => $machineStatus->action_plan,
            'progres' => $machineStatus->progres,
            'tanggal_mulai' => $machineStatus->tanggal_mulai,
            'target_selesai' => $machineStatus->target_selesai,
            'unit_source' => $powerPlant->unit_source,
            'created_at' => $machineStatus->created_at,
            'updated_at' => $machineStatus->updated_at
        ];
    }
} 