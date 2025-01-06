<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MachineStatusLog extends Model
{
    use HasFactory;

    public static $isSyncing = false;

    protected $table = 'machine_status_logs';

    protected $fillable = [
        'machine_id',
        'tanggal',
        'status',
        'component',
        'equipment',
        'deskripsi',
        'kronologi',
        'action_plan',
        'progres',
        'tanggal_mulai',
        'target_selesai',
        'dmn',
        'dmp',
        'load_value',
        'unit_source'
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
        return $this->belongsTo(Machine::class);
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
        return session('unit');
    }

    protected static function boot()
    {
        parent::boot();
        
        // Handle Created Event
        static::created(function ($machineStatusLog) {
            self::syncToUpKendari('create', $machineStatusLog);
        });

        // Handle Updated Event
        static::updated(function ($machineStatusLog) {
            self::syncToUpKendari('update', $machineStatusLog);
        });

        // Handle Deleted Event
        static::deleted(function ($machineStatusLog) {
            self::syncToUpKendari('delete', $machineStatusLog);
        });
    }

    protected static function syncToUpKendari($action, $machineStatusLog)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $data = [
                'id' => $machineStatusLog->id,
                'machine_id' => $machineStatusLog->machine_id,
                'tanggal' => $machineStatusLog->tanggal,
                'status' => $machineStatusLog->status,
                'component' => $machineStatusLog->component,
                'equipment' => $machineStatusLog->equipment,
                'deskripsi' => $machineStatusLog->deskripsi,
                'kronologi' => $machineStatusLog->kronologi,
                'action_plan' => $machineStatusLog->action_plan,
                'progres' => $machineStatusLog->progres,
                'tanggal_mulai' => $machineStatusLog->tanggal_mulai,
                'target_selesai' => $machineStatusLog->target_selesai,
                'dmn' => $machineStatusLog->dmn,
                'dmp' => $machineStatusLog->dmp,
                'load_value' => $machineStatusLog->load_value,
                'unit_source' => session('unit'),
                'created_at' => $machineStatusLog->created_at,
                'updated_at' => $machineStatusLog->updated_at
            ];

            Log::info("Attempting to {$action} Machine Status Log sync", ['data' => $data]);

            $upKendari = DB::connection('mysql')->table('machine_status_logs');

            switch($action) {
                case 'create':
                    $upKendari->insert($data);
                    break;
                    
                case 'update':
                    $upKendari->where('id', $machineStatusLog->id)
                             ->update($data);
                    break;
                    
                case 'delete':
                    $upKendari->where('id', $machineStatusLog->id)
                             ->delete();
                    break;
            }

            Log::info("Machine Status Log {$action} sync successful", [
                'id' => $machineStatusLog->id,
                'unit' => 'poasia'
            ]);

        } catch (\Exception $e) {
            Log::error("Machine Status Log {$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
        }
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
} 