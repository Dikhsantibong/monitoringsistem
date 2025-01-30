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

    protected static function syncData($action, $hop)
    {
        $sessionId = self::logSyncProcess('start', [
            'action' => $action,
            'hop_id' => $hop->id
        ]);

        try {
            if (self::$isSyncing) {
                self::debugSync($sessionId, 'Sync already in progress, skipping');
                return;
            }

            self::$isSyncing = true;
            
            // Dapatkan power plant
            $powerPlant = $hop->powerPlant;
            if (!$powerPlant) {
                throw new \Exception('Power Plant not found');
            }

            self::debugSync($sessionId, 'Power plant retrieved', [
                'power_plant_id' => $powerPlant->id,
                'unit_source' => $powerPlant->unit_source
            ]);

            // Siapkan data untuk sinkronisasi
            $data = [
                'id' => $hop->id,
                'power_plant_id' => $hop->power_plant_id,
                'tanggal' => $hop->tanggal,
                'hop_value' => $hop->hop_value,
                'keterangan' => $hop->keterangan,
                'unit_source' => $powerPlant->unit_source,
                'created_at' => $hop->created_at,
                'updated_at' => $hop->updated_at
            ];

            self::debugSync($sessionId, 'Data prepared', ['data' => $data]);

            // Tentukan target database berdasarkan session
            $currentSession = session('unit', 'mysql');
            
            if ($currentSession === 'mysql') {
                $targetConnection = PowerPlant::getConnectionByUnitSource($powerPlant->unit_source);
                self::debugSync($sessionId, 'Syncing from UP Kendari to unit local', [
                    'target_connection' => $targetConnection
                ]);
            } else {
                $targetConnection = 'mysql';
                self::debugSync($sessionId, 'Syncing from unit local to UP Kendari');
            }

            $targetDB = DB::connection($targetConnection);

            // Begin transaction
            DB::beginTransaction();
            
            try {
                switch($action) {
                    case 'create':
                        $targetDB->table('unit_operation_hours')->insert($data);
                        self::debugSync($sessionId, 'Insert operation completed');
                        break;
                        
                    case 'update':
                        $oldData = $targetDB->table('unit_operation_hours')
                                          ->where('id', $hop->id)
                                          ->first();
                        self::debugSync($sessionId, 'Previous data state', [
                            'old_data' => $oldData
                        ]);
                        
                        $targetDB->table('unit_operation_hours')
                                ->where('id', $hop->id)
                                ->update($data);
                        self::debugSync($sessionId, 'Update operation completed');
                        break;
                        
                    case 'delete':
                        $targetDB->table('unit_operation_hours')
                                ->where('id', $hop->id)
                                ->delete();
                        self::debugSync($sessionId, 'Delete operation completed');
                        break;
                }

                DB::commit();
                self::debugSync($sessionId, 'Transaction committed');

                if (self::verifySyncSuccess($action, $hop, $targetDB)) {
                    self::recordSyncAttempt($action, $hop, true);
                    self::logSyncProcess('success', [
                        'sync_id' => $sessionId,
                        'hop_id' => $hop->id,
                        'target_connection' => $targetConnection
                    ]);
                } else {
                    throw new \Exception("Sync verification failed");
                }

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            self::recordSyncAttempt($action, $hop, false, $e->getMessage());
            self::logSyncProcess('failed', [
                'sync_id' => $sessionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            Log::error("HOP {$action} sync failed", [
                'message' => $e->getMessage(),
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

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }
}
