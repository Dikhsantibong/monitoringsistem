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

        // Get service requests and work orders
        $serviceRequestsQuery = ServiceRequest::select('id', 'description', 'created_at', 'status');
        $workOrdersQuery = WorkOrder::select('id', 'description', 'created_at', 'type', 'status');

        if ($startDate) {
            $serviceRequestsQuery->whereDate('created_at', '>=', $startDate);
            $workOrdersQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $serviceRequestsQuery->whereDate('created_at', '<=', $endDate);
            $workOrdersQuery->whereDate('created_at', '<=', $endDate);
        }

        $serviceRequests = $serviceRequestsQuery->get()
            ->map(function ($sr) {
                return [
                    'id' => $sr->id,
                    'type' => 'Service Request',
                    'description' => $sr->description,
                    'date' => Carbon::parse($sr->created_at)->format('Y-m-d'),
                    'status' => $sr->status,
                ];
            });

        $workOrders = $workOrdersQuery->get()
            ->map(function ($wo) {
                return [
                    'id' => $wo->id,
                    'type' => 'Work Order - ' . $wo->type,
                    'description' => $wo->description,
                    'date' => Carbon::parse($wo->created_at)->format('Y-m-d'),
                    'status' => $wo->status ?? 'Pending',
                ];
            });

        // Combine and group by date
        $events = $serviceRequests->concat($workOrders)
            ->groupBy('date')
            ->map(function ($items) {
                return $items->sortBy('type');
            })
            ->sortKeys();

        return view('calendar.index', compact('events', 'startDate', 'endDate'));
    }
}
