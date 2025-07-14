<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // Ambil bulan dan tahun dari query string, default ke bulan & tahun sekarang
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Tanggal awal dan akhir bulan
        $firstDay = Carbon::create($year, $month, 1);
        $lastDay = (clone $firstDay)->endOfMonth();

        // Get work orders only untuk bulan & tahun yang dipilih
        $workOrdersQuery = WorkOrder::with('powerPlant')
            ->select('id', 'description', 'created_at', 'type', 'status', 'priority', 'schedule_start', 'schedule_finish', 'power_plant_id', 'unit_source')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);
        $workOrders = $workOrdersQuery->get()
            ->map(function ($wo) {
                return [
                    'id' => $wo->id,
                    'type' => 'Work Order - ' . $wo->type,
                    'description' => $wo->description,
                    'date' => Carbon::parse($wo->created_at)->format('Y-m-d'),
                    'status' => $wo->status ?? 'Pending',
                    'priority' => $wo->priority ?? null,
                    'schedule_start' => $wo->schedule_start ?? null,
                    'schedule_finish' => $wo->schedule_finish ?? null,
                    'unit_source' => $wo->unit_source ?? null,
                    'power_plant_name' => $wo->powerPlant->name ?? '-',
                    'created_at' => $wo->created_at,
                    'updated_at' => $wo->updated_at,
                ];
            });

        // Buat array tanggal satu bulan penuh
        $dates = [];
        $current = $firstDay->copy();
        while ($current->lte($lastDay)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        // Group events by date
        $eventsByDate = $workOrders->groupBy('date');

        // Gabungkan semua tanggal dengan event (atau array kosong)
        $events = collect($dates)->mapWithKeys(function ($date) use ($eventsByDate) {
            return [$date => $eventsByDate->get($date, collect())];
        });

        // Untuk grid kalender, butuh info bulan & tahun
        return view('calendar.index', [
            'events' => $events,
            'month' => $month,
            'year' => $year,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
        ]);
    }
}
