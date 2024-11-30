<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function srWo()
    {
        // Logika untuk menampilkan laporan SR/WO
        return view('admin.laporan.sr_wo');
    }

    // Tambahkan metode lain sesuai kebutuhan
}
