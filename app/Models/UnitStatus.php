<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitStatus extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'unit_statuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'wonum',
        'status_unit',
    ];

    /**
     * Disable auto-increment if wonum is primary, 
     * but we use an 'id' as primary key for Laravel best practices.
     */

    /**
     * Mendapatkan koneksi secara dinamis seperti model lainnya
     */
    public function getConnectionName()
    {
        return session('unit', 'mysql');
    }
}
