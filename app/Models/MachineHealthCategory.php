<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineHealthCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'active_issues',
        'resolved_issues'
    ];

    public function issues()
    {
        return $this->hasMany(MachineIssue::class, 'category_id');
    }
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'u478221055_up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
} 