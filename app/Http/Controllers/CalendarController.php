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

        // Group by date
        $events = $workOrders
            ->groupBy('date')
            ->map(function ($items) {
                return $items->sortBy('type');
            })
            ->sortKeysDesc();

        return view('calendar.index', compact('events', 'startDate', 'endDate'));
    }
}
