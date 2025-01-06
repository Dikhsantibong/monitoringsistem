<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyQrCode extends Model
{
    public static $isSyncing = false;

    protected $fillable = [
        'code', 
        'valid_date', 
        'is_active',
        'unit_source'
    ];

    protected $casts = [
        'valid_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getConnectionName()
    {
        return session('unit', 'u478221055_up_kendari');
    }

    protected static function boot()
    {
        parent::boot();
        
        // Handle Created Event
        static::created(function ($qrCode) {
            self::syncToUpKendari('create', $qrCode);
        });

        // Handle Updated Event
        static::updated(function ($qrCode) {
            self::syncToUpKendari('update', $qrCode);
        });

        // Handle Deleted Event
        static::deleted(function ($qrCode) {
            self::syncToUpKendari('delete', $qrCode);
        });
    }

    protected static function syncToUpKendari($action, $qrCode)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $data = [
                'id' => $qrCode->id,
                'code' => $qrCode->code,
                'valid_date' => $qrCode->valid_date,
                'is_active' => $qrCode->is_active,
                'unit_source' => 'poasia',
                'created_at' => $qrCode->created_at,
                'updated_at' => $qrCode->updated_at
            ];

            Log::info("Attempting to {$action} Daily QR Code sync", ['data' => $data]);

            $upKendari = DB::connection('mysql')->table('daily_qr_codes');

            switch($action) {
                case 'create':
                    $upKendari->insert($data);
                    break;
                    
                case 'update':
                    $upKendari->where('id', $qrCode->id)
                             ->update($data);
                    break;
                    
                case 'delete':
                    $upKendari->where('id', $qrCode->id)
                             ->delete();
                    break;
            }

            Log::info("Daily QR Code {$action} sync successful", [
                'id' => $qrCode->id,
                'unit' => 'poasia'
            ]);

        } catch (\Exception $e) {
            Log::error("Daily QR Code {$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
        }
    }
} 