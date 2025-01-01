<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'description',
        'status',
        'priority',
        'schedule_start',
        'schedule_finish'
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

            // $this->update(['status' => 'COMP']);
            return true;
        }
        return false;
    }

    public function getConnectionName()
    {
        return session('unit', 'u478221055_up_kendari');
    }
} 