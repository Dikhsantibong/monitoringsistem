<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    protected function cleanHtml($html)
    {
        if (!$html) return $html;

        // Remove style, class, and id attributes
        $html = preg_replace('/\s+style\s*=\s*"[^"]*"/', '', $html);
        $html = preg_replace('/\s+class\s*=\s*"[^"]*"/', '', $html);
        $html = preg_replace('/\s+id\s*=\s*"[^"]*"/', '', $html);

        // Replace <p> tags with line breaks
        $html = preg_replace('/<p[^>]*>/', '', $html);
        $html = str_replace('</p>', "\n", $html);

        // Replace multiple line breaks with a single one
        $html = preg_replace("/[\r\n]+/", "\n", $html);

        // Convert divs to line breaks
        $html = preg_replace('/<div[^>]*>/', '', $html);
        $html = str_replace('</div>', "\n", $html);

        // Handle lists properly
        $html = str_replace('<ul>', "\n", $html);
        $html = str_replace('</ul>', "\n", $html);
        $html = str_replace('<ol>', "\n", $html);
        $html = str_replace('</ol>', "\n", $html);
        $html = str_replace('<li>', '• ', $html);
        $html = str_replace('</li>', "\n", $html);

        // Clean up any remaining HTML tags but preserve line breaks
        $html = strip_tags($html, '<br>');

        // Convert <br> to line breaks
        $html = str_replace(['<br>', '<br/>', '<br />'], "\n", $html);

        // Clean up extra spaces and line breaks
        $html = preg_replace('/\s+/', ' ', $html);
        $html = preg_replace("/[\n\r]+/", "\n", $html);

        return trim($html);
    }

    public function getPembahasanAttribute($value)
    {
        return nl2br($this->cleanHtml($value));
    }

    public function getTindakLanjutAttribute($value)
    {
        return nl2br($this->cleanHtml($value));
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
