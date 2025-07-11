<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotulenAttendance extends Model
{
    protected $table = 'notulen_attendances';
    protected $fillable = [
        'notulen_id',
        'session_id',
        'name',
        'position',
        'division',
        'signature',
        'is_late',
        'attended_at'
    ];

    protected $casts = [
        'is_late' => 'boolean',
        'attended_at' => 'datetime'
    ];

    public function notulen()
    {
        return $this->belongsTo(Notulen::class);
    }
}
