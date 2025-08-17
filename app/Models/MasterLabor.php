<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterLabor extends Model
{
    protected $table = 'master_labors';
    protected $fillable = [
        'nama',
        'bidang',
        'unit',
    ];
    public $timestamps = true;
}
