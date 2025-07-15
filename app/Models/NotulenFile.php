<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotulenFile extends Model
{
    protected $table = 'notulen_files';
    protected $fillable = [
        'notulen_id',
        'session_id',
        'file_path',
        'file_name',
        'file_type',
        'caption',
    ];

    public function notulen()
    {
        return $this->belongsTo(Notulen::class);
    }
} 