<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tanggalAwal;
    protected $tanggalAkhir;

    public function __construct($tanggalAwal, $tanggalAkhir)
    {
        $this->tanggalAwal = Carbon::parse($tanggalAwal)->startOfDay();
        $this->tanggalAkhir = Carbon::parse($tanggalAkhir)->endOfDay();
    }

    public function collection()
    {
        return Attendance::whereBetween('time', [$this->tanggalAwal, $this->tanggalAkhir])
            ->orderBy('time', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Tanggal',
            'Divisi',
            'Jabatan',
            'Waktu Hadir',
            'Status'
        ];
    }

    public function map($attendance): array
    {
        $waktuHadir = Carbon::parse($attendance->time)->format('H:i:s');
        $status = $waktuHadir <= '09:00:00' ? 'Tepat Waktu' : 'Terlambat';

        return [
            $attendance->id,
            $attendance->name,
            Carbon::parse($attendance->time)->format('d/m/Y'),
            $attendance->division,
            $attendance->position,
            $waktuHadir,
            $status
        ];
    }
} 