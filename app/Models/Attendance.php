<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    
    protected $fillable = [
        'name',
        'division',
        'position',
        'time',
        'token'
    ];

    protected $dates = [
        'time',
        'created_at',
        'updated_at'
    ];

    // Tambahkan mutator untuk memastikan data tersimpan dengan benar
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strip_tags(trim($value));
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setDivisionAttribute($value)
    {
        $this->attributes['division'] = strip_tags(trim($value));
    }

    public function setPositionAttribute($value)
    {
        $this->attributes['position'] = strip_tags(trim($value));
    }
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
}