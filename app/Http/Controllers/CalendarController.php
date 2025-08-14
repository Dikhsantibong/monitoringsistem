<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\WorkOrder;
use App\Models\MachineStatusLog;
use App\Models\WoBacklog;
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
            ->select('id', 'description', 'created_at', 'type', 'status', 'priority', 'schedule_start', 'schedule_finish', 'power_plant_id', 'unit_source', 'labor')
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
                    'labor' => $wo->labor ?? null // Tambahan labor
                ];
            });

        // Get backlog for selected month & year (gunakan tanggal_backlog jika ada, fallback ke created_at)
        $backlogsQuery = WoBacklog::with('powerPlant')
            ->select(
                'id', 'no_wo', 'deskripsi', 'type_wo', 'status', 'priority', 'tanggal_backlog',
                'schedule_start', 'schedule_finish', 'power_plant_id', 'unit_source', 'labor', 'created_at', 'updated_at'
            )
            ->whereYear('tanggal_backlog', $year)
            ->whereMonth('tanggal_backlog', $month);

        $backlogs = $backlogsQuery->get()
            ->map(function ($bl) {
                $date = $bl->tanggal_backlog ?: $bl->created_at;
                return [
                    'id' => 'BL-' . ($bl->no_wo ?? $bl->id),
                    'type' => 'Backlog - ' . strtoupper((string) $bl->type_wo),
                    'description' => $bl->deskripsi,
                    'date' => \Carbon\Carbon::parse($date)->format('Y-m-d'),
                    'status' => $bl->status ?? 'Open',
                    'priority' => $bl->priority ?? null,
                    'schedule_start' => $bl->schedule_start ?? null,
                    'schedule_finish' => $bl->schedule_finish ?? null,
                    'unit_source' => $bl->unit_source ?? null,
                    'power_plant_name' => optional($bl->powerPlant)->name ?? '-',
                    'created_at' => $bl->created_at,
                    'updated_at' => $bl->updated_at,
                    'labor' => $bl->labor ?? null,
                    'is_backlog' => true,
                ];
            });

        // Generate maintenance notifications when cumulative JSMO crosses thresholds
        $thresholds = [
            125 => 'P2',
            250 => 'P3',
            500 => 'P4',
            3000 => 'P5',
        ];
        // Ambil log hingga akhir bulan untuk menghitung kumulatif
        $logs = MachineStatusLog::with(['machine.powerPlant'])
            ->select('machine_id', 'tanggal', 'jsmo')
            ->whereDate('tanggal', '<=', $lastDay)
            ->orderBy('machine_id')
            ->orderBy('tanggal')
            ->get();
        $maintenanceEvents = collect();
        $logsByMachine = $logs->groupBy('machine_id');
        foreach ($logsByMachine as $machineId => $machineLogs) {
            $cumulative = 0.0;
            $triggered = [];
            foreach (array_keys($thresholds) as $t) { $triggered[$t] = false; }
            foreach ($machineLogs as $log) {
                $cumulative += (float) ($log->jsmo ?? 0);
                foreach ($thresholds as $limit => $label) {
                    if (!$triggered[$limit] && $cumulative >= $limit) {
                        $machine = optional($log->machine);
                        $powerPlant = optional($machine->powerPlant);
                        $maintenanceEvents->push([
                            'id' => 'M' . $machineId . '-' . $label,
                            'type' => 'Maintenance ' . $label,
                            'description' => sprintf('Mesin %s mencapai %d jam (%s)', (string) $machine->name, $limit, $label),
                            'date' => \Carbon\Carbon::parse($log->tanggal)->format('Y-m-d'),
                            'status' => 'Open',
                            'priority' => null,
                            'schedule_start' => null,
                            'schedule_finish' => null,
                            'unit_source' => null,
                            'power_plant_name' => (string) $powerPlant->name,
                            'created_at' => $log->tanggal,
                            'updated_at' => $log->tanggal,
                            'labor' => null,
                        ]);
                        $triggered[$limit] = true;
                    }
                }
            }
        }

        // Buat array tanggal satu bulan penuh
        $dates = [];
        $current = $firstDay->copy();
        while ($current->lte($lastDay)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        // Group terpisah: WO, Backlog, dan Maintenance
        $workOrdersByDate = $workOrders->groupBy('date');
        $backlogsByDate = $backlogs->groupBy('date');
        $maintenanceByDate = $maintenanceEvents->groupBy('date');

        // Buat peta tanggal -> events masing-masing
        $events = collect($dates)->mapWithKeys(function ($date) use ($workOrdersByDate) {
            return [$date => $workOrdersByDate->get($date, collect())];
        });
        $maintenanceEventsMap = collect($dates)->mapWithKeys(function ($date) use ($maintenanceByDate) {
            return [$date => $maintenanceByDate->get($date, collect())];
        });

        $backlogEventsMap = collect($dates)->mapWithKeys(function ($date) use ($backlogsByDate) {
            return [$date => $backlogsByDate->get($date, collect())];
        });

        // Untuk grid kalender, butuh info bulan & tahun
        return view('calendar.index', [
            'events' => $events,
            'month' => $month,
            'year' => $year,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
            'maintenanceEvents' => $maintenanceEventsMap,
            'backlogEvents' => $backlogEventsMap,
        ]);
    }
}
