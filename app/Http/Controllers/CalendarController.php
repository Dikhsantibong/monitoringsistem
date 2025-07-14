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
        // Date filter
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Tentukan bulan dan tahun yang akan ditampilkan
        if ($startDate) {
            $month = Carbon::parse($startDate)->month;
            $year = Carbon::parse($startDate)->year;
        } else {
            $month = now()->month;
            $year = now()->year;
        }

        // Tanggal awal dan akhir bulan
        $firstDay = Carbon::create($year, $month, 1);
        $lastDay = (clone $firstDay)->endOfMonth();

        // Jika filter endDate di luar bulan, sesuaikan lastDay
        if ($endDate && Carbon::parse($endDate)->month == $month && Carbon::parse($endDate)->year == $year) {
            $lastDay = Carbon::parse($endDate);
        }
        if ($startDate && Carbon::parse($startDate)->month == $month && Carbon::parse($startDate)->year == $year) {
            $firstDay = Carbon::parse($startDate);
        }

        // Get work orders only
        $workOrdersQuery = WorkOrder::select('id', 'description', 'created_at', 'type', 'status');
        if ($startDate) {
            $workOrdersQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $workOrdersQuery->whereDate('created_at', '<=', $endDate);
        }
        $workOrders = $workOrdersQuery->get()
            ->map(function ($wo) {
                return [
                    'id' => $wo->id,
                    'type' => 'Work Order - ' . $wo->type,
                    'description' => $wo->description,
                    'date' => Carbon::parse($wo->created_at)->format('Y-m-d'),
                    'status' => $wo->status ?? 'Pending',
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
            'startDate' => $startDate,
            'endDate' => $endDate,
            'month' => $month,
            'year' => $year,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
        ]);
    }
}
