<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Events\MachineStatusUpdated;
use Illuminate\Support\Facades\Storage;



class MachineStatusLog extends Model
{
    use HasFactory;

    public static $isSyncing = false;

    protected $table = 'machine_status_logs';

    protected $fillable = [
        'machine_id',
        'tanggal',
        'status',
        'dmn',
        'dmp',
        'load_value',
        'component',
        'equipment',
        'deskripsi',
        'kronologi',
        'action_plan',
        'progres',
        'tanggal_mulai',
        'target_selesai',
        'unit_source',
        'image_url'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_mulai' => 'date',
        'target_selesai' => 'date'
    ];

    protected $dates = [
        'tanggal',
        'tanggal_mulai',
        'target_selesai',
        'created_at',
        'updated_at'
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function powerPlant()
    {
        return $this->hasOneThrough(
            PowerPlant::class,
            Machine::class,
            'id', 
            'id',
            'machine_id', 
            'power_plant_id' 
        );
    }

    public function machineOperation()
    {
        return $this->hasOne(MachineOperation::class, 'machine_id', 'machine_id')
            ->whereDate('recorded_at', '=', DB::raw('DATE(machine_status_logs.tanggal)'));
    }

    public function getConnectionName()
    {
        return session('unit', 'mysql');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($machineStatus) {
            if (session('unit') !== 'mysql') {
                event(new MachineStatusUpdated($machineStatus, 'create'));
            }
        });

        static::updated(function ($machineStatus) {
            if (session('unit') !== 'mysql') {
                event(new MachineStatusUpdated($machineStatus, 'update'));
            }
        });

        static::deleted(function ($machineStatus) {
            if (session('unit') !== 'mysql') {
                event(new MachineStatusUpdated($machineStatus, 'delete'));
            }
        });
    }

    // Helper methods yang sudah ada
    public static function getDummyMonthlyData()
    {
        return collect([
            ['month' => 'January', 'count' => 5, 'tanggal' => '2024-01-15'],
            ['month' => 'February', 'count' => 8, 'tanggal' => '2024-02-15'],
            ['month' => 'March', 'count' => 3, 'tanggal' => '2024-03-15'],
            ['month' => 'April', 'count' => 7, 'tanggal' => '2024-04-15'],
            ['month' => 'May', 'count' => 12, 'tanggal' => '2024-05-15'],
            ['month' => 'June', 'count' => 6, 'tanggal' => '2024-06-15'],
            ['month' => 'July', 'count' => 9, 'tanggal' => '2024-07-15'],
            ['month' => 'August', 'count' => 15, 'tanggal' => '2024-08-15'],
            ['month' => 'September', 'count' => 11, 'tanggal' => '2024-09-15'],
            ['month' => 'October', 'count' => 4, 'tanggal' => '2024-10-15'],
            ['month' => 'November', 'count' => 7, 'tanggal' => '2024-11-15'],
            ['month' => 'December', 'count' => 10, 'tanggal' => '2024-12-15']
        ]);
    }

    public static function getDummyActiveIssues()
    {
        return 15;
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('H:i:s d/m/Y') : 'N/A';
    }

    public function isActiveIssue()
    {
        if ($this->status !== 'Gangguan') {
            return false;
        }
        return !$this->target_selesai || Carbon::now()->lte($this->target_selesai);
    }

    public function hasNewerUpdate()
    {
        return static::where('machine_id', $this->machine_id)
            ->where('created_at', '>', $this->created_at)
            ->whereBetween('created_at', [$this->tanggal_mulai, $this->target_selesai])
            ->exists();
    }

    public static function getChartData($powerPlantId)
    {
        $lastWeek = Carbon::now()->subWeek();
        $today = Carbon::now();

        \Log::info('Getting chart data for power plant:', [
            'powerPlantId' => $powerPlantId,
            'dateRange' => [$lastWeek, $today]
        ]);

        $data = static::query()
            ->join('machines', 'machines.id', '=', 'machine_status_logs.machine_id')
            ->where('machines.power_plant_id', $powerPlantId)
            ->whereBetween('tanggal', [$lastWeek, $today])
            ->select(
                DB::raw('DATE(tanggal) as date'),
                DB::raw('AVG(load_value) as avg_load'),
                DB::raw('SUM(machines.capacity) as total_capacity')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        \Log::info('Chart data retrieved:', ['data' => $data]);

        return $data->map(function ($item) {
            return [
                'date' => Carbon::parse($item->date)->format('D'),
                'load' => round($item->avg_load, 2),
                'capacity' => round($item->total_capacity, 2)
            ];
        });
    }
} 