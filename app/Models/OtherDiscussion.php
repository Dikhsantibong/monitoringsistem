<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherDiscussion extends Model
{
    use HasFactory;

    protected $table = 'other_discussions';

    protected $fillable = [
        'sr_number',
        'wo_number',
        'unit',
        'topic',
        'target',
        'risk_level',
        'priority_level',
        'previous_commitment',
        'next_commitment',
        'pic',
        'status',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'date',
        'sr_number' => 'integer',
        'wo_number' => 'integer',
    ];

    protected $dates = ['deadline'];

    // Konstanta untuk pilihan unit
    const UNITS = [
        'UP KENDARI',
        'ULPLTD POASIA',
        'ULPLTD KOLAKA',
        'ULPLTD WUA WUA',
        'ULPLTD BAU BAU'
    ];

    // Konstanta untuk tingkat resiko
    const RISK_LEVELS = [
        'R' => 'Rendah',
        'MR' => 'Menengah Rendah',
        'MT' => 'Menengah Tinggi',
        'T' => 'Tinggi'
    ];

    // Konstanta untuk tingkat prioritas
    const PRIORITY_LEVELS = [
        'Low',
        'Medium',
        'High'
    ];

    // Konstanta untuk status
    const STATUSES = [
        'Open',
        'Closed'
    ];

    // Accessor untuk mendapatkan label tingkat resiko
    public function getRiskLevelLabelAttribute()
    {
        return self::RISK_LEVELS[$this->risk_level] ?? $this->risk_level;
    }

    // Scope untuk filter berdasarkan unit
    public function scopeByUnit($query, $unit)
    {
        return $query->where('unit', $unit);
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan tingkat resiko
    public function scopeByRiskLevel($query, $riskLevel)
    {
        return $query->where('risk_level', $riskLevel);
    }

    // Scope untuk filter berdasarkan tingkat prioritas
    public function scopeByPriorityLevel($query, $priorityLevel)
    {
        return $query->where('priority_level', $priorityLevel);
    }
} 