<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitStatus extends Model
{
    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql';

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
}
