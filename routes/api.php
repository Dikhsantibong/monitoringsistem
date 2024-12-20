<?php

use App\Models\MachineOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/machine-operations', function (Request $request) {
    $query = MachineOperation::with(['machine.powerPlant'])
        ->orderBy('recorded_at', 'desc')
        ->limit(50);

    if ($request->machine_id) {
        $query->where('machine_id', $request->machine_id);
    }

    return $query->get();
}); 