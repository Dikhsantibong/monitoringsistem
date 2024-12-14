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
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
} 