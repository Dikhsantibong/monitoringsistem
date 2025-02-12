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

    // Mapping untuk koneksi database
    protected static $connectionMapping = [
        'mysql_bau_bau' => 'u478221055_ulpltd_bau_bau',
        'mysql_kolaka' => 'u478221055_ulpltd_kolaka',
        'mysql_poasia' => 'u478221055_ulpltd_poasia',
        'mysql_wua_wua' => 'u478221055_ulpltd_wua_wua',
        'mysql' => 'u478221055_up_kendari'
    ];

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
        $unitSession = session('unit', 'mysql');
        
        \Log::debug('Current Connection Details', [
            'session_unit' => $unitSession,
            'mapped_database' => self::$connectionMapping[$unitSession] ?? 'u478221055_up_kendari'
        ]);

        return $unitSession;
    }

    public static function getDatabaseName()
    {
        $unitSession = session('unit', 'mysql');
        return self::$connectionMapping[$unitSession] ?? 'u478221055_up_kendari';
    }

    public function getTimeAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Makassar');
    }

    public static function getUnitConnection()
    {
        $unitSession = session('unit', 'mysql');
        \Log::debug('Getting Unit Connection', [
            'session' => $unitSession,
            'database' => self::$connectionMapping[$unitSession] ?? 'u478221055_up_kendari'
        ]);
        return $unitSession;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->setConnection(self::getUnitConnection());
        });
        
        static::saving(function ($model) {
            $model->setConnection(self::getUnitConnection());
        });
    }
}
