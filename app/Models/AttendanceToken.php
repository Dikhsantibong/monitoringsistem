<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceToken extends Model
{
    use HasFactory;

    public static $isSyncing = false;

    protected $table = 'attendance_tokens';

    protected $fillable = [
        'id',
        'token',
        'user_id',
        'expires_at',
        'unit_source'

    ];

    protected $dates = ['expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getConnectionName()
    {
        return session('unit', 'u478221055_up_kendari');
    }

    protected static function boot()
    {
        parent::boot();
        
        // Handle Created Event
        static::created(function ($attendanceToken) {
            self::syncToUpKendari('create', $attendanceToken);
        });

        // Handle Updated Event
        static::updated(function ($attendanceToken) {
            self::syncToUpKendari('update', $attendanceToken);
        });

        // Handle Deleted Event
        static::deleted(function ($attendanceToken) {
            self::syncToUpKendari('delete', $attendanceToken);
        });
    }

    protected static function syncToUpKendari($action, $attendanceToken)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $data = [
                'id' => $attendanceToken->id,
                'token' => $attendanceToken->token,
                'expires_at' => $attendanceToken->expires_at,
                'user_id' => $attendanceToken->user_id,
                'unit_source' => session('unit'),
                'created_at' => $attendanceToken->created_at,
                'updated_at' => $attendanceToken->updated_at
            ];

            Log::info("Attempting to {$action} Attendance Token sync", ['data' => $data]);

            $upKendari = DB::connection('mysql')->table('attendance_tokens');

            switch($action) {
                case 'create':
                    $upKendari->insert($data);
                    break;
                    
                case 'update':
                    $upKendari->where('id', $attendanceToken->id)
                             ->update($data);
                    break;
                    
                case 'delete':
                    $upKendari->where('id', $attendanceToken->id)
                             ->delete();
                    break;
            }

            Log::info("Attendance Token {$action} sync successful", [
                'id' => $attendanceToken->id,
                'unit' => 'poasia'
            ]);

        } catch (\Exception $e) {
            Log::error("Attendance Token {$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
        }
    }
} 