<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineIssue extends Model
{
    protected $fillable = [
        'machine_id',
        'category_id',
        'description',
        'status',
        'resolved_at',
        'resolution_notes'
    ];

    protected $casts = [
        'resolved_at' => 'datetime'
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function category()
    {
        return $this->belongsTo(MachineHealthCategory::class, 'category_id');
    }
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'u478221055_up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
} 