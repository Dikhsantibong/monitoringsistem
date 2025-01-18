<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceRequest extends Model
{
    public static $isSyncing = false;
    
    protected $fillable = [
        'id', 'description', 'status', 'created_at', 'downtime', 
        'tipe_sr', 'priority', 'unit_source', 'power_plant_id'
    ];

    public $incrementing = false;

    // Gunakan connection sesuai session unit
    public function getConnectionName()
    {
        return session('unit');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($serviceRequest) {
            self::syncData('create', $serviceRequest);
        });

        static::updated(function ($serviceRequest) {
            self::syncData('update', $serviceRequest);
        });

        static::deleted(function ($serviceRequest) {
            self::syncData('delete', $serviceRequest);
        });
    }

    protected static function syncData($action, $serviceRequest)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $powerPlant = PowerPlant::find($serviceRequest->power_plant_id);
            if (!$powerPlant) {
                throw new \Exception('Power Plant not found');
            }

            $data = [
                'id' => $serviceRequest->id,
                'description' => $serviceRequest->description,
                'status' => $serviceRequest->status,
                'downtime' => $serviceRequest->downtime,
                'tipe_sr' => $serviceRequest->tipe_sr,
                'priority' => $serviceRequest->priority,
                'power_plant_id' => $serviceRequest->power_plant_id,
                'unit_source' => $powerPlant->unit_source,
                'created_at' => $serviceRequest->created_at,
                'updated_at' => $serviceRequest->updated_at
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

            Log::info("Attempting to {$action} sync", ['data' => $data]);

            switch($action) {
                case 'create':
                    $targetDB->table('service_requests')->insert($data);
                    break;
                    
                case 'update':
                    $targetDB->table('service_requests')
                            ->where('id', $serviceRequest->id)
                            ->update($data);
                    break;
                    
                case 'delete':
                    $targetDB->table('service_requests')
                            ->where('id', $serviceRequest->id)
                            ->delete();
                    break;
            }

            Log::info("Sync successful", [
                'id' => $serviceRequest->id,
                'unit' => $powerPlant->unit_source
            ]);

        } catch (\Exception $e) {
            Log::error("Sync failed", [
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
}   