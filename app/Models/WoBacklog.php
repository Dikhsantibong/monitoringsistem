<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WoBacklog extends Model
{
    use HasFactory;

    protected $table = 'wo_backlog';

    // Tambahkan kolom yang dapat diisi secara massal
    protected $fillable = [
        'no_wo',
        'deskripsi',
        'tanggal_backlog',
        'keterangan'
    ];
}
