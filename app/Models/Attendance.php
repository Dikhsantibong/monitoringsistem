<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['user_id', 'daily_qr_code_id', 'attended_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dailyQrCode()
    {
        return $this->belongsTo(DailyQrCode::class);
    }
} 