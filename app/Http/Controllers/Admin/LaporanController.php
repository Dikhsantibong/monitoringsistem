<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest; // Tambahkan ini untuk mengimport model ServiceRequest
use App\Models\WorkOrder; // Tambahkan ini untuk mengimport model WorkOrder

class LaporanController extends Controller
{
    public function srWo()
    {
        // Logika untuk menampilkan laporan SR/WO
        $serviceRequests = ServiceRequest::all(); // Tambahkan ini untuk menginisialisasi variabel $serviceRequests
        $workOrders = WorkOrder::all(); // Tambahkan ini untuk menginisialisasi variabel $workOrders
        return view('admin.laporan.sr_wo', compact('serviceRequests', 'workOrders'));
    }

    // Tambahkan metode lain sesuai kebutuhan
}
