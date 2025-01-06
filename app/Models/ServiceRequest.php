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
        'tipe_sr', 'priority', 'unit_source'
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
        
        // Handle Created Event
        static::created(function ($serviceRequest) {
            self::syncToUpKendari('create', $serviceRequest);
        });

        // Handle Updated Event
        static::updated(function ($serviceRequest) {
            self::syncToUpKendari('update', $serviceRequest);
        });

        // Handle Deleted Event
        static::deleted(function ($serviceRequest) {
            self::syncToUpKendari('delete', $serviceRequest);
        });
    }

    protected static function syncToUpKendari($action, $serviceRequest)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $data = [
                'id' => $serviceRequest->id,
                'description' => $serviceRequest->description,
                'status' => $serviceRequest->status,
                'downtime' => $serviceRequest->downtime,
                'tipe_sr' => $serviceRequest->tipe_sr,
                'priority' => $serviceRequest->priority,
                'unit_source' => session('unit'),
                'created_at' => $serviceRequest->created_at,
                'updated_at' => $serviceRequest->updated_at
            ];

            Log::info("Attempting to {$action} sync", ['data' => $data]);

            $upKendari = DB::connection('mysql')->table('service_requests');

            switch($action) {
                case 'create':
                    $upKendari->insert($data);
                    break;
                    
                case 'update':
                    $upKendari->where('id', $serviceRequest->id)
                             ->update($data);
                    break;
                    
                case 'delete':
                    $upKendari->where('id', $serviceRequest->id)
                             ->delete();
                    break;
            }

            Log::info("{$action} sync successful", [
                'id' => $serviceRequest->id,
                'unit' => 'poasia'
            ]);

        } catch (\Exception $e) {
            Log::error("{$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
        }
    }
}   