<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotulenRevision extends Model
{
    protected $fillable = [
        'notulen_id',
        'user_id',
        'field_name',
        'old_value',
        'new_value',
        'revision_reason'
    ];

    /**
     * Get the notulen that owns this revision
     */
    public function notulen()
    {
        return $this->belongsTo(Notulen::class);
    }

    /**
     * Get the user who made this revision
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted field name for display
     */
    public function getFormattedFieldName()
    {
        $fieldMappings = [
            'agenda' => 'Agenda Rapat',
            'unit' => 'Unit',
            'pimpinan_rapat' => 'Pimpinan Rapat',
            'tanggal' => 'Tanggal Rapat',
            // Tambahkan mapping lainnya sesuai kebutuhan
        ];

        return $fieldMappings[$this->field_name] ?? $this->field_name;
    }
}
