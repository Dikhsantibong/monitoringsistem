<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    // Tentukan nama tabel yang benar
    protected $table = 'attendance';

    protected $fillable = [
        'user_id',
        'qr_code',
        'attended_at',
        'is_valid'
    ];

    protected $dates = [
        'attended_at',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}