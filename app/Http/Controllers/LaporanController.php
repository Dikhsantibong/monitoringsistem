<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\WorkOrder;

class LaporanController extends Controller
{
    public function updateSRStatus(Request $request, $id)
    {
        $sr = ServiceRequest::findOrFail($id);
        $sr->status = $request->status;
        $sr->save();

        return response()->json(['success' => true]);
    }

    public function updateWOStatus(Request $request, $id)
    {
        $wo = WorkOrder::findOrFail($id);
        $wo->status = $request->status;
        $wo->save();

        return response()->json(['success' => true]);
    }
}