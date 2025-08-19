<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialMaster extends Model
{
    protected $table = 'material_master';
    public $timestamps = false;
    protected $fillable = [
        'inventory_statistic_code',
        'inventory_statistic_desc',
        'stock_code',
        'description',
        'quantity',
        'inventory_price',
        'inventory_value',
        'updated_at',
    ];
}
