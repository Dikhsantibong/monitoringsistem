<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MachineStatusLog extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_mulai' => 'date',
        'target_selesai' => 'date'
    ];

    // Relasi ke model Machine
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    // Relasi ke PowerPlant melalui Machine
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
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'u478221055_up_kendari'); // default ke 'up_kendari' jika tidak ada
    }

    // Tambahkan method untuk data sementara
    public static function getDummyMonthlyData()
    {
        // Data dummy untuk 12 bulan di tahun 2024
        $dummyData = [
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
        ];

        // Konversi array ke collection
        return collect($dummyData);
    }

    // Tambahkan method untuk total gangguan aktif
    public static function getDummyActiveIssues()
    {
        return 15; // Contoh jumlah gangguan aktif
    }
} 