<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description'
    ];

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
} 