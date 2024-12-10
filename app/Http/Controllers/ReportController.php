<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function srWoReport()
    {
        $serviceRequests = ServiceRequest::all();
        $workOrders = WorkOrder::all();

        return view('admin.laporan.sr_wo', compact('serviceRequests', 'workOrders'));
    }
} 