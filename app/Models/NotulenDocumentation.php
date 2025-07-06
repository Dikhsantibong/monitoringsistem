<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotulenDocumentation extends Model
{
    protected $table = 'notulen_documentation';
    protected $fillable = [
        'notulen_id',
        'session_id',
        'image_path',
        'caption',
    ];

    public function notulen()
    {
        return $this->belongsTo(Notulen::class);
    }
}
