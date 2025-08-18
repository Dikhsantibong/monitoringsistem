<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Events\KatalogFileUpdated;

class KatalogFile extends Model
{
    protected $table = 'katalog_files';
    protected $fillable = [
        'user_id', 'filename', 'path', 'no_part', 'nama_material', 'created_at', 'updated_at'
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
        $this->attributes['user_id'] = $value;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            event(new KatalogFileUpdated($model, 'create'));
        });
        static::updated(function ($model) {
            event(new KatalogFileUpdated($model, 'update'));
        });
        static::deleted(function ($model) {
            event(new KatalogFileUpdated($model, 'delete'));
        });
    }
}
