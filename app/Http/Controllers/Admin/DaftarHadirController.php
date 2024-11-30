<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DaftarHadirController extends Controller
{
    public function index()
    {
        // Logika untuk menampilkan daftar hadir
        return view('admin.daftar_hadir.index');
    }

    // Tambahkan metode lain sesuai kebutuhan
}
