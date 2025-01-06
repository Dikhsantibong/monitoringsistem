<?php

namespace App\Observers;

use App\Models\ServiceRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceRequestObserver
{
    public function created(ServiceRequest $serviceRequest)
    {
        // Skip jika sedang sinkronisasi
        if (ServiceRequest::$isSyncing) {
            return;
        }

        try {
            ServiceRequest::$isSyncing = true;
            
            // Konfigurasi koneksi UP Kendari
            $upKendariConfig = [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => 'u478221055_up_kendari', // Langsung specify database
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
            ];

            // Buat koneksi baru
            config(['database.connections.up_kendari' => $upKendariConfig]);
            
            Log::info('Attempting to sync', [
                'data' => [
                    'id' => $serviceRequest->id,
                    'description' => $serviceRequest->description,
                    'status' => $serviceRequest->status,
                    'downtime' => $serviceRequest->downtime,
                    'tipe_sr' => $serviceRequest->tipe_sr,
                    'priority' => $serviceRequest->priority,
                    'unit_source' => 'poasia', // Hardcode unit untuk testing
                    'created_at' => $serviceRequest->created_at,
                    'updated_at' => $serviceRequest->updated_at
                ]
            ]);

            // Insert ke UP Kendari menggunakan koneksi baru
            DB::connection('up_kendari')->table('service_requests')->insert([
                'id' => $serviceRequest->id,
                'description' => $serviceRequest->description,
                'status' => $serviceRequest->status,
                'downtime' => $serviceRequest->downtime,
                'tipe_sr' => $serviceRequest->tipe_sr,
                'priority' => $serviceRequest->priority,
                'unit_source' => 'poasia', // Hardcode unit untuk testing
                'created_at' => $serviceRequest->created_at,
                'updated_at' => $serviceRequest->updated_at
            ]);

            Log::info('Sync successful', [
                'id' => $serviceRequest->id,
                'unit' => 'poasia'
            ]);

        } catch (\Exception $e) {
            Log::error('Sync failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            ServiceRequest::$isSyncing = false;
        }
    }

    public function updated(ServiceRequest $serviceRequest)
    {
        if (ServiceRequest::$isSyncing || session('unit') === 'u478221055_up_kendari') {
            return;
        }

        try {
            ServiceRequest::$isSyncing = true;
            
            DB::connection('u478221055_up_kendari')->table('service_requests')
                ->where('id', $serviceRequest->id)
                ->update([
                    'description' => $serviceRequest->description,
                    'status' => $serviceRequest->status,
                    'downtime' => $serviceRequest->downtime,
                    'tipe_sr' => $serviceRequest->tipe_sr,
                    'priority' => $serviceRequest->priority,
                    'updated_at' => $serviceRequest->updated_at
                ]);

            Log::channel('sync')->info('Service Request updated in UP Kendari', [
                'id' => $serviceRequest->id,
                'unit' => session('unit'),
                'action' => 'updated'
            ]);
        } catch (\Exception $e) {
            Log::channel('sync')->error('Sync Error', [
                'message' => $e->getMessage(),
                'unit' => session('unit'),
                'id' => $serviceRequest->id
            ]);
        } finally {
            ServiceRequest::$isSyncing = false;
        }
    }

    public function deleted(ServiceRequest $serviceRequest)
    {
        if (ServiceRequest::$isSyncing || session('unit') === 'u478221055_up_kendari') {
            return;
        }

        try {
            ServiceRequest::$isSyncing = true;
            
            DB::connection('u478221055_up_kendari')->table('service_requests')
                ->where('id', $serviceRequest->id)
                ->delete();

            Log::channel('sync')->info('Service Request deleted from UP Kendari', [
                'id' => $serviceRequest->id,
                'unit' => session('unit'),
                'action' => 'deleted'
            ]);
        } catch (\Exception $e) {
            Log::channel('sync')->error('Sync Error', [
                'message' => $e->getMessage(),
                'unit' => session('unit'),
                'id' => $serviceRequest->id
            ]);
        } finally {
            ServiceRequest::$isSyncing = false;
        }
    }
} 