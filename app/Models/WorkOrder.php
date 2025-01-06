<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkOrder extends Model
{
    use HasFactory;

    public static $isSyncing = false;

    protected $fillable = [
        'id',
        'description',
        'status',
        'priority',
        'schedule_start',
        'schedule_finish',
        'unit_source'
    ];

    public function isExpired()
    {
        return Carbon::parse($this->schedule_finish)->isPast() && $this->status == 'Open';
    }

    public function moveToBacklog()
    {
        if ($this->isExpired()) {
            WoBacklog::create([
                'no_wo' => $this->id,
                'deskripsi' => $this->description,
                'tanggal_backlog' => $this->schedule_finish,
                'keterangan' => 'Otomatis masuk backlog karena melewati jadwal',
                'status' => 'Open'
            ]);

            return true;
        }
        return false;
    }

    public function getConnectionName()
    {
        return session('unit');
    }

    protected static function boot()
    {
        parent::boot();
        
        // Handle Created Event
        static::created(function ($workOrder) {
            self::syncToUpKendari('create', $workOrder);
        });

        // Handle Updated Event
        static::updated(function ($workOrder) {
            self::syncToUpKendari('update', $workOrder);
        });

        // Handle Deleted Event
        static::deleted(function ($workOrder) {
            self::syncToUpKendari('delete', $workOrder);
        });
    }

    protected static function syncToUpKendari($action, $workOrder)
    {
        if (self::$isSyncing) return;

        try {
            self::$isSyncing = true;
            
            $data = [
                'id' => $workOrder->id,
                'description' => $workOrder->description,
                'status' => $workOrder->status,
                'priority' => $workOrder->priority,
                'schedule_start' => $workOrder->schedule_start,
                'schedule_finish' => $workOrder->schedule_finish,
                'unit_source' => session('unit'),
                'created_at' => $workOrder->created_at,
                'updated_at' => $workOrder->updated_at
            ];

            Log::info("Attempting to {$action} WO sync", ['data' => $data]);

            $upKendari = DB::connection('mysql')->table('work_orders');

            switch($action) {
                case 'create':
                    $upKendari->insert($data);
                    break;
                    
                case 'update':
                    $upKendari->where('id', $workOrder->id)
                             ->update($data);
                    break;
                    
                case 'delete':
                    $upKendari->where('id', $workOrder->id)
                             ->delete();
                    break;
            }

            Log::info("Work Order {$action} sync successful", [
                'id' => $workOrder->id,
                'unit' => 'poasia'
            ]);

        } catch (\Exception $e) {
            Log::error("Work Order {$action} sync failed", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            self::$isSyncing = false;
        }
    }
} 