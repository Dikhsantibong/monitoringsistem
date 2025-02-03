<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Commitment extends Model
{
    protected $fillable = [
        'description',
        'deadline',
        'department_id',
        'section_id',
        'status',
        'pic',
        'other_discussion_id'
    ];

    // Tambahkan accessor untuk mengecek status overdue
    public function getIsOverdueAttribute()
    {
        if ($this->status === 'Closed') {
            return false;
        }
        return Carbon::now()->startOfDay()->gt(Carbon::parse($this->deadline));
    }

    // Relasi ke discussion
    public function discussion()
    {
        return $this->belongsTo(OtherDiscussion::class, 'other_discussion_id');
    }

    // Relasi ke department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Relasi ke section
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // Getter untuk PIC (gabungan department dan section)
    public function getPicAttribute()
    {
        if ($this->department && $this->section) {
            return $this->department->name . ' - ' . $this->section->name;
        }
        return null;
    }
    public function getConnectionName()
    {
        return session('unit', 'u478221055_up_kendari');
    }
} 