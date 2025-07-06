<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotulenAttendance extends Model
{
    protected $fillable = [
        'notulen_id',
        'name',
        'position',
        'signature'
    ];

    public function notulen()
    {
        return $this->belongsTo(Notulen::class);
    }
}
