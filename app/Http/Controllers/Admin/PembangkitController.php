<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PembangkitController extends Controller
{
    public function ready()
    {
        // Logika untuk menampilkan kesiapan pembangkit
        return view('admin.pembangkit.ready');
    }

    // Tambahkan metode lain sesuai kebutuhan
}
