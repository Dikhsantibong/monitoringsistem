<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Section extends Model
{
    protected $fillable = ['department_id', 'name'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function pics()
    {
        return $this->hasMany(Pic::class);
    }
} 