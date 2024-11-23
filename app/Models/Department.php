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
} 