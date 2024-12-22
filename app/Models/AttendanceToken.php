<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceToken extends Model
{
    use HasFactory;

    protected $table = 'attendance_tokens';

    protected $fillable = [
        'token',
        'expires_at',
        'user_id'
    ];

    protected $dates = ['expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'u478221055_up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
} 