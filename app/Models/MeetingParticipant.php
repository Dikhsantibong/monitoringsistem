<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MeetingParticipant extends Pivot
{
    protected $table = 'meeting_participants';

    protected $fillable = [
        'meeting_id',
        'user_id',
        'status',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'u478221055_up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
} 