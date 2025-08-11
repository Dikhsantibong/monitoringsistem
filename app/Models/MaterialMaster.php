<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialMaster extends Model
{
    protected $table = 'material_master';
    public $timestamps = false;
    protected $fillable = [
        'code',
        'deskripsi',
        'kategori',
    ];
}
