<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name'
    ];

    protected $with = ['pics']; // Eager load pics by default

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function pics()
    {
        return $this->hasMany(Pic::class)->orderBy('name');
    }

    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($section) {
            Log::info('Section retrieved:', [
                'id' => $section->id,
                'name' => $section->name,
                'department_id' => $section->department_id
            ]);
        });
    }
    public function getConnectionName()
    {
        return session('unit');
    }
} 