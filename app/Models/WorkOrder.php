<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // Hapus semua event model yang mungkin mempengaruhi status
    protected static function boot()
    {
        parent::boot();
    }
} 