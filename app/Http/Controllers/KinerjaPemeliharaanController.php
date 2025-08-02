<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;

class KinerjaPemeliharaanController extends Controller
{
    public function index()
    {
        $pmClosed = WorkOrder::where('type', 'PM')->where('status', 'Closed')->get();
        $cmClosed = WorkOrder::where('type', 'CM')->where('status', 'Closed')->get();

        $pmCount = $pmClosed->count();
        $cmCount = $cmClosed->count();

        return view('kinerja.index', compact('pmClosed', 'cmClosed', 'pmCount', 'cmCount'));
    }
}