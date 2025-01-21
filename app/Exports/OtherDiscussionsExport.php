<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OtherDiscussionsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'No SR',
            'No Pembahasan',
            'Unit',
            'Topik',
            'Target',
            'PIC',
            'Status',
            'Deadline',
            'Komitmen',
            'PIC Komitmen',
            'Deadline Komitmen',
            'Status Komitmen'
        ];
    }

    public function map($discussion): array
    {
        $rows = [];
        
        // Jika tidak ada komitmen, tampilkan satu baris
        if ($discussion->commitments->isEmpty()) {
            $rows[] = [
                $discussion->sr_number,
                $discussion->no_pembahasan,
                $discussion->unit,
                $discussion->topic,
                $discussion->target,
                $discussion->pic,
                $discussion->status,
                $discussion->target_deadline ? date('d/m/Y', strtotime($discussion->target_deadline)) : '-',
                '-',
                '-',
                '-',
                '-'
            ];
        } else {
            // Jika ada komitmen, tampilkan satu baris untuk setiap komitmen
            foreach ($discussion->commitments as $commitment) {
                $rows[] = [
                    $discussion->sr_number,
                    $discussion->no_pembahasan,
                    $discussion->unit,
                    $discussion->topic,
                    $discussion->target,
                    $discussion->pic,
                    $discussion->status,
                    $discussion->target_deadline ? date('d/m/Y', strtotime($discussion->target_deadline)) : '-',
                    $commitment->description,
                    $commitment->pic,
                    $commitment->deadline ? date('d/m/Y', strtotime($commitment->deadline)) : '-',
                    $commitment->status
                ];
            }
        }

        return $rows;
    }
} 