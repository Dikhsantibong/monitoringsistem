<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class Pic extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'name',
        'position',
        'department',
        'section'
    ];

    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($pic) {
            Log::info('PIC retrieved:', [
                'id' => $pic->id,
                'name' => $pic->name,
                'section_id' => $pic->section_id
            ]);
        });
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
} 