<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SRWO extends Model
{
    protected $table = 'sr_wo';
    
    protected $fillable = [
        'nomor',
        'tanggal',
        'unit',
        'deskripsi',
        'status'
    ];
    
    protected $dates = [
        'tanggal'
    ];
} 