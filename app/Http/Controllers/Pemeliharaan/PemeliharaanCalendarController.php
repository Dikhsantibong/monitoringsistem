<?php

namespace App\Http\Controllers\Pemeliharaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\WoBacklog;
use Carbon\Carbon;

class PemeliharaanCalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        $firstDay = Carbon::create($year, $month, 1);
        $lastDay = $firstDay->copy()->endOfMonth();

        // Ambil semua WO dan Backlog untuk bulan ini
        $workOrders = WorkOrder::with('powerPlant')
            ->whereMonth('schedule_start', $month)
            ->whereYear('schedule_start', $year)
            ->get();
        $backlogs = WoBacklog::with('powerPlant')
            ->whereMonth('tanggal_backlog', $month)
            ->whereYear('tanggal_backlog', $year)
            ->get();

        // Kelompokkan event berdasarkan tanggal
        $events = [];
        foreach ($workOrders as $wo) {
            $date = Carbon::parse($wo->schedule_start)->toDateString();
            $events[$date][] = [
                'id' => $wo->id,
                'description' => $wo->description,
                'type' => $wo->type,
                'status' => $wo->status,
                'priority' => $wo->priority,
                'schedule_start' => $wo->schedule_start,
                'schedule_finish' => $wo->schedule_finish,
                'power_plant_name' => $wo->powerPlant->name ?? '-',
                'labor' => $wo->labor,
            ];
        }
        $backlogEvents = [];
        foreach ($backlogs as $b) {
            $date = Carbon::parse($b->tanggal_backlog)->toDateString();
            $backlogEvents[$date][] = [
                'id' => $b->no_wo,
                'description' => $b->deskripsi,
                'type' => $b->type_wo,
                'status' => $b->status,
                'priority' => $b->priority,
                'schedule_start' => $b->schedule_start,
                'schedule_finish' => $b->schedule_finish,
                'power_plant_name' => $b->powerPlant->name ?? '-',
                'labor' => $b->labor,
            ];
        }

        return view('pemeliharaan.calendar', [
            'month' => $month,
            'year' => $year,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
            'events' => $events,
            'backlogEvents' => $backlogEvents,
        ]);
    }
}
