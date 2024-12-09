<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_participants')
                    ->withTimestamps()
                    ->withPivot('status', 'notes');
    }

    public function createdMeetings()
    {
        return $this->hasMany(Meeting::class, 'created_by');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
