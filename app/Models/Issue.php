<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_id',
        'category_id',
        'description',
        'status',
    ];

    // Relasi dengan Machine
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    // Relasi dengan Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'u478221055_up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
} 