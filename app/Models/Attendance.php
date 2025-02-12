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
        return session('unit', 'mysql');
    }

    public function getTimeAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Makassar');
    }
}
