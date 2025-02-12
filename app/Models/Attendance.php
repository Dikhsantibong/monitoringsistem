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
        'id',
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

    // Mapping session ke database yang benar
    private static $databaseMapping = [
        'mysql_bau_bau' => 'u478221055_ulpltd_bau_bau',
        'mysql_kolaka' => 'u478221055_ulpltd_kolaka',
        'mysql_poasia' => 'u478221055_ulpltd_poasia',
        'mysql_wua_wua' => 'u478221055_ulpltd_wua_wua',
        'mysql' => 'u478221055_up_kendari'
    ];

    // Override koneksi database berdasarkan session unit
    protected $connection = null;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // Set koneksi berdasarkan session unit
        $currentUnit = session('unit', 'mysql');
        $this->connection = $currentUnit;
        
        // Set unit_source sesuai dengan koneksi yang aktif
        $this->attributes['unit_source'] = $currentUnit;
        
        Log::debug('Attendance Model Connection Check', [
            'session_unit' => $currentUnit,
            'connection' => $this->connection,
            'database_name' => self::$databaseMapping[$currentUnit] ?? 'unknown',
            'unit_source' => $this->attributes['unit_source'] ?? null
        ]);
    }

    // Override method getConnectionName untuk memastikan koneksi yang benar
    public function getConnectionName()
    {
        return session('unit', 'mysql');
    }

    public static function getCurrentDatabase()
    {
        $currentUnit = session('unit', 'mysql');
        return self::$databaseMapping[$currentUnit] ?? 'u478221055_up_kendari';
    }

    public function getTimeAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Makassar');
    }

    // Override method save untuk memastikan data tersimpan di koneksi yang benar
    public function save(array $options = [])
    {
        $currentUnit = session('unit', 'mysql');
        $this->connection = $currentUnit;
        
        if (!isset($this->attributes['unit_source'])) {
            $this->attributes['unit_source'] = $currentUnit;
        }
        
        Log::debug('Saving Attendance Check', [
            'session_unit' => $currentUnit,
            'connection' => $this->connection,
            'database_name' => self::$databaseMapping[$currentUnit],
            'unit_source' => $this->attributes['unit_source']
        ]);

        return parent::save($options);
    }
}
