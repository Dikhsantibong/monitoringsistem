<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyQrCode extends Model
{
    protected $fillable = ['code', 'valid_date', 'is_active'];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
} 