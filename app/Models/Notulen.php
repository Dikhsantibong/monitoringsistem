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

        // Preserve line breaks and spacing
        $html = str_replace(['<br>', '<br/>', '<br />'], "\n", $html);

        // Handle lists and numbering
        $html = str_replace(['<ul>', '</ul>', '<ol>', '</ol>'], "\n", $html);

        // Special handling for list items to preserve indentation
        $html = preg_replace('/<li[^>]*>(.*?)<\/li>/i', '  • $1' . "\n", $html);

        // Handle paragraphs while preserving format
        $html = preg_replace('/<p[^>]*>(.*?)<\/p>/i', '$1' . "\n", $html);

        // Remove any remaining HTML tags except formatting
        $html = strip_tags($html);

        // Preserve letter/number points (e.g., "a.", "1.", etc.)
        $html = preg_replace('/^([a-z0-9]\.)\s*/m', '$1 ', $html);

        // Preserve indentation for points with reduced spacing
        $lines = explode("\n", $html);
        $formattedLines = [];
        $prevLineWasPoint = false;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            if (empty($trimmedLine)) {
                if (!$prevLineWasPoint) {
                    $formattedLines[] = "";
                }
                continue;
            }

            // Check if line starts with a point (a., 1., etc.)
            if (preg_match('/^[a-z0-9]\./', $trimmedLine)) {
                // Add smaller indentation for points
                if ($prevLineWasPoint) {
                    $formattedLines[] = "  " . $trimmedLine;
                } else {
                    $formattedLines[] = "\n  " . $trimmedLine;
                }
                $prevLineWasPoint = true;
            } else {
                if ($prevLineWasPoint) {
                    $formattedLines[] = $trimmedLine;
                } else {
                    $formattedLines[] = $trimmedLine;
                }
                $prevLineWasPoint = false;
            }
        }

        $html = implode("\n", $formattedLines);

        // Clean up extra spaces and normalize line breaks
        $html = preg_replace('/[ \t]+/', ' ', $html);
        $html = preg_replace('/\n{3,}/', "\n\n", $html);
        $html = preg_replace('/^\n+/', '', $html); // Remove leading newlines
        $html = preg_replace('/\n+$/', '', $html); // Remove trailing newlines

        // Add single line break between sections
        $html = preg_replace('/\n\n+/', "\n\n", $html);

        return trim($html);
    }

    public function getPembahasanAttribute($value)
    {
        $cleaned = $this->cleanHtml($value);
        // Only apply nl2br if we're not in the edit form
        return request()->route()->getName() === 'notulen.edit' ? $cleaned : nl2br($cleaned);
    }

    public function getTindakLanjutAttribute($value)
    {
        $cleaned = $this->cleanHtml($value);
        // Only apply nl2br if we're not in the edit form
        return request()->route()->getName() === 'notulen.edit' ? $cleaned : nl2br($cleaned);
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
