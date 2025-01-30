<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnitOperationHour extends Model
{
    protected $table = 'unit_operation_hours';
    public static $isSyncing = false;
    protected static $syncAttempts = [];
    protected static $lastSyncTime = null;
    protected static $syncErrors = [];
    
    protected $fillable = [
        'power_plant_id',
        'tanggal',
        'hop_value',
        'keterangan',
        'unit_source'
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];

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
    protected static function recordSyncAttempt($action, $hop, $success = true, $error = null)
    {
        $timestamp = now();
        self::$lastSyncTime = $timestamp;
        
        $attempt = [
            'timestamp' => $timestamp,
            'action' => $action,
            'hop_id' => $hop->id,
            'power_plant_id' => $hop->power_plant_id,
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
     * Enhanced logging for sync process
     */
    protected static function logSyncProcess($stage, $data)
    {
        $sessionId = uniqid('hop_sync_');
        $currentSession = session('unit', 'mysql');
        
        $logData = array_merge([
            'sync_id' => $sessionId,
            'timestamp' => now()->toDateTimeString(),
            'stage' => $stage,
            'current_session' => $currentSession,
        ], $data);

        Log::channel('sync')->info("HOP Sync Process: {$stage}", $logData);
        
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

            Log::channel('sync')->debug("HOP Sync Debug: {$message}", $debugData);
        }
    }

    /**
     * Verify sync success
     */
    protected static function verifySyncSuccess($action, $hop, $targetDB)
    {
        try {
            $record = $targetDB->table('unit_operation_hours')
                              ->where('id', $hop->id)
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
            Log::warning("HOP Sync verification failed", [
                'action' => $action,
                'id' => $hop->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($hop) {
            try {
                Log::info("HOP Created Event Triggered", [
                    'hop_id' => $hop->id,
                    'power_plant_id' => $hop->power_plant_id,
                    'session' => session('unit')
                ]);
                
                self::syncData('create', $hop);
            } catch (\Exception $e) {
                Log::error("Error in HOP created event", [
                    'error' => $e->getMessage(),
                    'hop_id' => $hop->id
                ]);
            }
        });

        static::updated(function ($hop) {
            try {
                Log::info("HOP Updated Event Triggered", [
                    'hop_id' => $hop->id,
                    'power_plant_id' => $hop->power_plant_id,
                    'session' => session('unit')
                ]);
                
                self::syncData('update', $hop);
            } catch (\Exception $e) {
                Log::error("Error in HOP updated event", [
                    'error' => $e->getMessage(),
                    'hop_id' => $hop->id
                ]);
            }
        });

        static::deleted(function ($hop) {
            try {
                Log::info("HOP Deleted Event Triggered", [
                    'hop_id' => $hop->id,
                    'power_plant_id' => $hop->power_plant_id,
                    'session' => session('unit')
                ]);
                
                self::syncData('delete', $hop);
            } catch (\Exception $e) {
                Log::error("Error in HOP deleted event", [
                    'error' => $e->getMessage(),
                    'hop_id' => $hop->id
                ]);
            }
        });
    }

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

    protected static function syncData($action, $hop)
    {
        $sessionId = uniqid('sync_');
        
        try {
            if (self::$isSyncing) {
                return;
            }

            self::$isSyncing = true;
            
            $powerPlant = $hop->powerPlant;
            if (!$powerPlant) {
                throw new \Exception('Power Plant not found');
            }

            if (!self::shouldSync($powerPlant)) {
                return;
            }

            // Tentukan target connection
            $currentSession = session('unit', 'mysql');
            if ($currentSession === 'mysql') {
                // Dari UP Kendari ke unit lokal
                $targetConnection = PowerPlant::getConnectionByUnitSource($powerPlant->unit_source);
            } else {
                // Dari unit lokal ke UP Kendari
                $targetConnection = 'mysql';
            }

            if (!$targetConnection) {
                throw new \Exception("Invalid target connection");
            }

            DB::beginTransaction();

            try {
                $targetDB = DB::connection($targetConnection);

                $data = [
                    'power_plant_id' => $hop->power_plant_id,
                    'tanggal' => $hop->tanggal,
                    'hop_value' => $hop->hop_value,
                    'keterangan' => $hop->keterangan,
                    'unit_source' => $currentSession === 'mysql' ? $powerPlant->unit_source : $currentSession,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Cek existing record
                $existingRecord = $targetDB->table('unit_operation_hours')
                    ->where('power_plant_id', $hop->power_plant_id)
                    ->whereDate('tanggal', $hop->tanggal)
                    ->first();

                if ($existingRecord) {
                    $targetDB->table('unit_operation_hours')
                            ->where('id', $existingRecord->id)
                            ->update($data);
                } else {
                    $targetDB->table('unit_operation_hours')->insert($data);
                }

                DB::commit();
                
                Log::info("HOP sync successful", [
                    'action' => $action,
                    'hop_id' => $hop->id,
                    'target_connection' => $targetConnection,
                    'unit_source' => $data['unit_source']
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error("HOP sync failed", [
                'message' => $e->getMessage(),
                'hop_id' => $hop->id ?? null,
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
}



