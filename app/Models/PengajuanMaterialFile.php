<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Events\PengajuanMaterialFileUpdated;

class PengajuanMaterialFile extends Model
{
    protected $table = 'pengajuan_material_files';
    protected $fillable = [
        'user_id', 'filename', 'path', 'created_at', 'updated_at'
    ];
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getConnectionName()
    {
        return session('unit', 'mysql');
    }

    public function setUserIdAttribute($value)
    {
        $this->attributes['user_id'] = session('unit', 'unknown_unit');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            event(new PengajuanMaterialFileUpdated($model, 'create'));
        });
        static::updated(function ($model) {
            event(new PengajuanMaterialFileUpdated($model, 'update'));
        });
        static::deleted(function ($model) {
            event(new PengajuanMaterialFileUpdated($model, 'delete'));
        });
    }
}
