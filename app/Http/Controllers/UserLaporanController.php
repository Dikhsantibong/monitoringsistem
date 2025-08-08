<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserLaporanController extends Controller
{
    public function index()
    {
        // Ambil data Service Request dan Work Order untuk user
        $serviceRequests = \App\Models\ServiceRequest::with(['powerPlant:id,name'])
            ->select(['id', 'description', 'status', 'created_at', 'downtime', 'tipe_sr', 'priority', 'power_plant_id', 'unit_source'])
            ->orderBy('created_at', 'desc')
            ->get();

        $workOrders = \App\Models\WorkOrder::with(['powerPlant:id,name'])
            ->select(['id', 'description', 'kendala', 'tindak_lanjut', 'document_path', 'status', 'created_at', 'priority', 'type', 'labor', 'schedule_start', 'schedule_finish', 'power_plant_id', 'unit_source'])
            ->orderBy('created_at', 'desc')
            ->get();

        $woBacklogs = \App\Models\WoBacklog::with(['powerPlant:id,name'])
            ->select(['id', 'no_wo', 'deskripsi', 'type_wo', 'priority', 'schedule_start', 'schedule_finish', 'tanggal_backlog', 'document_path', 'kendala', 'tindak_lanjut', 'keterangan', 'status', 'power_plant_id', 'unit_source'])
            ->orderBy('created_at', 'desc')
            ->get();

        $powerPlants = \App\Models\PowerPlant::select('id', 'name', 'unit_source')->orderBy('name')->get();
        return view('user.laporan.sr_wo', compact('serviceRequests', 'workOrders', 'woBacklogs', 'powerPlants'));
    }
}
