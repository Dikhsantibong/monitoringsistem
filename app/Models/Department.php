<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan ini

class Department extends Model
{
    use HasFactory; // Tambahkan ini jika diperlukan
    
    protected $fillable = ['name'];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function pics()
    {
        return $this->hasMany(Pic::class);
    }
}
