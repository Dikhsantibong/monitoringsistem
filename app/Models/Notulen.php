<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\TextFormatter;

class Notulen extends Model
{
    protected $fillable = [
        'nomor_urut',
        'unit',
        'bidang',
        'sub_bidang',
        'bulan',
        'tahun',
        'pembahasan',
        'tindak_lanjut',
        'format_nomor',
        'pimpinan_rapat',
        'tempat',
        'agenda',
        'peserta',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'pimpinan_rapat_nama',
        'notulis_nama',
        'tanggal_tanda_tangan',
        'revision_count'
    ];

    protected $casts = [
        'tahun' => 'integer',
        'tanggal' => 'date',
        'tanggal_tanda_tangan' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime'
    ];

    public function getPembahasanAttribute($value)
    {
        // If we're in the edit form, return plain text format
        if (request()->route()->getName() === 'notulen.edit') {
            return TextFormatter::htmlToPlainText($value);
        }
        
        // Otherwise, format the text with our helper, specifying 'pembahasan' section
        return TextFormatter::parseListsToHtml($value, 'pembahasan');
    }

    public function getTindakLanjutAttribute($value)
    {
        // If we're in the edit form, return plain text format
        if (request()->route()->getName() === 'notulen.edit') {
            return TextFormatter::htmlToPlainText($value);
        }
        
        // Otherwise, format the text with our helper
        return TextFormatter::parseListsToHtml($value, 'tindak_lanjut');
    }

    // Generate the formatted number
    public static function generateFormatNomor($nomor_urut, $unit, $bidang, $sub_bidang, $bulan, $tahun)
    {
        return sprintf(
            '%s/%s/%s/%s/%s/%s',
            str_pad($nomor_urut, 4, '0', STR_PAD_LEFT),
            $unit,
            $bidang,
            $sub_bidang,
            $bulan,
            $tahun
        );
    }

    public function attendances()
    {
        return $this->hasMany(NotulenAttendance::class);
    }

    public function documentations()
    {
        return $this->hasMany(NotulenDocumentation::class);
    }

    public function files()
    {
        return $this->hasMany(NotulenFile::class);
    }

    /**
     * Get the revisions for this notulen
     */
    public function revisions()
    {
        return $this->hasMany(NotulenRevision::class);
    }

    /**
     * Track changes when notulen is updated
     */
    public function trackRevision($userId, $changes, $reason = null)
    {
        foreach ($changes as $field => $values) {
            $this->revisions()->create([
                'user_id' => $userId,
                'field_name' => $field,
                'old_value' => $values['old'],
                'new_value' => $values['new'],
                'revision_reason' => $reason
            ]);
        }

        $this->increment('revision_count');
    }

    /**
     * Get formatted revision history
     */
    public function getRevisionHistory()
    {
        return $this->revisions()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
