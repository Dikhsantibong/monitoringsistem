<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
