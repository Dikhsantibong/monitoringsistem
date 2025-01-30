<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PowerPlant extends Model
{
    use HasFactory;

    public static $isSyncing = false;

    protected $table = 'power_plants';

    protected $fillable = [
        'id',
        'name',
        'latitude',
        'longitude',
        'unit_source'
    ];

    public function machines()
    {
        return $this->hasMany(Machine::class, 'power_plant_id');
    }

    public function getMachinesByName($name)
    {
        return $this->machines()->where('name', $name)->get();
    }

    public function getConnectionName()
    {
        return session('unit');
    }

    public function scopeByUnitSource($query, $unitSource)
    {
        return $query->where('unit_source', $unitSource);
    }

    protected static function boot()
    {
        parent::boot();
        
        // Handle Created Event
        static::created(function ($powerPlant) {
            self::syncToUpKendari('create', $powerPlant);
        });

        // Handle Updated Event
        static::updated(function ($powerPlant) {
            self::syncToUpKendari('update', $powerPlant);
        });

        // Handle Deleted Event
        static::deleted(function ($powerPlant) {
            self::syncToUpKendari('delete', $powerPlant);
        });
    }

    protected static function syncToUpKendari($action, $powerPlant)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $data = [
                'id' => $powerPlant->id,
                'name' => $powerPlant->name,
                'latitude' => $powerPlant->latitude,
                'longitude' => $powerPlant->longitude,
                'unit_source' => 'poasia',
                'created_at' => $powerPlant->created_at,
                'updated_at' => $powerPlant->updated_at
            ];

            Log::info("Attempting to {$action} Power Plant sync", ['data' => $data]);

            $upKendari = DB::connection('mysql')->table('power_plants');

            switch($action) {
                case 'create':
                    $upKendari->insert($data);
                    break;
                    
                case 'update':
                    $upKendari->where('id', $powerPlant->id)
                             ->update($data);
                    break;
                    
                case 'delete':
                    $upKendari->where('id', $powerPlant->id)
                             ->delete();
                    break;
            }

            Log::info("Power Plant {$action} sync successful", [
                'id' => $powerPlant->id,
                'unit' => 'poasia'
            ]);

        } catch (\Exception $e) {
            Log::error("Power Plant {$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
        }
    }

    // Relasi dengan ServiceRequest
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    // Relasi dengan WorkOrder
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    // Relasi dengan WoBacklog
    public function woBacklogs()
    {
        return $this->hasMany(WoBacklog::class);
    }

    // Mendapatkan koneksi database berdasarkan unit_source
    public static function getConnectionByUnitSource($unitSource)
    {
        $unitConnections = [
            'mysql_poasia' => 'mysql_poasia',
            'mysql_kolaka' => 'mysql_kolaka',
            'mysql_bau_bau' => 'mysql_bau_bau',
            'mysql_wua_wua' => 'mysql_wua_wua',
            'mysql' => 'u478221055_up_kendari'
        ];

        return $unitConnections[$unitSource] ?? 'mysql';
    }

    // Mendapatkan nama database berdasarkan unit_source
    public static function getDatabaseNameByUnitSource($unitSource)
    {
        $databases = [
            'mysql_poasia' => 'u478221055_ulpltd_poasia',
            'mysql_kolaka' => 'u478221055_ulpltd_kolaka',
            'mysql_bau_bau' => 'u478221055_ulpltd_bau_bau',
            'mysql_wua_wua' => 'u478221055_ulpltd_wua_wua',
            'mysql' => 'u478221055_up_kendari'
        ];

        return $databases[$unitSource] ?? 'u478221055_up_kendari';
    }

    public function calculateUnservedLoad($startDate = null, $endDate = null)
    {
        // Jika tanggal tidak diset, gunakan 7 hari terakhir
        $startDate = $startDate ?: now()->subDays(6)->startOfDay();
        $endDate = $endDate ?: now()->endOfDay();

        // Ambil semua status log mesin dengan kondisi yang menyebabkan beban tak tersalur
        $unservedLoads = $this->machines()
            ->with(['statusLogs' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate])
                      ->whereIn('status', ['Gangguan', 'Mothballed', 'Overhaul'])
                      ->select('machine_id', 'tanggal', 'status', 'dmp');
            }])
            ->get()
            ->flatMap(function($machine) {
                return $machine->statusLogs->map(function($log) use ($machine) {
                    return [
                        'date' => $log->tanggal->format('Y-m-d'),
                        'dmp' => $log->dmp ?? 0,
                        'status' => $log->status,
                        'machine_name' => $machine->name
                    ];
                });
            })
            ->groupBy('date');

        return [
            'power_plant' => $this->name,
            'daily_data' => $unservedLoads,
            'total_unserved' => $unservedLoads->sum(function($day) {
                return collect($day)->sum('dmp');
            })
        ];
    }
}

