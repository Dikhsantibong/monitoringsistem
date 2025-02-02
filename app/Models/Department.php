<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function pics()
    {
        return $this->hasMany(Pic::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::retrieved(function ($department) {
            \Log::info('Department retrieved:', $department->toArray());
        });
    }
    public function getConnectionName()
    {
        return session('unit');
    }
}
