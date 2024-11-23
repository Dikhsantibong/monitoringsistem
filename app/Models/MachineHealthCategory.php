<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineHealthCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'active_issues',
        'resolved_issues'
    ];

    public function issues()
    {
        return $this->hasMany(MachineIssue::class, 'category_id');
    }
} 