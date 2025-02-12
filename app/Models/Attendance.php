<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    public static $isSyncing = false;

    protected $table = 'attendance';

    protected $fillable = [
        'name',
        'position',
        'division',
        'token',
        'time',
        'signature',
        'unit_source'
    ];

    protected $dates = ['time'];

    protected $casts = [
        'time' => 'datetime',
    ];

    public function getConnectionName()
    {
        // Pastikan nilai default adalah yang sesuai dengan konfigurasi
        $unit = session('unit');
        Log::info('Current database connection', ['unit' => $unit]);
        return $unit ?? 'mysql';
    }

    protected static function boot()
    {
        parent::boot();
        
        // Semua event listener dinonaktifkan dengan menghapus atau memberi komentar pada kode di bawah
        /*
        // Handle Created Event
        static::created(function ($attendance) {
            self::syncToUpKendari('create', $attendance);
        });

        // Handle Updated Event
        static::updated(function ($attendance) {
            self::syncToUpKendari('update', $attendance);
        });

        // Handle Deleted Event
        static::deleted(function ($attendance) {
            self::syncToUpKendari('delete', $attendance);
        });
        */
    }

    protected static function syncToUpKendari($action, $attendance)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $data = [
                'id' => $attendance->id,
                'name' => $attendance->name,
                'position' => $attendance->position,
                'division' => $attendance->division,
                'token' => $attendance->token,
                'time' => $attendance->time,
                'signature' => $attendance->signature,
                'unit_source' => 'poasia',
                'created_at' => $attendance->created_at,
                'updated_at' => $attendance->updated_at
            ];

            Log::info("Attempting to {$action} Attendance sync", ['data' => $data]);

            $upKendari = DB::connection('mysql')->table('attendance');

            switch($action) {
                case 'create':
                    $upKendari->insert($data);
                    break;
                    
                case 'update':
                    $upKendari->where('id', $attendance->id)
                             ->update($data);
                    break;
                    
                case 'delete':
                    $upKendari->where('id', $attendance->id)
                             ->delete();
                    break;
            }

            Log::info("Attendance {$action} sync successful", [
                'id' => $attendance->id,
                'unit' => 'poasia'
            ]);

        } catch (\Exception $e) {
            Log::error("Attendance {$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
        }
    }

    public function getTimeAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Makassar');
    }
}