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
        'signature'
    ];

    public function notulen()
    {
        return $this->belongsTo(Notulen::class);
    }
}
