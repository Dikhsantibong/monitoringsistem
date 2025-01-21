<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class OtherDiscussionsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $discussions;

    public function __construct($discussions)
    {
        $this->discussions = $discussions;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->discussions);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'No SR',
            'No Pembahasan',
            'Unit',
            'Topik',
            'Target',
            'PIC',
            'Status',
            'Deadline',
            'Komitmen'
        ];
    }

    /**
     * @param mixed $discussion
     * @return array
     */
    public function map($discussion): array
    {
        $commitments = collect($discussion->commitments)->map(function($commitment) {
            return sprintf(
                "%s (PIC: %s, Deadline: %s, Status: %s)",
                $commitment->description ?? '-',
                $commitment->pic ?? '-',
                $commitment->deadline ? Carbon::parse($commitment->deadline)->format('d/m/Y') : '-',
                $commitment->status ?? '-'
            );
        })->join("\n");

        return [
            $discussion->id ?? '-',
            $discussion->sr_number ?? '-',
            $discussion->no_pembahasan ?? '-',
            $discussion->unit ?? '-',
            $discussion->topic ?? '-',
            $discussion->target ?? '-',
            $discussion->pic ?? '-',
            $discussion->status ?? '-',
            $discussion->target_deadline ? Carbon::parse($discussion->target_deadline)->format('d/m/Y') : '-',
            $commitments
        ];
    }
} 