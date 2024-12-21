<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'expires_at',
        'user_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'expires_at'
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 