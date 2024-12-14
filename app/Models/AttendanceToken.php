<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceToken extends Model
{
    protected $fillable = ['token', 'expires_at'];
    
    protected $dates = ['expires_at', 'created_at', 'updated_at'];

    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
} 