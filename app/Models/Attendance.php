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

    // Override koneksi database berdasarkan session unit
    protected $connection = null;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // Set koneksi berdasarkan session unit
        $this->connection = session('unit', 'mysql');
        
        // Set unit_source sesuai dengan koneksi yang aktif
        $this->attributes['unit_source'] = $this->connection;
        
        Log::debug('Attendance Model Connection', [
            'connection' => $this->connection,
            'unit_source' => $this->attributes['unit_source'] ?? null
        ]);
    }

    public function getTimeAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Makassar');
    }

    // Override method save untuk memastikan data tersimpan di koneksi yang benar
    public function save(array $options = [])
    {
        if (!isset($this->attributes['unit_source'])) {
            $this->attributes['unit_source'] = $this->connection;
        }
        
        Log::debug('Saving Attendance', [
            'connection' => $this->connection,
            'unit_source' => $this->attributes['unit_source']
        ]);

        return parent::save($options);
    }
}
