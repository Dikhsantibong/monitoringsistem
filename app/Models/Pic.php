<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pic extends Model
{
    protected $fillable = ['name', 'position', 'department'];

    public function commitments()
    {
        return $this->hasMany(Commitment::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
} 